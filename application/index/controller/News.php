<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\index\service\NewsData;

class News extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index(NewsData $newsData){

        $list = $newsData->getNewsList();
        $this->assign('list',$list);
        return $this->view->translate();
    }



    public function detail(NewsData $newsData,$id){
        $info = $newsData->detail($id);
        $this->assign('info',$info);
        return $this->view->translate();
    }
}