<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;


class User extends Base
{
    /**
     * @api {post} /user/user_login
     * @apiName user_login
     * @apiGroup User
     *
     * @apiParam {String} username 提交的用户名
     * @apiParam {String} password 提交的密码
     *
     * @apiSuccess (成功添加) {Array}  data 包含用户的id,name和token
     * @apiSuccess (成功添加) {Number} code 状态标识码
     * @apiSuccess (成功添加) {String} message 状态信息
     */
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

                //创建用户的登录cache
                $user_session['id']=$user_data["id"];
                $user_session['token']=$msg["data"]["token"];

                //设置登录缓存,周期一个礼拜
                cache('user'.$user_session['id'], $user_session, 3600*24*7);

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
