<?php
namespace app\index\controller;


use think\View;

class Index extends Thy
{
    public function index()
    {
        $view=new View();

        $test="测试文字";

        $view->thy=$test;

        $tt=db('article')->where('id',93)->find();

        return $view->fetch();

    }

}
