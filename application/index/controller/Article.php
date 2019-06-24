<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");
use think\View;
use think\Db;
use app\index\model\ArticleModel;
use app\index\model\TypeModel;
use think\Session;


class Article extends Thy
{

    /**
     * @api {get} /article/index 获取文章数据
     * @apiName article_get_data
     * @apiGroup Article
     *
     * @apiParam {Number} id 用来获取文章的父id,0=获取所有,可传多个值(1,2,3),以逗号分隔
     * @apiParam {Number} page 页数
     * @apiParam {Number} num 每页文章数
     *
     * @apiSuccess (成功返回) {Array} data 文章数组
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message  状态信息
     * @apiSuccess (成功返回) {Number} count 文章数量
     */
    public function index()
    {
        $id= $_GET['id'];
        $page= $_GET['page'];
        $num= $_GET['num'];
        $total= ($page-1)*$num;

        if($id == 0){
            $data["data"]= db("article")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();

        }
        else{
            $data["data"]= db("article")->where("id in ($id)")->order(['order'=>'desc','id'=>'desc'])->limit("$total,$num")->select();
        }

        foreach ($data["data"] as $key=>$value){
            $pname= db("type")->where("id =".$value['pid'])->value("name");
            $data["data"][$key]["pname"]=$pname;
        }

        $data["count"]= count(db("article")->select());
        $data['code']= "200";
        $data['message']= "返回成功";

        return json($data);

    }


    public function getData(){
        //页数
        $page=$_REQUEST['page'];
        //每页数
        $limit=$_REQUEST['limit'];
        //总数
        $total=$page*$limit-$limit;

        //获取id集
//        $base=new Base();
//        $ids=$base->getTypeID(13);

        //获取文章信息
        $data['data'] = db('article')->order(['order'=>'desc','id'=>'desc'])->limit("$total,$limit")->select();
        foreach ($data['data'] as $key => $value) {
            $pids=explode(",",$value['pid']);
            $data['data'][$key]['cat']='';
            if(count($pids)>1){
                foreach ($pids as $key2=>$value2){
                    $data['data'][$key]['cat'].=db('type')->where("`id`= $value2")->value('name')." , ";
                }
            }
            else{
                $data['data'][$key]['cat'] = db('type')->where("`id`= $pids[0] ")->value('name');
            }

        }

        $data['code'] = '0';
        $data['msg'] = "文章列表数据";
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['count'] = db('article')->order(['order'=>'desc','id'=>'desc'])->count();

        return json($data);
    }


    //添加文章页面
    public function add()
    {
        $view=new View();

        $base=new Base();
        $tree=$base->getTree(1);
        $view->tree=$base->genOption($tree);

        return $view->fetch();
    }

    //添加文章方法
    public function addArticle(){
        $article = new ArticleModel;
        //获取ajax传输的表单数据
        $data=$_POST;


        if($data['pid'] == ''){
            return false;
            exit;
        }

        if(isset($data['img1'])){
            $this->moveFile($data['img1']);
        }
        if(isset($data['img2'])){
            $this->moveFile($data['img2']);
        }
        if(isset($data['img3'])){
            $this->moveFile($data['img3']);
        }
        $article->data($data);
        // 过滤post数组中的非数据表字段数据
        $article->save();
    }

    //修改文章页面
    public function edit(){
        if(isset($_GET["id"])){
            $id=$_GET["id"];
            $article=ArticleModel::get($id);

//            $article["pname"]=TypeModel::where('id',$article['pid'])->value('name');


            $pids=explode(",",$article['pid']);
            $article['pname']='';
            if(count($pids)>1){
                foreach ($pids as $key=>$value){
                    $article['pname'].=db('type')->where("`id`=$value")->value('name')." , ";
                }
            }
            else{
                $article['pname'] = db('type')->where("`id`=$pids[0]")->value('name');
            }


            $view = new View();

            $base=new Base();

            $tree=$base->getTree(1);

            $view->tree=$base->genOption($tree);

            $view->assign('list',$article);

            return $view->fetch();
        }
    }


    public function editArticle(){
        $id=$_GET["id"];

        $data=$_POST;

        $ori=ArticleModel::get($id);

        if(isset($data['img1'])){
            if ($ori["img1"]!=$data['img1']){ //判断图片有无更改
                $this->moveFile($data['img1']);
            }
        }
        if(isset($data['img2'])){
            if ($ori["img2"]!=$data['img2']){ //判断图片有无更改
                $this->moveFile($data['img2']);
            }
        }
        if(isset($data['img3'])){
            if ($ori["img3"]!=$data['img3']){ //判断图片有无更改
                $this->moveFile($data['img3']);
            }
        }

        $article = new ArticleModel;
        $article->allowField(true)->save($data,['id' => $id]);
    }

    //删除信息
    public function delArticle($id_single=''){
        if($id_single!=''){
            $id=$id_single;
        }
        else{
            $id=$_REQUEST['id'];
        }
        $result=db('article')->where('id',$id)->find();
        $base=new Base();
        if($result['img1']!=''){
            $base->delete_img($result['img1']);
        }
        if($result['img2']!=''){
            $base->delete_img($result['img2']);
        }
        if($result['img3']!=''){
            $base->delete_img($result['img3']);
        }
        if($result['content']!=''){
            $base->delete_imgs($result['content']);
        }
        $result=db('article')->where('id',$id)->delete();
        return $result;
    }

    //批量删除
    public function delAllArticle(){
        $id=$_REQUEST['id'];
        $idArr=explode(",",$id);//将传过来的ids字符串转换为数组
        foreach ($idArr as $key2=>$value2){
            $this->delArticle($value2);
        }
        return "ok";
    }

}
