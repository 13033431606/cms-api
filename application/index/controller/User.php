<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;
use think\Session;

class User extends Base
{


    public function user_login(){
        $data=$_POST['data'];

        $data['username']=strip_tags($data['username']);
        $data['password']=strip_tags($data['password']);

        $user_data=db("user")->where("username = '". $data['username']."'")->find();

        if($user_data){
            if($user_data['password'] == $data['password']){
                $msg["code"]=200;
                $msg["message"]="登录成功";
                $msg["data"]["token"]=md5($user_data['id'].time().$data['password']);
                $msg["data"]["id"]=$user_data["id"];
                $msg["data"]["username"]=$user_data["username"];
                $msg["code"]=200;

                //创建用户的登录session
                $user_session['id']=$user_data["id"];
                $user_session['token']=$msg["data"]["token"];
                session('user', $user_session);
            }
            else{
                $msg["code"]=502;
                $msg["message"]="密码错误";
            }
        }
        else{
            $msg["code"]=502;
            $msg["message"]="用户名不存在";
        }

        return json($msg);

    }
}
