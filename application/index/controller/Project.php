<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\index\service\ProjectData;

class Project extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    public function index(ProjectData $data){

        $list = $data->getProject(input());

        foreach ($list as $key => $val){
            if($key %3 == 1 ){
                $val->class_name = 'fadeInLeft';
            }
            if($key %3 == 2){
                $val->class_name = 'fadeInUp';
            }
            if($key %3 ==0){
                $val->class_name = 'fadeInRight';
            }
            $list[$key]->rate = round($val->minimum/$val->goal,2) *100;
        }

        $one_category_id = input('one_category_id');
        $two_category_id = input('two_category_id');

        $oneCategoryList = $data->getCategory();
        $twoCategoryList = $data->getTwoCategory($one_category_id);

        $this->assign('list',$list);
        $this->assign('oneCategoryList',$oneCategoryList);
        $this->assign('twoCategoryList',$twoCategoryList);
        $this->assign('one_category_id',$one_category_id);
        $this->assign('two_category_id',$two_category_id);
        return $this->view->translate();
    }

    public function detail(ProjectData $data,$id){

        $info  = $data->getProjectInfo($id);

        $Random = rand($info['min_fee'],$info['max_fee']);
        $this->assign('info',$info);
        $this->assign('random',$Random);
        $rate = round($info['minimum']/$info['goal'],2) *100;
        $this->assign('rate',$rate);
        return $this->view->translate();
    }
}