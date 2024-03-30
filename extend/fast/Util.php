<?php

namespace fast;

use think\facade\Env;
use think\Image;


/**
 * 工具类
 * @author admin
 *
 */
class Util
{
    /**
     * 处理虚拟手机号码供前端可拨打
     * @param $phone
     * @return mixed
     */
    public static function handleVirtualPhone($phone)
    {
        return str_replace('_', ',', $phone);
    }

    /**
     * 日期别名
     * @param $datetime
     * @return false|string
     */
    public static function dateAlias($datetime)
    {
        if (date('Y-m-d') == date('Y-m-d', strtotime($datetime))) {
            return '今天';
        } else if (date('Y-m-d', strtotime('-1 days')) == date('Y-m-d', strtotime($datetime))) {
            return '昨天';
        } else if (date('Y-m-d', strtotime('+1 days')) == date('Y-m-d', strtotime($datetime))) {
            return '明天';
        } else {
            return date('m-d', strtotime($datetime));
        }
    }

    /**
     * 判断时间戳是否合法
     * @param $value
     * @return string
     */
    public static function isTimestamp($value)
    {
        if (is_numeric($value) && (int)$value == $value && strlen((string)$value) == 10 && date('Y-m-d H:i:s', $value) !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 时间戳转换（毫秒级转秒级）
     * @param $timestamp
     * @return mixed
     */
    public static function timestampCovert($timestamp)
    {
        if (strlen($timestamp) != strlen(time())) {
            return substr($timestamp, 0, -3);
        }
        return $timestamp;
    }

    /**
     * 判断是否中文字符
     * @param $str
     * @return bool
     */
    public static function isChineseChar($str)
    {
        if (preg_match("/^[\x7f-\xff]+$/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查文件是否为空
     * @param $file
     * @return boolean
     */
    public static function checkFileIsEmpty($file)
    {
        $content = @file_get_contents($file);
        if ($content === false || strlen($content) === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查密码长度和类型
     * @param $pwd
     * @param $minLen
     * @param $maxLen
     * @return boolean
     */
    public static function checkPasswordRex($pwd, $minLen = 8, $maxLen = 100)
    {
        // 8-20 位，字母、数字、字符
        $regStr = "/^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z\\W_]+$)(?![a-z0-9]+$)(?![a-z\\W_]+$)(?![0-9\\W_]+$)[a-zA-Z0-9\\W_]{" . $minLen . "," . $maxLen . "}$/";
        if (preg_match($regStr, $pwd)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断密码重点级别
     * @param $pwd
     * @return int  [1,3]:弱   [4,6]:中等   [7,8]:强   [9,10]:极好
     */
    public static function checkPasswordLevel($pwd)
    {
        $score = 0;
        if (preg_match("/[0-9]+/", $pwd)) {
            $score++;
        }
        if (preg_match("/[0-9]{3,}/", $pwd)) {
            $score++;
        }
        if (preg_match("/[a-z]+/", $pwd)) {
            $score++;
        }
        if (preg_match("/[a-z]{3,}/", $pwd)) {
            $score++;
        }
        if (preg_match("/[A-Z]+/", $pwd)) {
            $score++;
        }
        if (preg_match("/[A-Z]{3,}/", $pwd)) {
            $score++;
        }
        if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $pwd)) {
            $score += 2;
        }
        if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/", $pwd)) {
            $score++;
        }
        if (strlen($pwd) >= 10) {
            $score++;
        }
        return $score;
    }

    /**
     * 从$arr中移除$values值
     * @param $arr
     * @param $values
     * @return array
     */
    public static function removeArrayByValue($arr, $values)
    {
        $data = [];
        if (!empty($arr)) {
            foreach ($arr as $key => $item) {
                if (!empty($values) && in_array($item, $values)) {
                    continue;
                }
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * 数组a元素是否完全存在于b数组
     * @param $arr
     * @param $allArr
     * @return bool
     */
    public static function isAllInArray($arr, $allArr)
    {
        if (!empty($arr) && !empty($allArr)) {
            foreach ($arr as $item) {
                if (!in_array($item, $allArr)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 拷贝数组
     * @param $arr
     * @return mixed
     */
    public static function copyArray($arr)
    {
        $result = [];
        if (!empty($arr) && is_array($arr)) {
            foreach ($arr as $item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 二维数组值相加
     * @param $arr
     * @param $key
     * @return float
     */
    public static function my_array_sum($arr, $key)
    {
        $result = 0;
        if (!empty($arr)) {
            foreach ($arr as $item) {
                if (isset($item[$key])) {
                    $result += $item[$key];
                }
            }
        }
        return $result;
    }

    /**
     * 数组分组
     * @param $arr
     * @param $key
     * @param $rspKey
     * @return array
     */
    public static function array_group($arr, $key, $rspKey = false)
    {
        $result = []; //初始化一个数组
        foreach ($arr as $k => $v) {
            $result[$v[$key]][] = $v; //把$key对应的值作为键 进行数组重新赋值
        }
        if (!$rspKey) {
            $newResult = [];
            foreach ($result as $v) {
                $newResult[] = $v;
            }
            return $newResult;
        }
        return $result;
    }

    /**
     * 格式化日期为周几
     * @param $date
     * @return mixed
     */
    public static function formatWeekByDate($date)
    {
        $weekArray = ["日", "一", "二", "三", "四", "五", "六"];
        return $weekArray[date("w", strtotime($date))];
    }

    /**
     * 实体转义
     * @param $html
     */
    public static function my_html_entity_decode($html)
    {
        if (!empty($html)) {
            return html_entity_decode($html);
        }
        return '';
    }

    /**
     * 判断一个数组是否全部存在于另一个数组中
     * @param $arr
     * @param $allArr
     * @return boolean
     */
    public static function arrayIsAllExists($arr, $allArr)
    {
        if (!empty($arr) && !empty($allArr)) {
            foreach ($arr as $item) {
                if (!in_array($item, $allArr)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 判断一个数组是否部分存在于另一个数组中
     * @param $arr
     * @param $allArr
     * @return boolean
     */
    public static function arrayIsPartExists($arr, $allArr)
    {
        if (!empty($arr) && !empty($allArr)) {
            foreach ($arr as $item) {
                if (in_array($item, $allArr)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 返回13位时间戳(毫秒)
     * @return float
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (int)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 生成GUID
     * @return string
     */
    public static function getGuid()
    {
        $charId = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $guid = substr($charId, 0, 8) . $hyphen
            . substr($charId, 8, 4) . $hyphen
            . substr($charId, 12, 4) . $hyphen
            . substr($charId, 16, 4) . $hyphen
            . substr($charId, 20, 12);

        return $guid;
    }

    /**
     * 生成随机字符串
     * @param integer $length 生成字符串位数
     * @return string
     */
    public static function getRandStr($length)
    {
        $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str = $str . $char[mt_rand(0, strlen($char) - 1)];
        }
        return $str;
    }

    /**
     * 生成随机数字
     * @param integer $length 生成数字的位数
     * @return string
     */
    public static function getRandNum($length)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str = $str . mt_rand(0, 9);
        }
        return $str;
    }

    public static function getRandSymbol($length)
    {

        $char = '@!$#*.';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str = $str . $char[mt_rand(0, strlen($char) - 1)];
        }
        return $str;
    }

    /**
     * 判断是否是微信内置浏览器
     * @return boolean
     */
    public static function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否是手机号码
     * @param string $phone 手机号码
     * @return boolean
     */
    public static function isPhone($phone)
    {
        if (preg_match('/^1[3456789]\d{9}$/', $phone)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 过滤emoji表情
     * @desc 常用于微信昵称过虑表情
     * @param string $str 待过滤字符串
     * @return string
     */
    public static function filterEmoji($str)
    {
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);

        return $str;
    }

    /**
     * 获取两个时间戳之间相隔几天
     * @param integer $startDate 开始日期
     * @param integer $endDate 结束日期
     * @return integer
     */
    public static function getDiffDayByTimestamp($startDate, $endDate)
    {
        if (empty($endDate)) {
            $endDate = date('Y-m-d H:i:s');
        }
        $diffDay = (strtotime($endDate) - strtotime($startDate)) / 3600 / 24;
        return $diffDay;
    }

    /**
     * 获取两个时间戳之间相隔几个月
     * @param integer $startMonth 开始日期
     * @param integer $endMonth 结束日期
     * @return integer
     */
    public static function getDiffMonthByTimestamp($startMonth, $endMonth)
    {
        $date1 = explode('-', $endMonth);
        $date2 = explode('-', $startMonth);
        return abs($date1[0] - $date2[0]) * 12 - $date2[1] + abs($date1[1]);
    }

    /**
     * 获取两个日期之间的全部日期
     * @param DateTime $startDate 开始日期
     * @param DateTime $endDate 结束日期
     */
    public static function getDates($startDate, $endDate)
    {
        $arr = array();
        $dt_start = strtotime($startDate);
        $dt_end = strtotime($endDate);
        while ($dt_start <= $dt_end) {
            array_push($arr, date('Y-m-d', $dt_start));
            $dt_start = strtotime('+1 day', $dt_start);
        }
        return $arr;
    }

    /**
     * 导出数据为excel文件
     * @param string $tableFileName 文件名
     * @param array $data 表格数据
     */
    public static function exportExcel($tableFileName, $data)
    {
        require_once Env::get('root_path') . '/extend/phpexcel/PHPExcel.php';
        $xlsTitle = iconv('utf-8', 'gb2312', $tableFileName); // 文件名称
        $fileName = $tableFileName . date('_YmdHis'); // 文件名称
        $objPHPExcel = new \PHPExcel();
        $cellName = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ'
        );

        if (!empty($data)) {
            foreach ($data as $index => $v) {
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($index);
                $objPHPExcel->getActiveSheet()->setTitle($v['sheetTitle']);
                //冻结表头
                $objPHPExcel->getActiveSheet()->freezePane('A2');

                //循环写入表头
                for ($i = 0; $i < count($v['excelTitle']); $i++) {
                    $objPHPExcel->getActiveSheet()->setCellValue($cellName[$i] . '1', $v['excelTitle'][$i]);
                }
                //循环写入数据
                if (!empty(count($v['excelBody']))) {
                    foreach ($v['excelBody'] as $k => $v) {
                        $subRow = 0;
                        foreach ($v as $sv) {
                            $objPHPExcel->getActiveSheet()->setCellValue($cellName[$subRow] . ($k + 2), $sv);
                            $subRow++;
                        }
                    }
                }
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls"); // attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 输出xml字符
     * @param array $data 数据
     * @return string
     **/
    public static function formatArrayToXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return false;
        }

        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return
     */
    public static function formatXmlToArray($xml)
    {
        if (!$xml) {
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 格式化参数格式化成url参数
     * @param array $params 参数数组
     * @param array $filterParams 过滤参数数组
     * @param boolean $isFilterNullValue 是否过滤空值
     */
    public static function toUrlParams($params, $filterParams, $isFilterNullValue = true)
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($isFilterNullValue == false) {
                if (!in_array($k, $filterParams) && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            } else {
                if (!in_array($k, $filterParams) && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成二维码（链接类型）
     * @desc 1.不带logo二维码   2.带logo二维码
     * @param string $url 网址
     * @param string $fileName 文件名
     * @param string $logo 二维码中间的logo
     * @return string 返回二维码图片相对地址
     */
    public static function makeQrcode($url, $fileName = '', $logo = null)
    {
        require_once Env::get('root_path') . '/extend/phpqrcode/Phpqrcode.php';

        if (empty($url)) {
            return false;
        }

        //保存目录
        if (empty($fileName)) {
            $fileName = md5(date('YmdHis') . rand()) . '.png';//文件名
        } else {
            $fileName = $fileName . '.png';
        }
        $relativeSavePath = getUploadsRelativePath() . '/qrcodeImage'; //相对路径
        $savePath = getUploadsAbsolutePath() . '/qrcodeImage'; //绝对路径
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        //生成二维码
        $errorCorrectionLevel = !empty($logo) ? 'H' : 'L';//容错级别
        $matrixPointSize = !empty($logo) ? 6 : 8;//生成图片大小
        $outfile = $savePath . '/' . $fileName; //绝对路径+文件名
        \QRcode::png($url, $outfile, $errorCorrectionLevel, $matrixPointSize, 2);

        //二维码中间加logo
        if (!empty($logo) && file_exists($logo)) {
            $qrcode = imagecreatefromstring(file_get_contents($outfile));        //目标图象连接资源。
            $logo = imagecreatefromstring(file_get_contents($logo));    //源图象连接资源。
            $qrcode_width = imagesx($qrcode);            //二维码图片宽度
            $logo_width = imagesx($logo);        //logo图片宽度
            $logo_height = imagesy($logo);        //logo图片高度
            $logo_qr_width = $qrcode_width / 4;    //组合之后logo的宽度(占二维码的1/5)
            $scale = $logo_width / $logo_qr_width;    //logo的宽度缩放比(本身宽度/组合后的宽度)
            $logo_qr_height = $logo_height / $scale;  //组合之后logo的高度
            $from_width = ($qrcode_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

            //将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
            imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

            //保存图片
            imagepng($qrcode, $outfile);
            imagedestroy($qrcode);
            imagedestroy($logo);
        }

        return $relativeSavePath . '/' . $fileName;
    }

    /**
     * 合成推广海报
     * @desc 背景图片，二维码图片，头像图片均为本地图片，非远程图片
     * @param string $bgImage 背景图片
     * @param string $qrcodeImage 二维码图片
     * @param float $qrcodeX 二维码X轴偏移量(不带单位px)
     * @param float $qrcodeY 二维码Y轴偏移量(不带单位px)
     * @param float $qrcodeWidth 二维码宽度(不带单位px)
     * @param float $qrcodeHeight 二维码高度(不带单位px)
     * @param string $headImage 头像图片
     * @param float $headX 头像X轴偏移量(不带单位px)
     * @param float $headY 头像Y轴偏移量(不带单位px)
     * @param float $headWidth 头像宽度(不带单位px)
     * @param float $headHeight 头像高度(不带单位px)
     * @param string $nicknameText 昵称文本
     * @param float $nicknameX 昵称X轴偏移量(不带单位px)
     * @param float $nicknameY 昵称Y轴偏移量(不带单位px)
     * @param float $nicknameWidth 昵称文本宽度(不带单位px)
     * @param integer $nicknameFontsize 昵称字体大小(不带单位px)
     * @param string $nicknameColor 昵称字体颜色
     * @param string $nicknameAlign 昵称字段对齐方式
     * @return string 返回合成海报的相对路径
     */
    public static function makePosters($bgImage, $qrcodeImage, $qrcodeX = 0, $qrcodeY = 0, $qrcodeWidth = 160, $qrcodeHeight = 160, $headImage = null, $headX = 0, $headY = 0, $headWidth = 50, $headHeight = 50, $nicknameText = null, $nicknameX = 0, $nicknameY = 0, $nicknameWidth = 80, $nicknameFontsize = 14, $nicknameColor = '#ffffff', $nicknameAlign = 'left')
    {
        //保存海报路径
        $savePath = getUploadsAbsolutePath() . '/posters';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        //保存海报图片
        $bgImageInfo = getimagesize($bgImage);
        $bgImageExt = $bgImageInfo[2] == 2 ? '.jpg' : '.png';
        $saveFileName = md5(time() . rand()) . $bgImageExt;
        $absoluteSaveFile = getUploadsAbsolutePath() . '/posters/' . $saveFileName;
        $relativeSaveFile = getUploadsRelativePath() . '/posters/' . $saveFileName;

        //背景图像
        $bgImageObj = imagecreatefromstring(file_get_contents($bgImage));

        //合成二维码
        if ($qrcodeImage) {
            //二维码图片信息
            $qrcodeInfo = getimagesize($qrcodeImage);
            $qrcodeExt = $qrcodeInfo[2] == 2 ? '.jpg' : '.png';
            $qrcodeFile = getUploadsAbsolutePath() . '/posters/' . md5(time() . rand()) . $qrcodeExt;
            //缩放二维码
            $image = Image::open($qrcodeImage);
            $image->thumb($qrcodeWidth, $qrcodeHeight)->save($qrcodeFile);
            //二维码图像
            $qrcodeImageObj = imagecreatefromstring(file_get_contents($qrcodeFile));
            //合成二维码到背景图中
            imagecopymerge($bgImageObj, $qrcodeImageObj, $qrcodeX, $qrcodeY, 0, 0, $qrcodeWidth, $qrcodeHeight, 100);
        }

        //合成头像
        if ($headImage) {
            //头像信息
            $headInfo = getimagesize($headImage);
            $headExt = $headInfo[2] == 2 ? '.jpg' : '.png';
            $headFile = getUploadsAbsolutePath() . '/posters/' . md5(time() . rand()) . $headExt;
            //缩放头像
            $image = Image::open($headImage);
            $image->thumb($headWidth, $headHeight)->save($headFile);
            //头像图像
            $headImageObj = imagecreatefromstring(file_get_contents($headFile));
            //合成头像到背景图中
            imagecopymerge($bgImageObj, $headImageObj, $headX, $headY, 0, 0, $headWidth, $headHeight, 100);
        }

        //保存图片
        if ($bgImageExt == '.jpg') {
            imagejpeg($bgImageObj, $absoluteSaveFile);
        } else {
            imagepng($bgImageObj, $absoluteSaveFile);
        }

        //合成昵称水印
        if ($nicknameText) {
            //计算昵称文字水印的宽度
            $imageBox = ImageTTFBBox($nicknameFontsize, 0, 'public/static/font/SIMHEI.TTF', self::filterEmoji($nicknameText));
            $boxWidth = $imageBox[2] - $imageBox[0];
            if ($boxWidth > $nicknameWidth) {//文字宽度超过设置宽度，截取昵称
                $boxStrlen = strlen($nicknameText) / ($boxWidth / $nicknameWidth);//字符长度
                $nicknameText = mb_strcut($nicknameText, 0, (round($boxStrlen / 3) - 1) * 3, 'utf-8');
                $nicknameText .= '..';
            } else {
                if ($nicknameAlign == 'right') {//昵称右对齐
                    $nicknameX = $nicknameX + $nicknameWidth - $boxWidth;
                }
                if ($nicknameAlign == 'center') {//中间对齐
                    $nicknameX = ($nicknameX + $nicknameWidth / 2) - ($boxWidth / 2);
                }
            }
            //加文字水印
            $imageText = Image::open($absoluteSaveFile);
            $imageText->text($nicknameText, 'public/static/font/SIMHEI.TTF', $nicknameFontsize, $nicknameColor, [$nicknameX, $nicknameY])->save($absoluteSaveFile);
        }

        //销毁图像资源
        !isset($bgImageObj) ?: imagedestroy($bgImageObj);
        !isset($qrcodeImageObj) ?: imagedestroy($qrcodeImageObj);
        !isset($headImageObj) ?: imagedestroy($headImageObj);

        //删除图片
        !isset($qrcodeFile) ?: @unlink($qrcodeFile);
        !isset($headFile) ?: @unlink($headFile);

        return $relativeSaveFile;//返回合成后的图片相对路径
    }

    /**
     * 删除目录
     * @param $dir
     * @return bool
     */
    public static function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : @unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * 根据月份获取该月开始日期和结束日期
     * @param string $month 可传格式：yyyy-mm 、mm
     * @return array [$firstData, $lastDate]
     */
    public static function getMonthDate($month = null)
    {
        //不传month，则为当月
        if (empty($month)) {
            $firstDate = date('Y-m-d');
            return [$firstDate, date('Y-m-d', strtotime("$firstDate +1 month -1 day"))];
        }

        //传入月份带年份
        if (strlen($month) == 7) {
            $firstDate = $month . '-01';
            if (!self::isDate($firstDate)) {
                return false;
            }
            return [$firstDate, date('Y-m-d', strtotime("$firstDate +1 month -1 day"))];
        }

        //传入月份不带年份
        if (strlen($month) == 2) {
            $firstDate = date('Y') . '-' . $month . '-01';
            if (!self::isDate($firstDate)) {
                return false;
            }
            return [$firstDate, date('Y-m-d', strtotime("$firstDate +1 month -1 day"))];
        }

        return false;
    }

    /**
     * 判断日期是否合法
     * @param $data
     * @return bool
     */
    public static function isDate($data)
    {
        $date = strtotime($data);
        if ($data == (date("Y-m-d", $date)) || $data == (date("Y-m-j", $date)) || $data == (date("Y-n-d", $date)) || $data == (date("Y-n-j", $date))) {
            return true;
        }
        return false;
    }

    /**
     * 校验多个时间段是否合法
     * @param array $times
     * @return mixed
     */
    public static function checkTimes($times)
    {
        if (empty($times)) {
            return false;
        }

        $times = explode("\n", Util::handleInputEnterChar($times));
        if (empty($times)) {
            trace($times, '1');
            return false;
        }

        $newTimes = '';
        $prevTime = '';
        foreach ($times as $k => $v) {
            $v = str_replace('：', ':', $v);
            $v = str_replace('_', '-', $v);
            $v = str_replace(' ', '', $v);
            $splitTime = explode('-', $v);
            if (!$splitTime || count($splitTime) != 2) {
                continue;
            }
            if (!isset($splitTime[0]) || !isset($splitTime[1])) {
                trace($splitTime, '3');
                return false;
            }
            if (!strtotime($splitTime[0]) || !strtotime($splitTime[1])) {
                trace($splitTime, '4');
                return false;
            }

            $firstTime = explode(':', $splitTime[0]);
            $lastTime = explode(':', $splitTime[1]);
            $splitTime[0] = date('H:i', strtotime(str_pad($firstTime[0], 2, 0, STR_PAD_LEFT) . ':' . $firstTime[1]));
            $splitTime[1] = date('H:i', strtotime(str_pad($lastTime[0], 2, 0, STR_PAD_LEFT) . ':' . $lastTime[1]));

            if ($splitTime[0] > $splitTime[1]) {
                trace($splitTime, '5');
                return false;
            }
            if ($prevTime != '' && $splitTime[0] < $prevTime) {
                trace($splitTime, '6');
                return false;
            }
            $prevTime = $splitTime[1];

            $newTimes .= ($k != 0 ? "\r\n" : '') . ($splitTime[0] . '-' . $splitTime[1]);
        }

        return $newTimes;
    }

    /**
     * 获取指定日期的第1天
     * @param $date
     * @return bool|string
     */
    public static function getFirstDatetime($date)
    {
        if ($date && strtotime($date)) {
            return date('Y-m-d', strtotime($date)) . ' 00:00:00';
        }

        return false;
    }

    /**
     * 获取指定日期最后的datetime时间格式
     * @param string $date
     * @return string
     */
    public static function getLastDatetime($date)
    {
        if ($date && strtotime($date)) {
            return date('Y-m-d', strtotime($date)) . ' 23:59:59';
        }

        return false;
    }

    /**
     * 获取指定日期当月的开始和结束日期
     * @param string $date
     * @param $dateType
     * @return string
     */
    public static function getMonthDiff($date, $dateType = 'Y-m-d H:i:s')
    {
        if ($date && strtotime($date)) {
            $firstDatetime = date('Y-m-01', strtotime($date));
            $lastDatetime = date('Y-m-d', strtotime("$firstDatetime +1 month -1 day"));
            $datetimeArr = [$firstDatetime . ' 00:00:00', $lastDatetime . ' 23:59:59'];

            return [date($dateType, strtotime($datetimeArr[0])), date($dateType, strtotime($datetimeArr[1]))];
        }

        return false;
    }

    /**
     * 处理多行表单换行符不一致的问题
     * @param string $str
     * @return string
     */
    public static function handleInputEnterChar($str)
    {
        return str_replace("\r", "", $str);
    }

    /**
     * 处理姓名只显示姓
     * @param string $truename
     * @return string
     */
    public static function cutTruename($truename)
    {
        return !empty($truename) ? mb_substr($truename, 0, 1, 'utf-8') . '**' : '';
    }

    /**
     * 处理银行卡号
     * @param string $cardNo
     * @return string
     */
    public static function cutBankCardNo($cardNo)
    {
        return substr($cardNo, 0, 4) . str_repeat("*", 8) . substr($cardNo, -4);
    }

    /**
     * 处理手机号
     * @param string $phone
     * @param $model 1：显示前3后2    2：显示前3后4
     * @return string
     */
    public static function cutPhone($phone, $model = 1)
    {
        if ($model == 1) {
            return substr($phone, 0, 3) . str_repeat("*", 6) . substr($phone, -2);
        } else {
            return substr($phone, 0, 3) . str_repeat("*", 4) . substr($phone, -4);
        }
    }

    /**
     * 处理身份证号
     * @param string $idcard
     * @return string
     */
    public static function cutIdcard($idcard)
    {
        return substr($idcard, 0, 4) . str_repeat("*", 12) . substr($idcard, -2);
    }

    /**
     * 生成跑腿一维码编号
     * @param integer $orderId
     * @return string
     */
    public static function makeDeliveryCode($orderId)
    {
        return 100000 + $orderId;
    }

    /**
     * 解析跑腿一维码编号
     * @param string $code
     * @return integer $orderId
     */
    public static function parseDeliveryCode($code)
    {
        return floor($code) - 100000;
    }

    /**
     * 二维数组按照某个键值进行排序
     * @param array $arr 要排序的数组
     * @param string $sortKey 要排序的键
     * @param string $sortType 要排序的方式  默认为升序   asc升序   desc降序
     * @return array
     */
    public static function sortArr($arr, $sortKey, $sortType = 'asc')
    {
        if (empty($arr)) {
            return $arr;
        }

        if ($sortType == 'asc' || $sortType == 'ASC') {
            $order = 'SORT_ASC';
        } else if ($sortType == 'desc' || $sortType == 'DESC') {
            $order = 'SORT_DESC';
        } else {
            $order = 'SORT_ASC';
        }
        $arrSort = array();
        foreach ($arr as $i => $row) {
            foreach ($row as $k => $v) {
                $arrSort[$k][$i] = $v;
            }
        }
        array_multisort($arrSort[$sortKey], constant($order), $arr);
        return $arr;
    }

    /**
     * 判断是否为json
     * @param string $data
     * @param bool $assoc
     * @return array|bool|mixed|string
     */
    public static function isJson($data = '', $assoc = false)
    {
        $data = json_decode($data, $assoc);
        if ($data && (is_object($data)) || (is_array($data) && !empty(current($data)))) {
            return $data;
        }
        return false;
    }

    /**
     * 转换富文本为纯文本
     */
    public static function transformRichText($content)
    {
        $content = htmlspecialchars_decode($content);
        $content = str_replace("&nbsp;", "", $content);
        $content = str_replace(" ", "", $content);
        $content = strip_tags($content);
        return $content;
    }

    /**
     * php截取指定两个字符之间字符串
     * @param string $begin 开始字符串
     * @param string $end 结束字符串
     * @param string $str 需要截取的字符串
     * @return string
     */
    public static function cutStr($begin, $end, $str)
    {
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;

        return mb_substr($str, $b, $e);
    }


    public static function substrFormat($text, $length, $replace = '..', $encoding = 'UTF-8')
    {
        if ($text && mb_strlen($text, $encoding) > $length) {
            return mb_substr($text, 0, $length, $encoding) . $replace;
        }
        return $text;
    }

    /**
     * 删除目录及子目录下的空目录
     * @param $path
     */
    public static function rmEmptyDir($path)
    {
        if (is_dir($path) && ($handle = opendir($path)) !== false) {
            while (($file = readdir($handle)) !== false) {// 遍历文件夹
                if ($file != '.' && $file != '..') {
                    $currfile = $path . '/' . $file;// 当前目录
                    if (is_dir($currfile)) {// 目录
                        self::rmEmptyDir($currfile);// 如果是目录则继续遍历
                        if (count(scandir($currfile)) == 2) {//目录为空,=2是因为.和..存在
                            rmdir($currfile);// 删除空目录
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * 导出Csv数据表格
     * @param array $headList 导出的Excel数据第一列表头
     * @param array $dataList 要导出的数组格式的数据
     * @param string $fileName 输出Excel表格文件名
     * @param string $exportUrl 直接输出到浏览器or输出到指定路径文件下
     * @return bool|false|string
     */
    public static function exportCsv($headList, $dataList, $fileName, $exportUrl = 'php://output')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄,php://output 表示直接输出到浏览器,$exportUrl表示输出到指定路径文件下
        $fp = fopen($exportUrl, 'a');

        //输出Excel列名信息
        foreach ($headList as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headList[$key] = iconv("UTF-8", "gbk//IGNORE", $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headList);

        //逐行取出数据
        $count = count($dataList);
        for ($i = 0; $i < $count; $i++) {
            $row = $dataList[$i];
            foreach ($row as $key => $value) {
                $row[$key] = iconv('utf-8', 'gbk//IGNORE', $value);
            }
            fputcsv($fp, $row);
        }
        return $fileName;
    }

    /**
     * 导入Csv数据表格
     * @param string $fileName 文件名
     * @param int $offset 从第几行开始读，默认从第一行读取
     * @param int $line 读取几行，默认全部读取
     * @return bool|array
     */
    public static function importCsv($fileName, $offset = 0, $line = 0)
    {
        ini_set("memory_limit", "2048M");//防止内存溢出
        $handle = fopen($fileName, 'r');
        if (!$handle) {
            return '文件打开失败';
        }

        $i = 0;
        $j = 0;
        $arr = [];
        while ($data = fgetcsv($handle)) {
            //小于偏移量则不读取,但$i仍然需要自增
            if ($i < $offset && $offset) {
                $i++;
                continue;
            }
            //大于读取行数则退出
            if ($i > $line && $line) {
                break;
            }

            foreach ($data as $key => $value) {
                $encode = mb_detect_encoding($value, array("ASCII", "GB2312", "GBK", "BIG5", "UTF-8"));
                trace($encode, 'importCsvEncode');
                if (in_array($encode, ["ASCII", "GB2312", "GBK", "EUC-CN"])) {
                    $value = iconv("gbk", "utf-8//IGNORE", $value); //转化编码
                }
                $arr[$j][] = $value;
            }
            $i++;
            $j++;
        }

        trace($arr, 'importCsvData');
        return $arr;
    }

    /**
     * 指定字段返回无键值数据
     * @param array $data
     * @param $filed
     * @return array
     */
    public static function filterArrayByField($data, $filed)
    {
        $res = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                array_push($res, $item[$filed]);
            }
        }
        return $res;
    }

    /**
     * 获取两个时间相隔分钟数
     * @param $startDatetime
     * @param $endDatetime
     */
    public static function getDiffMinuteByDatetime($startDatetime, $endDatetime = '')
    {
        if (empty($endDatetime)) {
            $endDatetime = time();
        }

        return floor(($endDatetime - strtotime($startDatetime)) / 60);
    }

    /**
     * 字符串转十六进制
     * @param string $string
     * @return string
     */
    public static function strToHex($string)
    {
        return bin2hex($string);
    }

    /**
     * 十六进制转字符串
     * @param string $hex
     * @return string
     */
    public static function hexToStr($hex)
    {
        $string = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    public static function sha512($data, $rawOutput = false)
    {
        if (!is_scalar($data)) {
            return false;
        }
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha512', $data, $rawOutput);

    }

    /**
     * 替换中文名字
     * @param $userName
     * @return string
     */
    public static function nameHide($userName)
    {
        $strlen = mb_strlen($userName, 'utf-8');
        if (empty($strlen)) return '';
        $firstStr = mb_substr($userName, 0, 1, 'utf-8');
        $lastStr = mb_substr($userName, -1, 1, 'utf-8');
        return ($strlen == 2) ? $firstStr . str_repeat('*', mb_strlen($userName, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    /**
     * 获取指定月份最后一天
     * @param $date
     * @return mixed
     */
    public static function getMonthLastDate($date)
    {
        //日期格式错误，需要修正，如：2022-2-31
        if (!empty($date) && !self::isDate($date) && strtotime($date)) {
            return date('Y-m-t', strtotime($date) - 3600 * 24 * 3);
        }
        return $date;
    }

    public static function getCalendar($startTime, $endTime)
    {
        $dayList = self::getDates($startTime, $endTime);
        $arr_tpl = array(1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '', 7 => '');
        $date_arr = [];
        $j = 0;
        foreach ($dayList as $key => $value) {
            if (!isset($date_arr[$j])) {
                $date_arr[$j] = $arr_tpl;
            }
            $week = date("w", strtotime($value));
            $week = $week == 0 ? 7 : $week;
            $date_arr[$j][$week] = $value;
            if ($date_arr[$j][7]) {
                $j++;
            }
        }
        return $date_arr;
    }

    /**
     * 计算字符串长度，中文长度为2，英文为1
     * @param $str
     * @return int
     */
    public static function abslength($str)
    {
        return strlen(preg_replace("#[^\x{00}-\x{ff}]#u", '**', $str));
    }

    /**
     * 时间段重合判断
     * @param array $data 日期数组
     * @param string $fieldStart 开始日期字段名
     * @param string $fieldEnd 结束日期字段名
     * @return bool true为重合，false为不重合
     */
    public static function isTimeCross(array $data, string $fieldStart = 'start_day', string $fieldEnd = 'end_day')
    {
        // 按开始日期排序
        $array_column = array_column($data, $fieldStart);
        array_multisort($array_column, SORT_ASC, $data);

        // 冒泡判断是否满足时间段重合的条件
        $num = count($data);
        for ($i = 1; $i < $num; $i++) {
            $pre = $data[$i - 1];
            $current = $data[$i];
            if (strtotime($pre[$fieldStart]) < strtotime($current[$fieldEnd]) && strtotime($current[$fieldStart]) < strtotime($pre[$fieldEnd])) {
                return true;
            }
        }

        return false;
    }

    public static function prettyDate($date)
    {
        $time = strtotime($date);

        $now = time();

        $ago = $now - $time;

        if ($ago < 60) {
            $when = round($ago);

            $s = ($when == 1) ? "second" : "seconds";

            return "$when $s ago";

        } elseif ($ago < 3600) {
            $when = round($ago / 60);

            $m = ($when == 1) ? "minute" : "minutes";

            return "$when $m ago";

        } elseif ($ago >= 3600 && $ago < 86400) {
            $when = round($ago / 60 / 60);

            $h = ($when == 1) ? "hour" : "hours";

            return "$when $h ago";

        } elseif ($ago >= 86400 && $ago < 2629743.83) {
            $when = round($ago / 60 / 60 / 24);

            $d = ($when == 1) ? "day" : "days";

            return "$when $d ago";

        } elseif ($ago >= 2629743.83 && $ago < 31556926) {
            $when = round($ago / 60 / 60 / 24 / 30.4375);

            $m = ($when == 1) ? "month" : "months";

            return "$when $m ago";

        } else {
            $when = round($ago / 60 / 60 / 24 / 365);

            $y = ($when == 1) ? "year" : "years";

            return "$when $y ago";

        }

    }

    public static function arraySort($array, $key = null)
    {
        $count = count($array);
        if ($count < 0) {
            return false;
        }
        for ($i = 0; $i < $count; $i++) {
            for ($j = $count - 1; $j > $i; $j--) {
                if ($key && isset($array[$key])) {//二维数组健存在
                    if ($array[$j][$key] < $array[$j - 1][$key]) {
                        $tmp = $array[$j];
                        $array[$j] = $array[$j - 1];
                        $array[$j - 1] = $tmp;
                    }
                } else { //一维数组
                    if ($array[$j] < $array[$j - 1]) {
                        $tmp = $array[$j];
                        $array[$j] = $array[$j - 1];
                        $array[$j - 1] = $tmp;
                    }
                }
            }
        }
        return $array;
    }
}