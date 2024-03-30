<?php

namespace app\admin\model;

use think\Model;


class Nav extends Model
{

    

    

    // 表名
    protected $table = 'nav';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'typedata_text',
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getTypedataList()
    {
        //导航类型:home=首页,project=项目,publish=公共,news=新闻,about=关于我们
        return [
            'home' => __('首页'),
            'project' => __('项目'),
            'publish' => __('分类项目'),
            'news'=>'新闻',
            'about'=>'关于我们',
            'privacy_policy'=>'隐私协议',
            'terms'=>'条款',
            'faq'=>'常见问题',
            'news_detail'=>'新闻详情页',
            'project_detail'=>'项目详情',
        ];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getTypedataTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['typedata']) ? $data['typedata'] : '');
        $list = $this->getTypedataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
