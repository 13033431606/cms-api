<?php
namespace app\index\controller;


use think\View;

class Note extends Thy
{
    public function index()
    {
       $view=new View();
       $data=db("note")->order("id desc,create_time desc")->select();
        $view->assign('lists',$data);
        return $view->fetch();

    }

    public function add(){
        $data=$_GET;
//        $data['create_time']=date("Y-m-d h:i:s",time());
        db("note")->insert($data);
        return json($data);
    }

    public function data(){
        $data=db("note")->order("id desc,create_time desc")->select();
        return json($data);

    }

    public function edit(){
        if($_GET['type']=="done") db("note")->where("id = {$_GET['id']}")->update(["state"=>2,"edit_time"=>$_GET["edit_time"]]);
        if($_GET['type']=="close") db("note")->where("id = {$_GET['id']}")->update(["state"=>3,"edit_time"=>$_GET["edit_time"]]);
        if($_GET['type']=="edit") db("note")->where("id = {$_GET['id']}")->update(["content"=>$_GET["edit_content"]]);
    }
}
