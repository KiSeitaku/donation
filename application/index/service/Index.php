<?php

namespace app\index\service;

use app\admin\model\Banner;
use app\admin\model\Project;
use think\Db;

class Index
{
    public function getBannerList()
    {
        $bannerModel = new Banner();
        return $bannerModel
            ->where('status', 'normal')
            ->order('weigh desc')
            ->select();
    }

    public function getRecommendInfo(){

        $ProjectModel = new Project();
        $info = $ProjectModel
            ->where('is_recommend',1)
            ->find();
        $info['pay_list'] = Db::name('project_pay_list')
            ->where('project_id',$info['id'])
            ->select();
        $info['random'] = rand($info['min_fee'],$info['max_fee']);
        return $info;
    }


    public function getProjectByHome(){
        $ProjectModel = new Project();
        $list = $ProjectModel
            ->where('is_home',1)
            ->limit(3)
            ->order('weigh desc,id')
            ->select();
        return $list;
    }
}