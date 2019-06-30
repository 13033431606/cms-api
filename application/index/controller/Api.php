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
