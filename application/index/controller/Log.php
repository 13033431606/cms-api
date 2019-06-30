<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;


class Log extends Base
{
    /**
     * @api {get} /log/index
     *
     * @apiName log_index
     * @apiGroup Log
     *
     * @apiParam {Number} page 页数
     * @apiParam {Number} num 每页文章数
     *
     * @apiSuccess (成功返回) {Array} data 文章数组
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message  状态信息
     * @apiSuccess (成功返回) {Number} count 文章数量
     *
     */
    public function index(){

        $page= $_GET['page'];//页码
        $num= $_GET['num'];//每页个数
        $total= ($page-1)*$num;

        $data= Db::query("SELECT * FROM thy_log  ORDER BY id DESC LIMIT "."$total,$num");
        $msg["count"]= count(db("log")->select());

        foreach ($data as $key=>$val){
            $username=db("user")->where('id ='.$val['user_id'])->value('username');
            $data[$key]['username']=$username;
        }
        $msg['data']=$data;
        $msg['code']=200;
        $msg['message']='添加成功';


        return json($msg);
    }

}
