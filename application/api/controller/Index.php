<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }

    public function lang()
    {
        $lang = $this->request->get('lang', 'en');
        session('think_var', $lang);
        cookie('think_var', $lang);
        $this->success(lang('succeed'));
    }
}
