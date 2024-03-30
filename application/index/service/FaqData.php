<?php


namespace app\index\service;


use app\admin\model\Faq;

class FaqData
{
    public function getFaqList()
    {

        $faqModel = new Faq();

        return $faqModel->order('id desc')->paginate();
    }
}