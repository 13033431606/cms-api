<?php
namespace app\index\controller;

use think\View;

class Thy extends Base
{
    public function __construct()
    {
        $this->need_assign();
    }

    //获取文件夹大小
    public function dir_size($dir){
        $dh = opendir($dir);             //打开目录，返回一个目录流
        $size = 0;      //初始大小为0
        while(false !== ($file = @readdir($dh))){     //循环读取目录下的文件
            if($file!='.' and $file!='..'){
                $path = $dir.'/'.$file;     //设置目录，用于含有子目录的情况
                if(is_dir($path)){
                    $size += $this->dir_size($path);  //递归调用，计算目录大小
                }elseif(is_file($path)){
                    $size += filesize($path);   //计算文件大小
                }
            }
        }
        closedir($dh);             //关闭目录流
        return $size;               //返回大小
    }

    //清除缓存方法
    public function temp_clear(){
        $result=$this->dir_del(ROOT_PATH . 'public/uploads/temp/');

        if($result){
            return json("ok");
        }
        else{
            return json("no");
        }
    }

    //文件删除方法
    public function dir_del($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        $this->dir_del($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
        }
    }

    //默认执行方法
    public function need_assign(){
        $view=new View();
        //获取图片缓存的大小:mb
        $dirSize=$this->dir_size(ROOT_PATH . 'public/uploads/temp')/1024/1024;
        //向上取整,两位小数
        $dirSize=round($dirSize,2);
        $view->share("size",$dirSize);
    }


    //转移图片方法
    public function moveFile($fileSaveName){
        if($fileSaveName){
            $file=ROOT_PATH . 'public/uploads/temp/'.$fileSaveName; //旧目录
            $newFile=ROOT_PATH . 'public/uploads/'.$fileSaveName; //新目录
            $dir = iconv("UTF-8", "GBK", ROOT_PATH . 'public/uploads/'.substr($fileSaveName,0,8));
            if (!file_exists($dir)) mkdir ($dir,0777,true);
            copy($file,$newFile); //拷贝到新目录
        }
    }

    //添加文章内容图片路径
    function add_imgs_url($content){
        //匹配并删除图片
        $img_path = "/<img.*src=\"([^\"]+)\"/U";

        $matches = array();
        preg_match_all($img_path, $content, $matches);
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        foreach($matches[1] as $img_url){
            //strpos(a,b) 匹配a字符串中是否包含b字符串 包含返回true
            if(strpos($img_url, 'https://')===false){
                $filepath = $host.$img_url;
                $content=str_replace($img_url,$filepath,$content);
            }
        }
        return $content;

    }

}
