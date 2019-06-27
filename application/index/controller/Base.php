<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use app\index\model\ArticleModel;
use think\View;
use think\Db;


class Base
{

    /**
     * @api {post} /base/upload 文件上传
     * @apiName file_upload
     * @apiGroup Base
     * @apiParam {String} file 上传的文件
     *
     * @apiSuccess (成功返回) {String} data 图片上传后的保存名称:"20190625\b75a7da75f420c85529729ab907720e5.jpg"
     * @apiSuccess (成功返回) {String} message 上传信息
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} location 同data,供富文本编辑器使用
     */
    public function upload(){
        $file = request()->file('file');
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
                $msg['code']="200";
                $msg['message']="上传成功";
                $msg['data']=$info->getSaveName();
                $msg['location']=$msg['data'];
                return json($msg);
            }else{
                // 上传失败获取错误信息
                $msg['code']="502";
                $msg['message']=$file->getError();
                $msg['data']="none";
                return json($msg);
            }
        }
    }

    /**
     * @api {POST} /base/del_file  文件删除(外部)
     * @apiName del_file
     * @apiGroup Base
     *
     * @apiParam {String} file_save_name 文件保存名:"20190626\6159e602f3befeccd8f83ebcd74702b3.jpg"
     */
    public function del_file(){
        $file_name=$_POST['file_name'];
        $file = ROOT_PATH . 'public/uploads/temp/' . $file_name;
        if (file_exists($file)) {
            unlink ($file);
        }

        $file2 = ROOT_PATH . 'public/uploads/' . $file_name;
        if (file_exists($file2)) {
            unlink ($file2);
        }
    }

    /**
     * @api {POST} /base/del_file  文件删除(内部)
     * @apiName del_file_private
     * @apiGroup Base
     *
     * @apiParam {String} file_name 文件保存名:"20190626\6159e602f3befeccd8f83ebcd74702b3.jpg"
     */
    public function del_file_private($file_name){
        $file = ROOT_PATH . 'public/uploads/temp/' . $file_name;
        if (file_exists($file)) {
            unlink ($file);
        }

        $file2 = ROOT_PATH . 'public/uploads/' . $file_name;
        if (file_exists($file2)) {
            unlink ($file2);
        }
    }

    /**
     * @api {get} /base/type_tree 获取树状结构
     * @apiName type_tree
     * @apiGroup Base
     * @apiParam {Number} id 树状结构最外层的id
     *
     * @apiSuccess (成功返回) {String} data 树状结构
     * @apiSuccess (成功返回) {String} message 信息
     * @apiSuccess (成功返回) {Number} code 状态标识码
     *
     * @apiSuccessExample 样本数据:
     *{
    "data": [
    {
    "id": 17,
    "parent": "13",
    "name": "后端",
    "son": [
    {
    "id": 18,
    "parent": "17",
    "name": "php"
    },
    {
    "id": 21,
    "parent": "17",
    "name": "mysql"
    }
    ]
    }
    ],
    "message": "返回成功",
    "code": 200
    }
     *
     *
     */
    public function type_tree(){
        $id=$_GET["id"];

        $types=Db::query("select id,pid,title from thy_type where code like '%".$id."%' order by sort desc,id desc");

        //新数组以原数组的id为索引,进行遍历
        foreach ($types as $value){
            $items[$value['id']]=$value;
        }

        foreach ($items as $key=>$value){
            //判断当前的父元素为索引的元素定义时,此元素为子元素
            if(isset($items[$value['pid']])){
                //当前元素就是父元素的子集
                //$items[$value['parent']]['son'][],$tree[],$items[$key]
                //三个变量用&绑在了一起,指向了同一个地址
                //这里不用$value的原因是每个$value都有不同地址,最终三者的值为最后一个元素的值
                $items[$value['pid']]['son'][] = &$items[$key];
            }else{
                //如果已定义的话,说明是父级元素,直接push到tree
                $tree[] = &$items[$key];
            }
        }


        $data["data"]=$tree;
        $data["message"]="返回成功";
        $data["code"]=200;

        return json($data);
    }


    /**
     * @api {Method} /base/move_file  文件移动
     * @apiName move_file
     * @apiGroup Base
     *
     * @apiParam {String} file_save_name 文件保存名:"20190626\6159e602f3befeccd8f83ebcd74702b3.jpg"
     */
    public function move_file($file_save_name){
        if($file_save_name){
            $file=ROOT_PATH . 'public/uploads/temp/'.$file_save_name; //旧目录
            $new_file=ROOT_PATH . 'public/uploads/'.$file_save_name; //新目录
            $dir = iconv("UTF-8", "GBK", ROOT_PATH . 'public/uploads/'.substr($file_save_name,0,8));
            if (!file_exists($dir)) mkdir ($dir,0777,true);
            copy($file,$new_file); //拷贝到新目录
        }
    }


    /**
     * @api {METHOD} /base/del_imgs  文章图片删除(内部),删除img和content里的图片
     * @apiName del_imgs_private
     * @apiGroup Base
     *
     * @apiParam {Array} aritlle_arr select出来的文章数组,或者通过其他拼接也可以
     */
    public function del_imgs($aritlle_arr){
        //遍历content数据,删除图片文件
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';//正则获取所有图片路径
        foreach ($aritlle_arr as $val){
            //删除img的图片
            if($val['img'] != ''){
                $img_list=explode(",",$val['img']);
                foreach ($img_list as $value){
                    $this->del_file_private($value);
                }
            }

            //删除content的图片
            if($val['content'] != ''){
                preg_match_all($preg,$val['content'],$matches);//array[0]为img集,array[1]为src集
                //遍历所有src路径进行删除图片
                foreach ($matches[1] as $value){
                    //首先判断是否有匹配的文件
                    //是删除现有的id,所以路径不可能有temp,所以直接取uploads
                    $filename=substr(strrchr($value,"uploads"),8); //数组凭接获取:20190626\6159e602f3befeccd8f83ebcd74702b3.jpg
                    $this->del_file_private($filename);
                }
            }

        }
    }


    /**
     * @api {METHOD} /base/get_type_id 获取子类别ID和自身
     * @apiName get_type_id
     * @apiGroup Base
     *
     * @apiParam {Number} id 传入需要获取的id
     *
     * @apiSuccess (返回成功) {String} id字符串"15,16,12"
     */
    function get_type_id($id) {
        //返回$id的子类别ID和自身
        $pid= Db::query("select id from thy_type where code like '%,".$id.",%'");
        $nums = '';
        foreach ($pid as $v) {
            $nums .= $v['id'] . ',';
        }
        return rtrim($nums, ',');
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
