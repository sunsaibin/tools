<?php
namespace Sabine\Tools\Util;

/**
 * Class Tools
 * @package sabine\util
 * @author sabine <zx23324592@163.com>
 */
class Tools {
    /**
     * 获取相对时间
     * @param int $timestamp
     * @return string
     */
    public static function getDate(int $timestamp): string {
        $now = time();
        $diff = $now - $timestamp;
        if ($diff <= 60) {
            return $diff . '秒前';
        } elseif ($diff <= 3600) {
            return floor($diff / 60) . '分钟前';
        } elseif ($diff <= 86400) {
            return floor($diff / 3600) . '小时前';
        } elseif ($diff <= 2592000) {
            return floor($diff / 86400) . '天前';
        } else {
            return '一个月前';
        }
    }

    /**
     * 二次封装的密码加密
     * @param $str
     * @param string $auth_key
     * @return string
     */
    public static function userMd5(string $str, string $auth_key = ''): string {
        if (!$auth_key) {
            $auth_key = config('common.auth_key');
        }

        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }

    /**
     * 将查询的二维对象转换成二维数组
     * @param $res
     * @param string $key 允许指定索引值
     * @return array
     */
    public static function buildArrFromObj($res, string $key = ''): array {
        $arr = [];
        foreach ($res as $value) {
            $value = $value->toArray();
            if ($key) {
                $arr[$value[$key]] = $value;
            } else {
                $arr[] = $value;
            }
        }

        return $arr;
    }

    /**
     * 将二维数组变成指定key
     * @param $array
     * @param string $keyName
     * @return array
     */
    public static function buildArrByNewKey($array, string $keyName = 'id'): array {
        $list = [];
        foreach ($array as $item) {
            $list[$item[$keyName]] = $item;
        }

        return $list;
    }

    /**
     * 把返回的数据集转换成Tree
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param string $root
     * @return array
     */
    public static function listToTree(
        array $list,
        string $pk = 'id',
        string $pid = 'pid',
        string $child = 'children',
        string $root = '0'
    ): array {
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }

    /**
     * 将层级数组遍历成一维数组
     * @param array $list
     * @param int $lv
     * @param string $title
     * @return array
     */
    public static function formatTree(array $list, int $lv = 0, string $title = 'title'): array {
        $formatTree = [];
        foreach ($list as $key => $val) {
            $title_prefix = '';
            for ($i = 0; $i < $lv; $i++) {
                $title_prefix .= "|---";
            }
            $val['lv'] = $lv;
            $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
            $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix . $val[$title];
            if (!array_key_exists('children', $val)) {
                array_push($formatTree, $val);
            } else {
                $child = $val['children'];
                unset($val['children']);
                array_push($formatTree, $val);
                $middle = self::formatTree($child, $lv + 1, $title); //进行下一层递归
                $formatTree = array_merge($formatTree, $middle);
            }
        }

        return $formatTree;
    }

    /**
     * id加密
     * @param int $id
     * @param null $len
     * @param null $key
     * @param null $alphabet
     * @return false|string
     */
    public static function encodeId(int $id, $len = null, $key = null, $alphabet = null)
    {
        $hashids = \Sabine\Tools\Hashids\Hashids::instance($len,$key,$alphabet);
        $encodeId = $hashids->encode($id);
        return $encodeId;
    }

    /**
     * id解密
     * @param string $id
     * @param null $len
     * @param null $key
     * @param null $alphabet
     * @return array|int|mixed
     */
    public static function decodeId(string $id, $len = null, $key = null, $alphabet = null)
    {
        $hashids = \Sabine\Tools\Hashids\Hashids::instance($len,$key,$alphabet);
        $decodeId = $hashids->decode($id);
        return $decodeId;
    }

    /**
     * 判断是否微信访问
     * @return bool
     */
    public static function is_weixin_visit()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 时间秒转换为字符串
     * @param $secs
     * @param $lang
     * @return string|string[]
     */
    public static function secsToStr($secs, $lang) {
        $r = '';
        if ($lang == 'EN') {
            $day = 'day';
            $hour = 'hour';
            $minute = 'minute';
            $second = 'second';
        } else {
            $day = '天';
            $hour = '小时';
            $minute = '分钟';
            $second = '秒';
        }
        if($secs>=86400){$days=floor($secs/86400);
            $secs=$secs%86400;
            $r=$days.' '.$day;
            if($days<>1){$r.='s';}
            if($secs>0){$r.=', ';}}
        if($secs>=3600){$hours=floor($secs/3600);
            $secs=$secs%3600;
            $r.=$hours.' '.$hour;
            if($hours<>1){$r.='s';}
            if($secs>0){$r.=', ';}}
        if($secs>=60){$minutes=floor($secs/60);
            $secs=$secs%60;
            $r.=$minutes.' '.$minute;
            if($minutes<>1){$r.='s';}
            if($secs>0){$r.=', ';}}
        if ($secs > 0) {
            $r.=$secs.' '.$second;
        }
        if($secs<>1 && $lang=='EN'){$r.='s';
        }
        return $r;
    }

    /**
     * [arraytoxml 将数组转换成xml格式（简单方法）:]
     * @param [type] $data [数组]
     * @return [type]  [array 转 xml]
     */
    public static function arraytoxml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }

    /**
     * [xmltoarray xml格式转换为数组]
     * @param [type] $xml [xml]
     * @return [type]  [xml 转化为array]
     */
    public static function xmltoarray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }

    /**
     * @name php获取中文字符拼音首字母
     * @param $str
     * @return null|string
     */
    public static function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }

        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }

        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }

        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }

        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }

        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }

        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }

        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }

        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }

        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }

        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }

        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }

        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }

        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }

        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }

        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }

        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }

        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }

        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }

        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }

        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }

        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }

        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }

        return '其他';
    }

    /**
     * 获取当前月的所有日期
     * @return array
     */
    public static function getMonthDays($time = 0)
    {
        if ($time == 0) {
            $time = time();
        }
        $monthDays = [];
        $firstDay = date('Y-m-01', $time);
        $i = 0;
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
        while (date('Y-m-d', strtotime("$firstDay +$i days")) <= $lastDay) {
            $monthDays[] = date('Y-m-d', strtotime("$firstDay +$i days"));
            $i++;
        }
        return $monthDays;
    }
}
