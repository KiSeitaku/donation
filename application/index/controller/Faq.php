<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\index\service\FaqData;

class Faq extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index(FaqData $faqData){

        $list = $faqData->getFaqList();

        $this->assign('list',$list);
        return $this->view->translate();

    }
}