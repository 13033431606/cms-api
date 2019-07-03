<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;


class Api extends Base
{
    //毒鸡汤
    public function words(){
        $time=$_GET['time'];
        $UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://www.dutangapp.cn/u/toxic?date=".$time);
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        curl_exec($curl);
    }

    /**
     * @api {get} /article/index 获取文章数据
     * @apiName article_index
     * @apiGroup Article
     *
     * @apiParam {String} id 用来获取文章的父id,0=获取所有,可传多个值(1,2,3),以逗号分隔
     * @apiParam {Number} page 页数
     * @apiParam {Number} num 每页文章数
     *
     * @apiSuccess (成功返回) {Array} data 文章数组
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message  状态信息
     * @apiSuccess (成功返回) {Number} count 文章数量
     *
     *
     */
    public function article_list()
    {
        $id= parent::get_type_id($_GET['id']);
        $page= $_GET['page'];//页码
        $num= $_GET['num'];//每页个数
        $total= ($page-1)*$num;

        if($id == 0){
            //$data["data"]= db("article")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();
            //tp自带函数助手无目标取值,所以用了原生写法;
            $data["data"]= Db::query("SELECT id,sort,title,pid,img,description,time FROM thy_article ORDER BY sort DESC, id DESC LIMIT "."$total,$num");
            $data["count"]= count(db("article")->select());
        }
        else{
            //$data["data"]= db("article")->where("pid in ($id)")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();
            //原因同上
            $data["data"]= Db::query("SELECT id,sort,title,pid,img,description,time FROM thy_article where pid in (".$id.") ORDER BY sort DESC, id DESC LIMIT "."$total,$num");
            $data["count"]= count(db("article")->where("pid in ($id)")->select());
        }

        //基于pid查询分类名
        foreach ($data["data"] as $key=>$value){
            $pname= db("type")->where("id =".$value['pid'])->value("title");
            $data["data"][$key]["pname"]=$pname;
        }


        $data['code']= "200";
        $data['message']= "返回成功";

        return json($data);
    }

    //获取文章数量
    public function article_count(){
        $data['code']="200";//api标志码
        $data['message']="链接成功";//返回信息
        $data['data']=db("article")->order('sort desc,id desc')->select();
        $data['data']=count($data['data']);
        return json($data);
    }

    //获取文章数量
    public function todo_count(){
        $data['code']="200";//api标志码
        $data['message']="链接成功";//返回信息
        $data['data']=db("todo")->where("state = 'todo'")->order('id desc')->select();
        $data['data']=count($data['data']);
        return json($data);
    }

    //搜索
    public function article_search(){
        $keywords=$_GET["keywords"];
        $data['code']="200";//api标志码
        $data['message']="链接成功";//返回信息
        $data['data']=db("article")->where("title like '%$keywords%'")->order("sort desc,id desc")->select();
        $data['count']=count($data['data']);
        return json($data);
    }
}
