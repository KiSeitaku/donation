<?php


namespace app\index\service;

use app\admin\model\Project;
use app\admin\model\Category;
use app\admin\model\News;
use think\Db;

class ProjectData
{

    public function getProject($search = [])
    {
        $ProjectModel = new Project();
        $model = $ProjectModel->order('id desc');
        if(isset($search['one_category_id']) && !empty($search['one_category_id'])){
            $model =  $model->where('one_category_id',$search['one_category_id']);
        }
        if(isset($search['two_category_id']) && !empty($search['two_category_id'])){
            $model = $model->where('two_category_id',$search['two_category_id']);
        }
        return $model->paginate();
    }


    public function getCategory()
    {

        $oneCategoryList = (new Category())->where('pid', 0)->select();
        return$oneCategoryList;
    }
    public function getTwoCategory($one_category_id)
    {

        $twoCategoryList = (new Category())->where('pid', $one_category_id)->select();
        return $twoCategoryList;
    }

    public function getProjectInfo($id){
        $ProjectModel = new Project();
        $data =  $ProjectModel->where('id',$id)->find();
        if(!empty($data['news_id'])){
            $data['news_info'] = (new News())->where('id',$data['news_id'])->find();
        }
        $data['pay_list'] = Db::name('project_pay_list')
            ->where('project_id',$id)
            ->select();
        return $data;
    }
}