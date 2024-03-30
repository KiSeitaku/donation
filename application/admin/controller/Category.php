<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Category as CategoryModel;
use fast\Tree;
use think\Db;

/**
 * 分类管理
 *
 * @icon   fa fa-list
 * @remark 用于管理网站的所有分类,分类可进行无限级分类,分类类型请在常规管理->系统配置->字典配置中添加
 */
class Category extends Backend
{

    /**
     * @var \app\common\model\Category
     */
    protected $model = null;
    protected $categorylist = [];
    protected $noNeedRight = ['selectpage'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\common\model\Category');

        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        $typeList = CategoryModel::getTypeList();

        $this->view->assign("typeList", $typeList);
        $this->view->assign("parentList", $categorydata);
        $this->assignconfig('typeList', $typeList);
    }
    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }


    public function lists() {


        $data = input('name');
        $type = input('type');
        $keyValue = $this->request->request('keyValue');
        if ( is_numeric($keyValue)) {
            $model = Db::name('category')->where('id',$keyValue)->whereLike('name',"%$data%")->field('id,name');
            if($type =='one') {
                $model= $model->where('pid',0);
            } else{
                $model= $model->where('pid','>',0);
            }
            $list = $model->select();
        } else {
            $model = Db::name('category')->whereLike('name',"%$data%")->field('id,name');
            if($type =='one') {
                $model= $model->where('pid',0);
            } else{
                $model= $model->where('pid','>',0);
            }
            $list = $model->select();
        }

        $result = array("total" => count($list), "rows" => $list,'post'=>$data);

        return json($result);
    }
}
