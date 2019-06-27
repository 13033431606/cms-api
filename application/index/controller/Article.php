<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;

class Article extends Base
{

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
    public function index()
    {
        $id= $_GET['id'];
        $page= $_GET['page'];//页码
        $num= $_GET['num'];//每页个数
        $total= ($page-1)*$num;

        if($id == 0){
            //$data["data"]= db("article")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();
            //tp自带函数助手无目标取值,所以用了原生写法;
            $data["data"]= Db::query("SELECT id,sort,title,pid,img,time FROM thy_article ORDER BY sort DESC, id DESC LIMIT "."$total,$num");
        }
        else{
            //$data["data"]= db("article")->where("pid in ($id)")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();
            //原因同上
            $data["data"]= Db::query("SELECT id,sort,title,pid,img,time FROM thy_article where pid in (".$id.") ORDER BY sort DESC, id DESC LIMIT "."$total,$num");
        }

        //基于pid查询分类名
        foreach ($data["data"] as $key=>$value){
            $pname= db("type")->where("id =".$value['pid'])->value("title");
            $data["data"][$key]["pname"]=$pname;
        }

        $data["count"]= count(db("article")->select());
        $data['code']= "200";
        $data['message']= "返回成功";

        return json($data);
    }


    /**
     * @api {post} /article/add 添加|更新文章的方法
     * @apiName article_add
     * @apiGroup Article
     *
     * @apiParam {String{0..200}} title 文章标题
     * @apiParam {String{0..300}} img 图片列表用","隔开
     * @apiParam {Number} pid 父id
     * @apiParam {String} time 时间,yyyy-MM-dd格式,2019-06-26
     * @apiParam {String{0..150}} keywords 关键词
     * @apiParam {String{0..350}} keywords 描述
     * @apiParam {String="text"} content 文章内容
     * @apiParam {Number} click 点击量
     * @apiParam {Number{0-9999}} sort 排序(desc)
     * @apiParam {String} state 状态(on:off),显示:隐藏
     *
     * @apiSuccess (成功添加) {Number} insert_id 自增id
     * @apiSuccess (成功添加) {Number} code 状态标识码
     * @apiSuccess (成功添加) {String} message 状态信息
     *
     * @apiSuccess (成功更新) {Number} update_id 更新id
     * @apiSuccess (成功更新) {Number} code 状态标识码
     * @apiSuccess (成功更新) {String} message 状态信息
     */
    public function add(){
        //获取整个传过来的表单文件
        //此时相关文件图片内容已经入了缓存
        $data=$_POST;

        //处理img
        //1.遍历图片集,转入上传区
        $img_list=explode(",",$_POST['img']);
        foreach ($img_list as $value){
            //首先判断缓存区是否存在此文件
            $file = ROOT_PATH . 'public/uploads/temp/' . $value;
            if (file_exists($file)) {
                parent::move_file($value);
            }
        }

        //处理content
        //1.遍历content中的img,放入上传区,修改src路径
        $content=$data['content'];

        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';//正则获取所有图片路径
        preg_match_all($preg,$content,$matches);//array[0]为img集,array[1]为src集
        //遍历所有src路径进行转移图片
        foreach ($matches[1] as $value){
            //首先判断是否有匹配的文件
            $filename=substr(strrchr($value,"temp"),5); //数组凭接获取:20190626\6159e602f3befeccd8f83ebcd74702b3.jpg
            if($filename)
            //接着判断缓存区是否已有有此文件
            $file= ROOT_PATH . 'public/uploads/temp/'. $filename;
            if (file_exists($file)) {
                parent::move_file($filename);
            }
        }

        //2.更改所有图片文件的src
        $temp_path="http://cms-api.tt/public/uploads/temp/";
        $formal_path="http://cms-api.tt/public/uploads/";
        $content2=str_replace($temp_path,$formal_path,$content);

        $data["content"]=$content2;

        //判断是更新还是添加,id值不为0就是更新
        if($data['id'] != 0){
            //更新
            $msg['update_id']=$data['id'];
            unset($data->id);
            db('article')->where('id ='.$msg['update_id'])->update($data);
            $msg['message']= "更新成功";
        }else{
            //添加
            unset($data->id);
            $msg['insert_id']=db('article')->insertGetId($data);
            $msg['message']= "添加成功";
        }
        $msg['code']= "200";


        return json($msg);

    }


    /**
     * @api {get} /article/del 删除文章的方法
     * @apiName article_del
     * @apiGroup Article
     *
     * @apiParam {String} id 需删除的文章id,可传多个值(1,2,3),以逗号分隔
     *
     * @apiSuccess (成功返回) {Number} count 删除文章的个数
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message 状态信息
     */
    public function del(){
        $id=$_GET['id'];

        $temp=db('article')->where("id in ($id)")->select();//返回删除的文章列表

        parent::del_imgs($temp);

        $data['count']=db('article')->where("id in ($id)")->delete();//返回删除的数目
        $data['code']="200";
        $data['message']="删除成功";


    }


    /**
     * @api {get} /article/get_single_article 获取单个文章的内容
     * @apiName article_single
     * @apiGroup Article
     *
     * @apiParam {Number} id 单个文章id
     *
     * @apiSuccess (成功返回) {Array} data 文章内容
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message 状态信息
     *
     */
    public function get_single_article(){
        $id=$_GET['id'];

        $data['data']=db("article")->where("id = $id")->select()[0];
        $data['code']="200";
        $data['message']="获取成功";

        return json($data);

    }
}
