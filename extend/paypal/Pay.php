<?php

namespace paypal;

class Pay
{

    private $url = 'https://api-m.sandbox.paypal.com/';

    public function getAccessToken($clientId, $clientSecret)
    {
// 准备POST请求的数据
        $post_data = array(
            'grant_type' => 'client_credentials'
        );
// 使用base64编码拼接客户端ID和客户端密钥
        $auth_header = base64_encode($clientId . ':' . $clientSecret);

// 准备curl选项
        $curl_options = array(
            CURLOPT_URL => $this->url . 'v1/oauth2/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . $auth_header
            ),
            CURLOPT_RETURNTRANSFER => true
        );

// 初始化curl会话
        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 执行请求并获取响应
        $response = curl_exec($ch);
// 检查是否有错误发生
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response, true);
    }


    public function orderQuery($orderId)
    {
        $token = $this->getAccessToken(config('paypal')['client_id'], config('paypal')['client_secret']);
        $url = 'https://api-m.sandbox.paypal.com/v2/payments/captures/' . $orderId;
        $headerArray = ['Authorization' => 'Bearer ' . $token['access_token']];
        return $this->curl_get($url, null, 'json', true, $headerArray);
    }

    public function authorized($authorization_id){
        $token = $this->getAccessToken(config('paypal')['client_id'], config('paypal')['client_secret']);
        $url = 'https://api-m.sandbox.paypal.com/v2/payments/authorizations/' . $authorization_id;
        $headerArray = ['Authorization' => 'Bearer ' . $token['access_token']];
        return $this->curl_get($url, null, 'json', true, $headerArray);
    }

    /**
     * curl get请求
     * @param string $url 请求url
     * @param array $data 请求参数
     * @return array|error 成功返回微信服务器响应数组，失败返回错误信息
     */
    private function curl_get($url, $data = null, $headerType = null, $rspArray = true, $headerArray = null)
    {
        $curl = curl_init();

        //array数组序列化为字符串，拼接于url后
        if (!empty($data) && is_array($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if ($headerType == 'json') {
            $httpHeader = [
                'Content-Type: application/json'
            ];
            if (!empty($headerArray)) {
                foreach ($headerArray as $key => $item) {
                    $httpHeader[] = $key . ': ' . $item;
                }
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        }
        if ($headerType == 'form') {
            $httpHeader = [
                'Content-Type: application/x-www-form-urlencoded'
            ];
            if (!empty($headerArray)) {
                foreach ($headerArray as $key => $item) {
                    $httpHeader[] = $key . ': ' . $item;
                }
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        }

        $response = curl_exec($curl);
        if ($rspArray) {
            $response = json_decode($response, true);
        }
        $error = curl_error($curl);
        return $error ? $error : $response;
    }

}