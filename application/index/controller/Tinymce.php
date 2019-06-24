<?php
namespace app\index\controller;

use think\View;
use think\Session;
class Tinymce extends Thy
{
    public function index()
    {

//        $object["location"]="5b90e1c28fe84.jpg";
//
//        return json($object);

    }

    public function upload(){
        // 获取表单上传文件
        $file = request()->file('file');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public/uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename();
                $data['location']=$info->getSaveName();
                return json($data);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

    }

    public function imgTools(){
        $validMimeTypes = array("image/gif", "image/jpeg", "image/png");

        if (!isset($_GET["url"]) || !trim($_GET["url"])) {
            header("HTTP/1.0 500 Url parameter missing or empty.");
            return;
        }

        $scheme = parse_url($_GET["url"], PHP_URL_SCHEME);
        if ($scheme === false || in_array($scheme, array("http", "https")) === false) {
            header("HTTP/1.0 500 Invalid protocol.");
            return;
        }

        $content = file_get_contents($_GET["url"]);
        $info = getimagesizefromstring($content);

        if ($info === false || in_array($info["mime"], $validMimeTypes) === false) {
            header("HTTP/1.0 500 Url doesn't seem to be a valid image.");
            return;
        }

        header('Content-Type:' . $info["mime"]);
        echo $content;
    }

}
