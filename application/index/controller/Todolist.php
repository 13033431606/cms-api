<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

class Todolist extends Base
{
    /**
     * @api {get} /todolist/index
     *
     * @apiName todolist_index
     * @apiGroup Todolist
     *
     *
     */
    public function index(){
        $msg['data']=db("todo")->where("state = 'todo'")->order('id desc')->select();
        $msg['code']=200;
        $msg['message']='添加成功';
        return json($msg);
    }

    public function add(){
        $data=$_POST;
        $data['time']=date('Y-m-d', time());
        $msg['update_id']=db("todo")->insertGetId($data);
        $msg['code']=200;
        $msg['message']='添加成功';
        parent::log("添加了Todo:".$data['title'],$data['user_id'],$msg['update_id'],"添加");
        return json($msg);
    }

    /**
     * @api {get} /todolist/index
     *
     * @apiName todolist_edit
     * @apiGroup Todolist
     */
    public function edit(){
        $data=$_POST;
        if($data['type']=="finish"){
            db("todo")->where("id =".$data['id'])->update(['state' => $data['type']]);
        }
        elseif ($data['type']=="remove"){
            db("todo")->where("id =".$data['id'])->update(['state' => $data['type']]);
        }
        else{
            return false;
        }
        $msg['code']=200;
        $msg['message']='修改成功';
        return json($msg);
    }

    public function data(){
        $data=db("note")->order("id desc,create_time desc")->select();
        return json($data);

    }

}
