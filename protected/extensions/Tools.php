<?php

/**
 * Project: sample
 * User: chenzhidong
 * Date: 14-7-4
 * Time: 13:56
 */
class Tools {
	const FLAG_NUMERIC = 1;
	const FLAG_NO_NUMERIC = 2;
	const FLAG_ALPHANUMERIC = 3;

	/**
     * 无极分类
     * @param $arr  传入数组
     * @param int $level 层级
     * @param string $sort 排序字段
     * @param boolean $prefix 每项前缀
     * @param integer $mode 前缀模式
     */
    public static function menuMake($arr, $level = -1, $sort = 'sort', $menu_arr = array(), $prefix=true, $mode=1)
    {
    	$nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
    	if(empty($arr)) return $arr;

        if($level < 0){
            //初始化
            foreach ($arr as $v) {
                $d = $v->attributes;
                $_data[] = $d;
                $_sort[] = $d[$sort];
            }
            //排序
            array_multisort($_sort, SORT_DESC, $_data);
            //分级
            foreach($_data as $v) {
                $_menu[$v['parent']][] = $v;
            }
            //开始分级
            foreach($_menu[0] as $v){
                $menu[] = $v;
                //判断是否有子分类。有的话递归
                if(isset($_menu[$v['id']])){
                    $menu = array_merge($menu,self::menuMake($_menu[$v['id']], 1, $sort,$_menu, $prefix, $mode));
                }
            }
//            var_dump($menu);
//            die;
            return $menu;
        }else{
            foreach($arr as $k=>$m){
                switch ($mode) {
                	case 1:
                		$m['name'] = $prefix ? CHtml::decode(str_repeat("│&nbsp;&nbsp;", $level)).($k+1<count($arr)?'├─&nbsp;':'└─&nbsp;').$m['name'] : $m['name'];
                		break;

                	case 2:
                		$m['name'] = $prefix ? CHtml::decode(str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $level)).'└─&nbsp;'.$m['name'] : $m['name'];
                		break;
                }
                $ret[] = $m;
                if(isset($menu_arr[$m['id']])){
                    $ret = array_merge($ret,self::menuMake($menu_arr[$m['id']], $level+1, $sort,$menu_arr,$prefix, $mode));
                }
            }
            return $ret;
        }
    }

    /**
     * 打开缓冲器，开始输入JS
     */
    public static function startJs() {
        ob_start();
    }

    /**
     * 关闭缓冲器，提取内容并注册JS
     * @param $js_name bool | string 需要注册的JS文件名称，默认16位英文数字混编随机值
     */
    public static function endJs($js_name) {
        $js = ob_get_clean();
        $js_name = $js_name . uniqid();
        Yii::app()->clientScript->registerScript($js_name, $js, CClientScript::POS_END);
    }

	/**
	 * 截取UTF8编码字符串从首字节开始指定宽度(非长度), 适用于字符串长度有限的如新闻标题的等宽度截取
	 * 中英文混排情况较理想. 全中文与全英文截取后对比显示宽度差异最大,且截取宽度远大越明显.
	 * @param string $str	UTF-8 encoding
	 * @param int[option] $width 截取宽度
	 * @param string[option] $end 被截取后追加的尾字符
	 * @param float[option] $x3<p>
	 * 	3字节（中文）字符相当于希腊字母宽度的系数coefficient（小数）
	 * 	中文通常固定用宋体,根据ascii字符字体宽度设定,不同浏览器可能会有不同显示效果</p>
	 *
	 * @return string
	 * @author waiting
	 * http://waiting.iteye.com/blog/581888
	 */
	public static function u8_title_substr($str, $width = 0, $end = '...', $x3 = 0) {
		global $CFG; // 全局变量保存 x3 的值
	    if ($width <= 0 || $width >= strlen($str)) {
	        return $str;
	    }
	    $arr = str_split($str);
	    $len = count($arr);
	    $w = 0;
	    $width *= 10;
		$e = '';

	    // 不同字节编码字符宽度系数
	    $x1 = 11;   // ASCII
	    $x2 = 16;
	    $x3 = $x3===0 ? ( $CFG['cf3']  > 0 ? $CFG['cf3']*10 : $x3 = 21 ) : $x3*10;
	    $x4 = $x3;

	    // http://zh.wikipedia.org/zh-cn/UTF8
	    for ($i = 0; $i < $len; $i++) {
	        if ($w >= $width) {
	            $e = $end;
	            break;
	        }
	        $c = ord($arr[$i]);
	        if ($c <= 127) {
	            $w += $x1;
	        }
	        elseif ($c >= 192 && $c <= 223) { // 2字节头
	            $w += $x2;
	            $i += 1;
	        }
	        elseif ($c >= 224 && $c <= 239) { // 3字节头
	            $w += $x3;
	            $i += 2;
	        }
	        elseif ($c >= 240 && $c <= 247) { // 4字节头
	            $w += $x4;
	            $i += 3;
	        }
	    }

	    return implode('', array_slice($arr, 0, $i) ). $e;
	}

	public static function randomstring($length = 8, $flag = self::FLAG_NO_NUMERIC) {
		switch ($flag)
		{
			case self::FLAG_NUMERIC:
				$str = '0123456789';
				break;
			case self::FLAG_NO_NUMERIC:
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case self::FLAG_ALPHANUMERIC:
			default:
				$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}
		for ($i = 0, $passwd = ''; $i < $length; $i++)
			$passwd .= substr($str, mt_rand(0, strlen($str) - 1), 1);

		return $passwd;
	}

	public static function getHttpHost($http = false, $entities = false) {
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities)
			$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
		{
			$https = false;
			if (isset($_SERVER['HTTPS']))
				$https = ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
			if (isset($_SERVER['SSL']))
				$https = ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');
			$host = ($https ? 'https://' : 'http://') . $host;
		}

		return $host;
	}

    /*
     * 判断输出
     */
    public static function export($str,$replace='--'){
        return $str?nl2br($str):$replace;
    }

	public static function getDomain() {
		if (preg_match("#[\w-]+\.(com|net|org|gov|cc|biz|info|cn|co)\b(\.(cn|hk|uk|jp|tw))*#", $_SERVER['HTTP_HOST'], $match))
			return $match[0];
		return "";
	}

	public static function getServerName() {
		if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
			return $_SERVER['HTTP_X_FORWARDED_SERVER'];

		return $_SERVER['SERVER_NAME'];
	}

	public static function getRemoteAddress() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CDN_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_REAL_IP']))
		{
			$ip = $_SERVER['HTTP_CDN_REAL_IP'];
		}
		elseif (isset($_SERVER['HTTP_CDN_SRC_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_SRC_IP']))
		{
			$ip = $_SERVER['HTTP_CDN_SRC_IP'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP']))
		{
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
		{
			foreach ($matches[0] AS $xip)
			{
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip))
				{
					$ip = $xip;
					break;
				}
			}
		}

		return $ip;
	}

	public static function getReferer() {
		if (isset($_SERVER['HTTP_REFERER']))
			return $_SERVER['HTTP_REFERER'];
		else
			return null;
	}

	public static function usingSecureMode() {
		if (isset($_SERVER['HTTPS']))
			return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
		if (isset($_SERVER['SSL']))
			return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');

		return false;
	}

	public static function getCurrentUrlProtocolPrefix() {
		if (Tools::usingSecureMode())
			return 'https://';
		else
			return 'http://';
	}

	public static function truncate($str, $max_length, $suffix = '...') {
		if (mb_strlen($str, 'utf-8') <= $max_length)
			return $str;

		return mb_substr($str, 0, $max_length - mb_strlen($suffix, 'utf-8'), 'utf-8') . $suffix;
	}

	public static function substr($string, $length, $havedot = 0, $charset = '') {
		if (empty($charset))
		{
			$charset = 'utf8';
		}
		if (strtolower($charset) == 'gbk')
			$charset = 'gbk';
		else
			$charset = 'utf8';
		if (self::strlen($string, $charset) <= $length)
		{
			return $string;
		}
		if (function_exists('mb_strcut'))
		{
			$string = mb_substr($string, 0, $length, $charset);
		}
		else
		{
			$pre = '{%';
			$end = '%}';
			$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);
			$strcut = '';
			$strlen = strlen($string);
			if ($charset == 'utf8')
			{
				$n = $tn = $noc = 0;
				while ($n < $strlen)
				{
					$t = ord($string[$n]);
					if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
					{
						$tn = 1;
						$n++;
						$noc++;
					}
					elseif (194 <= $t && $t <= 223)
					{
						$tn = 2;
						$n += 2;
						$noc++;
					}
					elseif (224 <= $t && $t <= 239)
					{
						$tn = 3;
						$n += 3;
						$noc++;
					}
					elseif (240 <= $t && $t <= 247)
					{
						$tn = 4;
						$n += 4;
						$noc++;
					}
					elseif (248 <= $t && $t <= 251)
					{
						$tn = 5;
						$n += 5;
						$noc++;
					}
					elseif ($t == 252 || $t == 253)
					{
						$tn = 6;
						$n += 6;
						$noc++;
					}
					else
					{
						$n++;
					}
					if ($noc >= $length)
					{
						break;
					}
				}
				if ($noc > $length)
				{
					$n -= $tn;
				}
				$strcut = substr($string, 0, $n);
			}
			else
			{
				$n = $tn = $noc = 0;
				while ($n < $strlen)
				{
					$t = ord($string[$n]);
					if ($t > 127)
					{
						$tn = 2;
						$n += 2;
						$noc++;
					}
					else
					{
						$tn = 1;
						$n++;
						$noc++;
					}
					if ($noc >= $length)
					{
						break;
					}
				}
				if ($noc > $length)
				{
					$n -= $tn;
				}
				$strcut = substr($string, 0, $n);
			}
			$string = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
		}
		if ($havedot)
		{
			$string = $string . "...";
		}

		return $string;
	}

	public static function strlen($string, $charset = 'utf8') {
		if (strtolower($charset) == 'gbk')
			$charset = 'gbk';
		else
			$charset = 'utf8';
		if (function_exists('mb_strlen'))
		{
			return mb_strlen($string, $charset);
		}
		else
		{
			$n = $noc = 0;
			$strlen = strlen($string);
			if ($charset == 'utf8')
			{
				while ($n < $strlen)
				{
					$t = ord($string[$n]);
					if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
					{
						$n++;
						$noc++;
					}
					elseif (194 <= $t && $t <= 223)
					{
						$n += 2;
						$noc++;
					}
					elseif (224 <= $t && $t <= 239)
					{
						$n += 3;
						$noc++;
					}
					elseif (240 <= $t && $t <= 247)
					{
						$n += 4;
						$noc++;
					}
					elseif (248 <= $t && $t <= 251)
					{
						$n += 5;
						$noc++;
					}
					elseif ($t == 252 || $t == 253)
					{
						$n += 6;
						$noc++;
					}
					else
					{
						$n++;
					}
				}
			}
			else
			{
				while ($n < $strlen)
				{
					$t = ord($string[$n]);
					if ($t > 127)
					{
						$n += 2;
						$noc++;
					}
					else
					{
						$n++;
						$noc++;
					}
				}
			}

			return $noc;
		}
	}

	public static function simplearray($array, $key) {
		if (!empty($array) && is_array($array))
		{
			$result = array();
			foreach ($array as $k => $item)
			{
				$result[$k] = $item[$key];
			}

			return $result;
		}

		return null;
	}

	public static function simpleerrors($errors) {
		$result = array();
		if (is_array($errors))
		{
			foreach ($errors as $key => $error)
			{
				if (is_array($error))
				{
					$result = array_merge($result, self::simpleerrors($error));
				}
				else
				{
					$result[] = $error;
				}
			}
		}
		else
			$result[] = $errors;
		return $result;
	}

	public static function obj2array($object) {
		return json_decode(json_encode($object), true);
	}

	public static function arrayUnique($array) {
		if (version_compare(phpversion(), '5.2.9', '<'))
			return array_unique($array);
		else
			return array_unique($array, SORT_REGULAR);
	}

	public static function arrayUnique2d($array, $keepkeys = true) {
		$output = array();
		if (!empty($array) && is_array($array))
		{
			$stArr = array_keys($array);
			$ndArr = array_keys(end($array));
			$tmp = array();
			foreach ($array as $i)
			{
				$i = join("¤", $i);
				$tmp[] = $i;
			}
			$tmp = array_unique($tmp);
			foreach ($tmp as $k => $v)
			{
				if ($keepkeys)
					$k = $stArr[$k];
				if ($keepkeys)
				{
					$tmpArr = explode("¤", $v);
					foreach ($tmpArr as $ndk => $ndv)
					{
						$output[$k][$ndArr[$ndk]] = $ndv;
					}
				}
				else
				{
					$output[$k] = explode("¤", $v);
				}
			}
		}

		return $output;
	}

	/**
	 * 友好的时间显示
	 *
	 * @param int    $sTime 待显示的时间
	 * @param string $type  类型. normal | mohu | full | ymd | other
	 * @return string
	 */
	public static function friendlyDate($sTime,$type = 'normal',$format='Y-m-d') {
	    if (!$sTime)
	        return '';
	    //sTime=源时间，cTime=当前时间，dTime=时间差
	    $cTime      =   time();
	    $dTime      =   $cTime - $sTime;
	    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
	    //$dDay     =   intval($dTime/3600/24);
	    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	    //normal：n秒前，n分钟前，n小时前，日期
	    if($type=='normal'){
	        if( $dTime < 60 ){
	            if($dTime < 10){
	                return '刚刚';    //by yangjs
	            }else{
	                return intval(floor($dTime / 10) * 10)."秒前";
	            }
	        }elseif( $dTime < 3600 ){
	            return intval($dTime/60)."分钟前";
	        //今天的数据.年份相同.日期相同.
	        }elseif( $dYear==0 && $dDay == 0  ){
	            //return intval($dTime/3600)."小时前";
	            return '今天'.date('H:i',$sTime);
	        }elseif($dYear==0){
				return date($format,$sTime);
	            // return date("m月d日 H:i",$sTime);
	        }else{
	            return date($format,$sTime);
	        }
	    }elseif($type=='mohu'){
	        if( $dTime < 60 ){
	            return $dTime."秒前";
	        }elseif( $dTime < 3600 ){
	            return intval($dTime/60)."分钟前";
	        }elseif( $dTime >= 3600 && $dDay == 0  ){
	            return intval($dTime/3600)."小时前";
	        }elseif( $dDay > 0 && $dDay<=7 ){
	            return intval($dDay)."天前";
	        }elseif( $dDay > 7 &&  $dDay <= 30 ){
	            return intval($dDay/7) . '周前';
	        }elseif( $dDay > 30 ){
	            return intval($dDay/30) . '个月前';
	        }
	    //full: Y-m-d , H:i:s
	    }elseif($type=='full'){
	        return date("Y-m-d , H:i:s",$sTime);
	    }elseif($type=='ymd'){
	        return date("Y-m-d",$sTime);
	    }else{
	        if( $dTime < 60 ){
	            return $dTime."秒前";
	        }elseif( $dTime < 3600 ){
	            return intval($dTime/60)."分钟前";
	        }elseif( $dTime >= 3600 && $dDay == 0  ){
	            return intval($dTime/3600)."小时前";
	        }elseif($dYear==0){
	            return date("Y-m-d H:i:s",$sTime);
	        }else{
	            return date("Y-m-d H:i:s",$sTime);
	        }
	    }
	}

	public static function generateYear() {
		$tab = array();
		for ($i = date('Y') - 10; $i >= 1900; $i--)
			$tab[] = $i;

		return $tab;
	}

	public static function generateMonth() {
		$tab = array();
		for ($i = 1; $i != 13; $i++)
			$tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));

		return $tab;
	}

	public static function dateadd($interval, $step, $date) {
		list($year, $month, $day) = explode('-', $date);
		if (strtolower($interval) == 'y')
		{
			return date('Y-m-d', mktime(0, 0, 0, $month, $day, intval($year) + intval($step)));
		}
		elseif (strtolower($interval) == 'm')
		{
			return date('Y-m-d', mktime(0, 0, 0, intval($month) + intval($step), $day, $year));
		}
		elseif (strtolower($interval) == 'd')
		{
			return date('Y-m-d', mktime(0, 0, 0, $month, intval($day) + intval($step), $year));
		}

		return date('Y-m-d');
	}

	public static function strexists($string, $find) {
		return !(strpos($string, $find) === FALSE);
	}

	public static function utf8substr($string, $beginIndex, $length) {
		if (strlen($string) < $length)
		{
			return substr($string, $beginIndex);
		}
		$char = ord($string[$beginIndex + $length - 1]);
		if ($char >= 224 && $char <= 239)
		{
			$str = substr($string, $beginIndex, $length - 1);

			return $str;
		}
		$char = ord($string[$beginIndex + $length - 2]);
		if ($char >= 224 && $char <= 239)
		{
			$str = substr($string, $beginIndex, $length - 2);

			return $str;
		}

		return substr($string, $beginIndex, $length);
	}

	public static function implode($glue, $array) {
		$return = '';
		if (!empty($array))
		{
			foreach ($array as $item)
			{
				if (is_array($item))
					$return .= $glue . self::implode($glue, $item);
				elseif (!empty($item))
					$return .= $item;
			}
		}
		return trim($return, ';');
	}

	public static function isX86_64arch() {
		return (PHP_INT_MAX == '9223372036854775807');
	}

	public static function getMaxUploadSize($max_size = 0) {
		$post_max_size = self::strtobyte(ini_get('post_max_size'));
		$upload_max_filesize = self::strtobyte(ini_get('upload_max_filesize'));
		if ($max_size > 0)
			$result = min($post_max_size, $upload_max_filesize, $max_size);
		else
			$result = min($post_max_size, $upload_max_filesize);

		return $result;
	}

	public static function getMemoryLimit() {
		$memory_limit = @ini_get('memory_limit');

		return self::strtobyte($memory_limit);
	}

	public static function strtobyte($value) {
		if (is_numeric($value))
			return $value;
		else
		{
			$value_length = strlen($value);
			$qty = (int)substr($value, 0, $value_length - 1);
			$unit = strtolower(substr($value, $value_length - 1));
			switch ($unit)
			{
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}

			return $qty;
		}
	}

	public static function bytetostr($value) {
		if ($value >= 1073741824)
		{
			$value = round($value / 1073741824 * 100) / 100 . ' GB';
		}
		elseif ($value >= 1048576)
		{
			$value = round($value / 1048576 * 100) / 100 . ' MB';
		}
		elseif ($value >= 1024)
		{
			$value = round($value / 1024 * 100) / 100 . ' KB';
		}
		else
		{
			$value = $value . ' Bytes';
		}

		return $value;
	}

	public static function verifyPostToken(BaseController $controller, $post_token) {
		$token = $controller->getSession('post_token');
		$controller->unsetSession('post_token');
		if ($post_token && $token && $token == $post_token)
			return true;
		else
			return false;
	}

	public static function setPostToken(BaseController $controller) {
		$token = uniqid();
		$controller->setSession('post_token', $token);
		return $token;
	}

	public static function cleanXSS($data) {
		// Fix &entity\n;
		$data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		} while ($old_data !== $data);

		// we are done...
		return $data;
	}

	public static function emotion($message = '', $size = '24px') {
		$emotions = array(
				"/::)",
				"/::~",
				"/::B",
				"/::|",
				"/:8-)",
				"/::<",
				"/::$",
				"/::X",
				"/::Z",
				"/::'(",
				"/::-|",
				"/::@",
				"/::P",
				"/::D",
				"/::O",
				"/::(",
				"/::+",
				"/:--b",
				"/::Q",
				"/::T",
				"/:,@P",
				"/:,@-D",
				"/::d",
				"/:,@o",
				"/::g",
				"/:|-)",
				"/::!",
				"/::L",
				"/::>",
				"/::,@",
				"/:,@f",
				"/::-S",
				"/:?",
				"/:,@x",
				"/:,@@",
				"/::8",
				"/:,@!",
				"/:!!!",
				"/:xx",
				"/:bye",
				"/:wipe",
				"/:dig",
				"/:handclap",
				"/:&-(",
				"/:B-)",
				"/:<@",
				"/:@>",
				"/::-O",
				"/:>-|",
				"/:P-(",
				"/::'|",
				"/:X-)",
				"/::*",
				"/:@x",
				"/:8*",
				"/:pd",
				"/:<W>",
				"/:beer",
				"/:basketb",
				"/:oo",
				"/:coffee",
				"/:eat",
				"/:pig",
				"/:rose",
				"/:fade",
				"/:showlove",
				"/:heart",
				"/:break",
				"/:cake",
				"/:li",
				"/:bome",
				"/:kn",
				"/:footb",
				"/:ladybug",
				"/:shit",
				"/:moon",
				"/:sun",
				"/:gift",
				"/:hug",
				"/:strong",
				"/:weak",
				"/:share",
				"/:v",
				"/:@)",
				"/:jj",
				"/:@@",
				"/:bad",
				"/:lvu",
				"/:no",
				"/:ok",
				"/:love",
				"/:<L>",
				"/:jump",
				"/:shake",
				"/:<O>",
				"/:circle",
				"/:kotow",
				"/:turn",
				"/:skip",
				"/:oY",
				"/:#-0",
				"/:hiphot",
				"/:kiss",
				"/:<&",
				"/:&>"
		);
		foreach ($emotions as $index => $emotion)
		{
			$message = str_replace($emotion, '<img style="width:' . $size . ';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/' . $index . '.gif" />', $message);
		}

		return $message;
	}

	public static function fulltohalf($str) {
		$arr = array(
				'０' => '0',
				'１' => '1',
				'２' => '2',
				'３' => '3',
				'４' => '4',
				'５' => '5',
				'６' => '6',
				'７' => '7',
				'８' => '8',
				'９' => '9',
				'Ａ' => 'A',
				'Ｂ' => 'B',
				'Ｃ' => 'C',
				'Ｄ' => 'D',
				'Ｅ' => 'E',
				'Ｆ' => 'F',
				'Ｇ' => 'G',
				'Ｈ' => 'H',
				'Ｉ' => 'I',
				'Ｊ' => 'J',
				'Ｋ' => 'K',
				'Ｌ' => 'L',
				'Ｍ' => 'M',
				'Ｎ' => 'N',
				'Ｏ' => 'O',
				'Ｐ' => 'P',
				'Ｑ' => 'Q',
				'Ｒ' => 'R',
				'Ｓ' => 'S',
				'Ｔ' => 'T',
				'Ｕ' => 'U',
				'Ｖ' => 'V',
				'Ｗ' => 'W',
				'Ｘ' => 'X',
				'Ｙ' => 'Y',
				'Ｚ' => 'Z',
				'ａ' => 'a',
				'ｂ' => 'b',
				'ｃ' => 'c',
				'ｄ' => 'd',
				'ｅ' => 'e',
				'ｆ' => 'f',
				'ｇ' => 'g',
				'ｈ' => 'h',
				'ｉ' => 'i',
				'ｊ' => 'j',
				'ｋ' => 'k',
				'ｌ' => 'l',
				'ｍ' => 'm',
				'ｎ' => 'n',
				'ｏ' => 'o',
				'ｐ' => 'p',
				'ｑ' => 'q',
				'ｒ' => 'r',
				'ｓ' => 's',
				'ｔ' => 't',
				'ｕ' => 'u',
				'ｖ' => 'v',
				'ｗ' => 'w',
				'ｘ' => 'x',
				'ｙ' => 'y',
				'ｚ' => 'z',
				'（' => '(',
				'）' => ')',
				'〔' => '[',
				'〕' => ']',
				'【' => '[',
				'】' => ']',
				'〖' => '[',
				'〗' => ']',
				'“' => '"',
				'”' => '"',
				'‘' => '\'',
				'’' => '\'',
				'｛' => '{',
				'｝' => '}',
				'《' => '<',
				'》' => '>',
				'％' => '%',
				'＋' => '+',
				'—' => '-',
				'－' => '-',
				'～' => '-',
				'：' => ':',
				'。' => '.',
				'、' => ',',
				'，' => '.',
				'；' => ',',
				'？' => '?',
				'！' => '!',
				'…' => '-',
				'‖' => '|',
				'｜' => '|',
				'〃' => '"',
				'　' => ' ',
				'＄' => '$',
				'＠' => '@',
				'＃' => '#',
				'＾' => '^',
				'＆' => '&',
				'＊' => '*',
				'＂' => '"'
		);

		return strtr($str, $arr);
	}

	private static function http_build_query($formdata, $separator, $key = '', $prefix = '') {
		$rlt = '';
		foreach ($formdata as $k => $v)
		{
			if (is_array($v))
			{
				if ($key)
					$rlt .= self::http_build_query($v, $separator, $key . '[' . $k . ']', $prefix);
				else
					$rlt .= self::http_build_query($v, $separator, $k, $prefix);
			}
			else
			{
				if ($key)
					$rlt .= $prefix . $key . '[' . urlencode($k) . ']=' . urldecode($v) . '&';
				else
					$rlt .= $prefix . urldecode($k) . '=' . urldecode($v) . '&';
			}
		}

		return $rlt;
	}

	public static function genTree($items, $idname, $pidname) {
		foreach ($items as $item)
			$items[$item[$pidname]]['children'][$item[$idname]] = &$items[$item[$idname]];

		return isset($items[0]['children']) ? $items[0]['children'] : array();
	}


    /**
     * 构建树状HTML
     * @param $arr AR对象
     * @param string $sort 排序字段
     * @return array|string 返回自身或者HTML代码
     */
    public static function makeTree($arr, $sort = 'sort')
    {
        if(empty($arr)) return $arr;
        $id_name = get_class($arr[0]);
        //排序
        if(!empty($sort)){
            foreach ($arr as $v) {
                $d = $v->attributes;
                $_data[] = $d;
                $_sort[] = $d[$sort];
            }
            //排序
            array_multisort($_sort, SORT_ASC, $_data);
            $arr = $_data;
        }

        //分级
        foreach($arr as $v) {
            $_menu[$v['parent']][] = $v;
        }

        //构建
        return '<div class="dd" id="tree'.$id_name.'">'.self::_makeTree($_menu,0).'</div>';
    }

    protected static function _makeTree($arr,$id)
    {
        $out = '<ol class="dd-list">';
        foreach($arr[$id] as $v){
            $out .= '<li class="dd-item" data-id="'.$v['id'].'" data-status="'.$v['status'].'">';
            $out .= '<div class="dd-handle">'.$v['name'].'</div>';
            if(isset($arr[$v['id']])){$out .= self::_makeTree($arr,$v['id']);}
            $out .= '</li>';
        }
        $out .= '</ol>';
        return $out;
    }

	public static function filterEmoji($str){
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);
		return $str;
	}
	/**
	 * [格式化价格 0 面议 显示1位小数 .00 整数显示]
	 * @param  [string] $price [价格]
	 * @param  [string] $unit  [单位]
	 * @return [string]        [价格]
	 */
	public static function FormatPrice($price='',$unit='',$result='面议'){
		if($price == 0){
			return $result;
		}else{
			if($pos = strpos($price,'.')){
				if(substr($price,-2)=='00'){
					return ceil($price).$unit;
				}else{
					return round($price,1).$unit;
				}
			}else{
				return $price.$unit;
			}

		}
	}




}
