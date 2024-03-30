<?php


namespace app\index\service;


use app\admin\model\News;

class NewsData
{
    public function getNewsList()
    {
        $newsModel = new News();
        return $newsModel->order('id desc')->paginate();
    }


    public function detail($id)
    {
        $newsModel = new News();

        return $newsModel->where('id',$id)->find();
    }
}