<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use fast\Random;

class View
{
    // 视图实例
    protected static $instance;
    // 模板引擎实例
    public $engine;
    // 模板变量
    protected $data = [];
    // 用于静态赋值的模板变量
    protected static $var = [];
    // 视图输出替换
    protected $replace = [];

    /**
     * 构造函数
     * @access public
     * @param array $engine 模板引擎参数
     * @param array $replace 字符串替换参数
     */
    public function __construct($engine = [], $replace = [])
    {
        // 初始化模板引擎
        $this->engine($engine);
        // 基础替换字符串
        $request = Request::instance();
        $base = $request->root();
        $root = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;
        if ('' != $root) {
            $root = '/' . ltrim($root, '/');
        }
        $baseReplace = [
            '__ROOT__' => $root,
            '__URL__' => $base . '/' . $request->module() . '/' . Loader::parseName($request->controller()),
            '__STATIC__' => $root . '/static',
            '__CSS__' => $root . '/static/css',
            '__JS__' => $root . '/static/js',
        ];
        $this->replace = array_merge($baseReplace, (array)$replace);
    }

    /**
     * 初始化视图
     * @access public
     * @param array $engine 模板引擎参数
     * @param array $replace 字符串替换参数
     * @return object
     */
    public static function instance($engine = [], $replace = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($engine, $replace);
        }
        return self::$instance;
    }

    /**
     * 模板变量静态赋值
     * @access public
     * @param mixed $name 变量名
     * @param mixed $value 变量值
     * @return void
     */
    public static function share($name, $value = '')
    {
        if (is_array($name)) {
            self::$var = array_merge(self::$var, $name);
        } else {
            self::$var[$name] = $value;
        }
    }

    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name 变量名
     * @param mixed $value 变量值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 设置当前模板解析的引擎
     * @access public
     * @param array|string $options 引擎参数
     * @return $this
     */
    public function engine($options = [])
    {
        if (is_string($options)) {
            $type = $options;
            $options = [];
        } else {
            $type = !empty($options['type']) ? $options['type'] : 'Think';
        }

        $class = false !== strpos($type, '\\') ? $type : '\\think\\view\\driver\\' . ucfirst($type);
        if (isset($options['type'])) {
            unset($options['type']);
        }
        $this->engine = new $class($options);
        return $this;
    }

    /**
     * 配置模板引擎
     * @access private
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return $this
     */
    public function config($name, $value = null)
    {
        $this->engine->config($name, $value);
        return $this;
    }

    /**
     * 解析和获取模板内容并翻译 用于输出
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @param bool $renderContent 是否渲染内容
     * @return string
     * @throws Exception
     */
    public function translate($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        // 模板变量
        $vars = array_merge(self::$var, $this->data, $vars);
        // 页面缓存
        ob_start('ob_gzhandler');
        ob_implicit_flush(0);
        // 渲染输出
        try {
            $method = $renderContent ? 'display' : 'fetch';
            // 允许用户自定义模板的字符串替换
            $replace = array_merge($this->replace, $replace, (array)$this->engine->config('tpl_replace_string'));
            $this->engine->config('tpl_replace_string', $replace);
            $this->engine->$method($template, $vars, $config);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        // 获取并清空缓存
        $content = ob_get_clean();
        // 内容过滤标签
        Hook::listen('view_filter', $content);
        return $this->compress_html($content, Lang::indexDetect() ?? 'en');
    }

    protected function compress_html($html, $lang = 'en')
    {
        if ($lang == 'en') return $html;
        $dom = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        $textNodes = $xpath->query('//body//*[not(self::script) and not(self::img) and not(self::meta) and not(self::lang) and not(self::style)]/text()');
        $texts = '';
        $currencyPattern = '/\$[0-9]+(\.[0-9]+)?/';
        $newNodes = [];
        foreach ($textNodes as $node) {
            $textContent = trim($node->nodeValue);
            if (!empty($textContent)) {
                if (!preg_match($currencyPattern, $textContent)) {
                    $texts .= "<p>$textContent</p>";
                    $newNodes[] = $node;
                }
            }
        }
        $translations = $this->translateTexts($texts, $lang);
        if (empty($translations)) return $html;
        foreach ($newNodes as $key => $node) {
            $textContent = trim($node->nodeValue);
            if (!empty($textContent)) {
                $newNode = $dom->createTextNode($translations[$key] ?? '');
                $node->parentNode->replaceChild($newNode, $node);
            }

        }
        return $dom->saveHTML();
    }

    protected function translateTexts($query, $lang = 'en')
    {
        $cache_key = "{$lang}_" . md5($query);
        $cache = Cache::get($cache_key);
        if ($cache) return json_decode($cache);
        $url = "https://openapi.youdao.com/translate_html";
        $appKey = '77c713135b20e210';
        $secKEY = 'hfySQrw27cJBPE8POy8XDn6rjAnkWR9e';
        $from = 'en';
        $salt = Random::uuid();
        $curtime = (string)time();
        $args = [
            'q' => $query,
            'appKey' => $appKey,
            'salt' => $salt,
            'from' => $from,
            'to' => $lang,
            'signType' => 'v3',
            'curtime' => $curtime,
            'sign' => hash("sha256", $appKey . (mb_strlen($query, 'utf-8') <= 20 ? $query : (mb_substr($query, 0, 10) . mb_strlen($query, 'utf-8') . mb_substr($query, mb_strlen($query, 'utf-8') - 10, mb_strlen($query, 'utf-8')))) . $salt . $curtime . $secKEY)
        ];
        $ch = curl_init();
        $data = '';
        foreach ($args as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $data .= $key . '[' . $k . ']=' . rawurlencode($v) . '&';
                }
            } else {
                $data .= "$key=" . rawurlencode($val) . "&";
            }
        }
        $data = trim($data, "&");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($r, true);
        if (isset($res['data']['translation'])) {
            $doc = $res['data']['translation'];
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML(mb_convert_encoding($doc, 'HTML-ENTITIES', 'UTF-8'));
            $p_tags = $dom->getElementsByTagName('p');
            $paragraphs = array();
            foreach ($p_tags as $tag) {
                $paragraphs[] = trim($tag->textContent);
            }
            Cache::set($cache_key, json_encode($paragraphs));
            return $paragraphs;
        }
        return null;
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @param bool $renderContent 是否渲染内容
     * @return string
     * @throws Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        // 模板变量
        $vars = array_merge(self::$var, $this->data, $vars);

        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        // 渲染输出
        try {
            $method = $renderContent ? 'display' : 'fetch';
            // 允许用户自定义模板的字符串替换
            $replace = array_merge($this->replace, $replace, (array)$this->engine->config('tpl_replace_string'));
            $this->engine->config('tpl_replace_string', $replace);
            $this->engine->$method($template, $vars, $config);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        // 获取并清空缓存
        $content = ob_get_clean();
        // 内容过滤标签
        Hook::listen('view_filter', $content);
        return $content;
    }

    /**
     * 视图内容替换
     * @access public
     * @param string|array $content 被替换内容（支持批量替换）
     * @param string $replace 替换内容
     * @return $this
     */
    public function replace($content, $replace = '')
    {
        if (is_array($content)) {
            $this->replace = array_merge($this->replace, $content);
        } else {
            $this->replace[$content] = $replace;
        }
        return $this;
    }

    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     */
    public function display($content, $vars = [], $replace = [], $config = [])
    {
        return $this->fetch($content, $vars, $replace, $config, true);
    }

    /**
     * 模板变量赋值
     * @access public
     * @param string $name 变量名
     * @param mixed $value 变量值
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板变量
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * 检测模板变量是否设置
     * @access public
     * @param string $name 模板变量名
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}
