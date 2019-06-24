<?php
namespace app\index\controller;

use app\index\model\ArticleModel;
use think\View;
use think\Db;
use think\Session;

class Base
{

    public function getTypeID($id) {
        if (!$id) return false;

        if ($id > 0 && $id < 4) {
            $pid = Db::query("select * from thy_type where code like '$id,%'");

        } else {
            $pid = Db::query("select * from thy_type where code like '%,$id,%'");
        }

        //文章 or 产品 or 人才 的PID
        $nums = '';
        foreach ($pid as $v) {
            $nums .= $v['id'] . ',';
        }

        return rtrim($nums, ',');
    }




    //获取分类集(引用方法)
    public function getTree($id=1){
        if (!$id) return false;

        if ($id > 0 && $id < 4) {
            $ids= Db::query("select * from thy_type where code like '$id,%'");
        } else {
            $ids = Db::query("select * from thy_type where code like '%,$id,%'");
        }

        //第一步 构造数据
        //深拷贝
        $items = array();
        foreach($ids as $value){
            $items[$value['id']] = $value;
        }

        //第二部 遍历数据 生成树状结构
        //&为变量的引用
        foreach($items as $key => $value){
            if(isset($items[$value['parent']])){
                $items[$value['parent']]['son'][] = &$items[$key];
            }else{
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

    //生成选择下拉框
    public function genOption($arr,$step=0,$style="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"){
        //static的定义语句只会被执行一次，但是它的值会被函数记住，直到程序终止
        static $str ='';
        $symbol='';
        for ($i=0; $i<$step; $i++) {
            $symbol=$symbol.$style;
        }
        foreach($arr as $key=>$value){
            if(isset($value['son'])){
                $str=$str."<div class='tree_son' id='{$value["id"]}' parent='{$value["parent"]}' code='{$value["code"]}'>{$symbol}<div class='tree_check_box'></div><span>{$value["name"]}</span></div>";
                $this->genOption($value['son'],$step+1);
            }
            else{
                $str=$str."<div class='tree_son' id='{$value["id"]}' parent='{$value["parent"]}' code='{$value["code"]}'>{$symbol}<div class='tree_check_box'></div><span>{$value["name"]}</span></div>";
            }
        }
        return $str;
    }


    //文件上传
    public function upLoads(){
        $filename=$_REQUEST['filename'];
        // 获取表单上传文件
        $file = request()->file($filename);
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public/uploads/temp');//先存至缓存文件夹,提交后转出
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename();
                $msg['code']="1";
                $msg['msg']="上传成功";
                $msg['data']['src']=$info->getSaveName();
                return json($msg);
            }else{
                // 上传失败获取错误信息
                $msg['code']="0";
                $msg['msg']=$file->getError();
                $msg['data']['src']="0";
                return json($msg);
            }
        }
    }


    //删除文章标题图片
    function delete_img($src){
        @unlink(ROOT_PATH."public/uploads/".$src);
    }

    //删除文章内容图片（也就是删除编辑器上传的图片
    function delete_imgs($content){
        //匹配并删除图片
        $img_path = "/<img.*src=\"([^\"]+)\"/U";

        $matches = array();
        preg_match_all($img_path, $content, $matches);

        foreach($matches[1] as $img_url){
            //strpos(a,b) 匹配a字符串中是否包含b字符串 包含返回true
            if(strpos($img_url, 'emoticons')===false){
                $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
                $filepath = str_replace($host,'',$img_url);
                if($filepath == $img_url) $filepath = substr($img_url, 1);
                @unlink(ROOT_PATH.$filepath);
                $filedir  = dirname(ROOT_PATH.$filepath);
                $files = scandir($filedir);
                if(count($files)<=2)@rmdir($filedir);//如果是./和../,直接删除文件夹
            }
        }
        unset($matches);
    }

}
