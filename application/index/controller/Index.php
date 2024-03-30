<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index(\app\index\service\Index $index)
    {
        $banner = $index->getBannerList();
        $recommendInfo = $index->getRecommendInfo();
        $projectList = $index->getProjectByHome();

        foreach ($projectList as $key => $val) {
            $projectList[$key]->rate = round($val->minimum / $val->goal, 2) * 100;
        }
        $this->assign('banner', $banner);
        $this->assign('recommendInfo', $recommendInfo);
        $this->assign('projectList', $projectList);
        return $this->view->translate();
    }



    public function notify()
    {
        $data = input();
        trace($data, 'notify');
        if (isset($data['merchant_reference']) && isset($data['status']) && $data['status'] == 1) {
            $orderInfo = Db::name('order')
                ->where('order_no', $data['merchant_reference'])
                ->find();
            if (!empty($orderInfo)) {
                Db::name('order')
                    ->where('id', $orderInfo['id'])
                    ->update([
                        'status' => 1,
                        'pay_time' => date("Y-m-d H:i:s"),
                    ]);
                echo "SUCCESS";
                $this->redirect('/index/index/index.html?pay_status=success');
            }
        }
        $this->redirect('/index/index/index.html?pay_status=fail');
    }

}
