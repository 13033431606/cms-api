<?php
namespace app\index\controller;


use think\View;
use think\Db;
use app\index\model\ArticleModel;
use app\index\model\TypeModel;
use think\Session;


class Category extends Thy
{
    public function index()
    {
        $view=new View();
        return $view->fetch();
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
        $data['data'] = db('type')->order(['order'=>'desc','id'=>'desc'])->limit("$total,$limit")->select();
//        foreach ($data['data'] as $key => $value) {
//            $pids=explode(",",$value['pid']);
//            $data['data'][$key]['cat']='';
//            if(count($pids)>1){
//                foreach ($pids as $key2=>$value2){
//                    $data['data'][$key]['cat'].=db('type')->where("`id`= $value2")->value('name')." , ";
//                }
//            }
//            else{
//                $data['data'][$key]['cat'] = db('type')->where("`id`= $pids[0] ")->value('name');
//            }
//
//        }

        $data['code'] = '0';
        $data['msg'] = "分类列表数据";
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['count'] = db('type')->order(['order'=>'desc','id'=>'desc'])->count();

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
        $article = new TypeModel;

        //获取ajax传输的表单数据
        $data=$_POST;


        if($data['parent'] == ''){
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

        $id=$article->id;
        $code['code']=$data['code'].$id.",";
        $article->save($code,['id' => $id]);

    }


    //修改文章页面
    public function edit(){
        if(isset($_GET["id"])){
            $id=$_GET["id"];
            $article=TypeModel::get($id);

//            $article["pname"]=TypeModel::where('id',$article['pid'])->value('name');


//            $pids=explode(",",$article['pid']);
//            $article['pname']='';
//            if(count($pids)>1){
//                foreach ($pids as $key=>$value){
//                    $article['pname'].=db('type')->where("`id`=$value")->value('name')." , ";
//                }
//            }
//            else{
//                $article['pname'] = db('type')->where("`id`=$pids[0]")->value('name');
//            }
            if ($article['parent']==0){
                $article['pname']=TypeModel::where('id',0)->value("name");
            }
            else{
                $article['pname']=TypeModel::where('id',$article['parent'])->value("name");
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

        $ori=TypeModel::get($id);

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

        if(isset($data['code'])){
            $data['code']=$data['code'].$id.",";
        }


        $article = new TypeModel;

        $article->allowField(true)->save($data,['id' => $id]);
    }


    public function test(){
        $arrs=array("1","2","3","4");
        $id="1";

        $arr = array_merge(array_diff($arrs, array($id)));
        var_dump($arr);
    }


    //删除信息
    public function delArticle($id_single=''){
        if($id_single!=''){
            $id=$id_single;
        }
        else{
            $id=$_REQUEST['id'];
        }
        $base=new Base();
        $article = new ArticleModel;
        $result=db('type')->where('id',$id)->delete();
        $result2=db('article')->select();

        $list=array();//更新列表

        foreach ($result2 as $key=>$value){
            $arrs=explode(",",$value['pid']);
            if(in_array($id,$arrs)){//判断文章是否包含这个pid
                if(count($arrs) == 1){//判断数组长度,1为独立pid,可删除
                    $result=db('article')->where('id',$value['id'])->find();
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
                    db('article')->where('id',$value['id'])->delete();
                }
                else{//判断数组长度,含复数的pid,清除本id
                    $arr = array_merge(array_diff($arrs, array($id)));//删除数组中的本id元素,生成新pid
                    $data['id']=$value['id'];
                    $data['pid']=implode(",",$arr);
                    array_push($list,$data);
                }
            }
            $article->isUpdate()->saveAll($list);//对pid进行批量更新
        }

        return "ok";
    }

    //批量删除
    public function delAllArticle(){
        $id=$_REQUEST['id'];
        $idArr=explode(",",$id);//将传过来的ids字符串转换为数组
        foreach ($idArr as $key2=>$value2){
            $this->delArticle($value2);
        }
        return 'ok';
    }

}
