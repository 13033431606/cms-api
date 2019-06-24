<?php
namespace app\index\controller;

use think\View;


class Admin extends Thy
{
    public function index()
    {
        $view=new View();


        return $view->fetch();

    }


}
