<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header("Content-type:app/json");

use think\Db;




class Category extends Base
{

    /**
     * @api {post} /category/add 添加|更新分类的方法
     * @apiName category_add
     * @apiGroup Category
     *
     * @apiParam {String{0..200}} title 分类标题
     * @apiParam {String{0..300}} img 图片列表用","隔开
     * @apiParam {Number} pid 父id
     * @apiParam {String{0..150}} keywords 关键词
     * @apiParam {String{0..350}} description 描述
     * @apiParam {String="text"} content 文章内容
     * @apiParam {Number} type 分类类型,预留,暂不使用
     * @apiParam {Number{0-9999}} sort 排序(desc)
     * @apiParam {String} state 状态(on:off),显示:隐藏
     *
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

        //此为分类表处理,与article相比
        //多:code,type
        //少:time,click
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
            $p_code=db('type')->where("id = ".$data['pid'])->value("code");
            $data['code']=$p_code.$msg['update_id'].",";
            db('type')->where('id ='.$msg['update_id'])->update($data);

            $msg['message']= "更新成功";
            parent::log("更新了分类 : ".$data['title'],$data['user_id'],$data['id'],"修改");
        }else{
            //添加
            unset($data->id);
            $msg['insert_id']=db('type')->insertGetId($data);
            //添加时,多了code要处理
            //code是(父code+本id);
            $p_code=db('type')->where("id = ".$data['pid'])->value("code");

            $code=$p_code.$msg['insert_id'].",";
            db('type')->where('id ='.$msg['insert_id'])->setField('code',$code);
            $msg['message']= "添加成功";
            parent::log("添加了分类 : ".$data['title'],$data['user_id'],$data['id'],"添加");
        }
        $msg['code']= "200";


        return json($data);

    }


    /**
     * @api {get} /category/get_single_category 获取单个分类的内容
     * @apiName category_single
     * @apiGroup Category
     *
     * @apiParam {Number} id 单个分类id
     *
     * @apiSuccess (成功返回) {Array} data 分类内容
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message 状态信息
     *
     */
    public function get_single_category(){
        $id=$_GET['id'];

        $data['data']=db("type")->where("id = $id")->select()[0];
        $data['code']="200";
        $data['message']="获取成功";

        return json($data);

    }


    /**
     * @api {get} /category/del 删除分类的方法
     * @apiName category_del
     * @apiGroup Catogory
     *
     * @apiParam {String} id 需删除的分类id,可传多个值(1,2,3),以逗号分隔
     *
     * @apiSuccess (成功返回) {Number} count 删除分类的个数
     * @apiSuccess (成功返回) {Number} code 状态标识码
     * @apiSuccess (成功返回) {String} message 状态信息
     */
    public function del(){
        $id=$_GET['id'];

        $data['count']=0;

        $id_arr=explode(",",$id);

        foreach ($id_arr as $id_val){
            //获取要删除的本身及子分类的id
            $types=parent::get_type_id($id_val);

            //删除本分类
            $temp=db('type')->where("id in ($types)")->select();//返回删除的文章列表
            //删除本身自带的Img
            parent::del_imgs($temp);

            //处理分类下文章的内容
            $articles=db("article")->where("pid in ($types)")->select();//返回所要删除分类下的文章列表
            //删除本身子分类的自带的Img
            parent::del_imgs($articles);

            $articles_count=db("article")->where("pid in ($types)")->delete();//返回删除的数目
            $categorys_count=db('type')->where("id in ($types)")->delete();//返回删除的数目


            $data['count'].=$articles_count;
            $data['count'].=$categorys_count;
        }


        $data['code']="200";
        $data['message']="删除成功";

        return json($data);

    }


}
