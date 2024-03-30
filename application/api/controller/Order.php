<?php


namespace app\api\controller;


use app\common\controller\Api;
use fast\HttpRequest;
use think\Db;
use think\Exception;

class Order extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['create', 'payPal', 'sendData'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['create', 'payPal', 'sendData'];


    public function create()
    {

        $data = input('post.');

        $project = Db::name('project')
            ->where('id', $data['project_id'])
            ->find();
        $secret = '720f7fe4-2a96-4414-9569-f0730788026d'; // 签名密钥
        $orderNo = 'O' . date("YmdHis") . rand(1111, 9999);
        $fields = [
            'customer_first_name' => $data['fast_name'],//
            'customer_last_name' => $data['last_name'],//
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'customer_ip' => $_SERVER['REMOTE_ADDR'],
            'merchant_reference' => $orderNo,//
            'amount' => $data['price'],
            'currency' => 'CNY',
            'network' => 'Alipay',
            'subject' => $project['title'],
            'return_url' => 'https://www.donation-home-hong.org/index/index/notify',
        ];
        ksort($fields);
        $fields['sign'] = hash('SHA512', http_build_query($fields) . $secret);
        Db::name('order')
            ->insertGetId([
                'order_no' => $orderNo,
                'email' => $data['email'],
                'fast_name' => $data['fast_name'],
                'last_name' => $data['last_name'],
                'country' => $data['country'],
                'city' => $data['city'],
                'address' => $data['address'],
                'card_number' => $data['card_number'],
                'expration' => $data['expration'],
                'cvc' => $data['cvc'],
                'create_time' => date("Y-m-d H:i:s"),
                'pay_fee' => $data['price'],
                'project_id' => $data['project_id'],
            ]);
        $this->success('', $fields);
    }


    public function payPal()
    {
        $data = input('post.');
        $orderNo = 'O' . date("YmdHis") . rand(1111, 9999);
        $ip = $this->request->ip();
        $info = Db::name('project')
            ->where('id', $data['project_id'])->find();
        Db::name('project')
            ->where('id', $data['project_id'])
            ->update([
                'minimum' => $info['minimum'] + $data['price'],
                'maxmum' => $info['maxmum'] + 1,
            ]);
        Db::name('order')
            ->insertGetId([
                'order_no' => $orderNo,
                'pay_fee' => $data['price'],
                'project_id' => $data['project_id'],
                'email' => $data['email_address'],
                'fast_name' => $data['given_name'],
                'country' => $data['country_code'],
                'address' => $data['country_code'],
                'pay_id' => $data['pay_id'],
                'pay_order_id' => $data['order_id'],
                'pay_time' => date("Y-m-d H:i:s"),
                'create_time' => date("Y-m-d H:i:s"),
                'status' => $data['status'] == 'COMPLETED' ? 1 : 0,
            ]);
        $ttclid = '';
        if (!empty($data['ttclid'])) {
            $ttclid = $data['ttclid'];
        } elseif (!empty($data['_ttp'])) {
            $ttclid = $data['_ttp'];
        }
        $res = $this->sendData($data['email_address'], $data['price'], $data['project_id'],$ip, $ttclid);
        $this->success('', $res);
    }


    public function sendData($email, $price, $project_id, $ip,$ttclid)
    {
        $url = 'https://business-api.tiktok.com/open_api/v1.3/event/track/';
        $data = [
            'event_source' => 'web',
            'event_source_id' => 'CNQQSSBC77U9UU6RSH2G',
            'data' => [
                [
                    'event' => 'CompletePayment',
                    'event_time' => time(),
                    'page' => ['url' => 'https://www.donation-home-hong.org'],
                    'user' => [
                        'ttclid' => $ttclid,
                        'external_id' => $this->getRandStr(20),
                        'phone' => '',
                        'email' => $email,
                        'ip' => $ip,
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ],
                    'properties' => [
                        'content_type' => 'product',
                        'contents' => [
                            [
                                'value' => $price,
                                'currency' => 'USD',
                                'content_id' => $project_id,
                                'contents' => [
                                    [
                                        'content_id' => time(),
                                        'content_type' => 'product',
                                        'content_name' => "product_$project_id"
                                    ]
                                ]
                            ]
                        ],
                        'value' => $price,
                        'currency' => 'USD',
                    ]
                ]
            ]
        ];
        $header = [
            'Access-Token' => 'fe6ded977ec66e0fa3c3ac740617a55731fb48fe',
        ];
        $res = HttpRequest::curl_post($url, $data, 'json', true, $header);
        trace($res, '同步抖音数据');
        return true;
    }

    /**
     * 生成随机字符串
     * @param integer $length 生成字符串位数
     * @return string
     */
    private function getRandStr($length)
    {
        $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str = $str . $char[mt_rand(0, strlen($char) - 1)];
        }
        return $str;
    }

}