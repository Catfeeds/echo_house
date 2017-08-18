<?php
/**
 * 发送请求到Ucenter
 * 封装了uc客户端函数库
 * @author tivon
 * @version 2016-02-05
 */
class HjUc extends CComponent
{
    public $ucServer = '';
    public $key = '';
    public $charset = 'utf-8';
    public $appId = 0;
    public $ip = '';
    /**
     * UC缓存数据标识名称
     */
    const CACHE_NAME_APPS = 'uc_name_apps';

    //函数库移过来的一些常量
    const UC_CLIENT_RELEASE = '20110501';
    const UC_API_FUNC = 'uc_api_post';

    public function __construct($ucServer, $key, $appId=0, $charset = 'utf-8')
    {
        $this->ucServer = $ucServer;
        $this->key = $key;
        $this->appId = $appId;
        $this->charset = $charset;
    }

    /**
     * 获得指定用户头像
     * @param  integer $uids  用户uids，可通过逗号指定多个
     * @param  string $size 分big、middle、small三种尺寸
     * @return array
     */
    public function getAvatars($uids, $size='big')
    {
        $uids = explode(',', $uids);
        $avatars = array();
        foreach($uids as $uid){
            $avatars[$uid] = $this->ucServer.'/avatar.php?uid='.$uid.'&size='.$size;
        }
        return $avatars;
    }

    //**********************************接口函数BEGIN**********************************

    /**
     * 用户登录，具体参数见ucenter客户端函数库文档
     * @param  string  $username   用户名
     * @param  string  $password   密码
     * @return array
     */
    public function uc_user_login($username, $password, $isuid = 0, $checkques = 0, $questionid = '', $answer = '') {
        $isuid = intval($isuid);
        $return = call_user_func(array($this,self::UC_API_FUNC), 'user', 'login', array('username'=>$username, 'password'=>$password, 'isuid'=>$isuid, 'checkques'=>$checkques, 'questionid'=>$questionid, 'answer'=>$answer));
        return self::uc_unserialize($return);
    }

    /**
     * 用户注册
     * 前面几个参数具体见擌客户端函数库
     * @param  string $regip      注册用户ip
     * @return integer 见文档
     */
    public function uc_user_register($username, $password, $email, $questionid = '', $answer = '', $regip = '') {
    	return call_user_func(array($this, self::UC_API_FUNC), 'user', 'register', array('username'=>$username, 'password'=>$password, 'email'=>$email, 'questionid'=>$questionid, 'answer'=>$answer, 'regip' => $regip));
    }

    /**
     * 更新用户资料
     * 用户登录，具体参数见ucenter客户端函数库文档
     */
    public function uc_user_edit($username, $oldpw, $newpw, $email, $ignoreoldpw = 0, $questionid = '', $answer = '') {
    	return call_user_func(array($this, self::UC_API_FUNC), 'user', 'edit', array('username'=>$username, 'oldpw'=>$oldpw, 'newpw'=>$newpw, 'email'=>$email, 'ignoreoldpw'=>$ignoreoldpw, 'questionid'=>$questionid, 'answer'=>$answer));
    }

    /**
     * 同步登录到其他应用
     * 若成功，则返回的HTML务必要显示在页面上以完成对其他应用的通知
     * @param  integer $uid 用户id
     * @return string  HTML代码
     */
    public function uc_user_synlogin($uid) {
    	$uid = intval($uid);
        $apps = Yii::app()->cache->get(self::CACHE_NAME_APPS);
		if(count($apps) > 1 || true) {
			$return = $this->uc_api_post('user', 'synlogin', array('uid'=>$uid));
		} else {
			$return = '';
		}
    	return $return;
    }

    /**
     * 同步退出其他应用
     * 若成功，则返回的HTML务必要显示在页面上以完成对其他应用的通知
     * @param  integer $uid 用户id
     * @return string  HTML代码
     */
    public function uc_user_synlogout() {
        $apps = Yii::app()->cache->get(self::CACHE_NAME_APPS);
		if(count($apps) > 1 || true) {
			$return = $this->uc_api_post('user', 'synlogout', array());
		} else {
			$return = '';
		}
    	return $return;
    }

    /**
     * 获取用户数据，默认使用用户id获取
     * @param  string  $username 这里传入用户id
     * @param  integer $isuid    这是设默认值为1，表示使用用户id获取，设为0就用用户名获取
     * @return array
     */
    public function uc_get_user($username, $isuid=1) {
    	$return = call_user_func(array($this,self::UC_API_FUNC), 'user', 'get_user', array('username'=>$username, 'isuid'=>$isuid));
    	return self::uc_unserialize($return);
    }

    //**********************************工具函数BEGIN**********************************

    private function uc_api_post($module, $action, $arg = array()) {
    	$s = $sep = '';
    	foreach($arg as $k => $v) {
            //转编码
            $k = mb_convert_encoding($k,$this->charset,'utf-8');
    		$k = urlencode($k);
    		if(is_array($v)) {
    			$s2 = $sep2 = '';
    			foreach($v as $k2 => $v2) {
                    //转编码
                    $k2 = mb_convert_encoding($k2,$this->charset,'utf-8');
    				$k2 = urlencode($k2);
                    //转编码
                    $tmp = mb_convert_encoding($this->uc_stripslashed($v2),$this->charset,'utf-8');
    				$s2 .= "$sep2{$k}[$k2]=".urlencode($tmp);
    				$sep2 = '&';
    			}
    			$s .= $sep.$s2;
    		} else {
                //转编码
                $v = mb_convert_encoding($this->uc_stripslashes($v),$this->charset,'utf-8');
    			$s .= "$sep$k=".urlencode($v);
    		}
    		$sep = '&';
    	}
    	$postdata = $this->uc_api_requestdata($module, $action, $s);
    	$result = $this->uc_fopen2($this->ucServer.'/index.php', 500000, $postdata, '', TRUE, $this->ip, 20);
        return mb_convert_encoding($result, 'utf-8', $this->charset);
    }

    private function uc_stripslashes($string) {
    	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    	if(MAGIC_QUOTES_GPC) {
    		return stripslashes($string);
    	} else {
    		return $string;
    	}
    }

    private function uc_api_requestdata($module, $action, $arg='', $extra='') {
    	$input = $this->uc_api_input($arg);
    	$post = "m=$module&a=$action&inajax=2&release=".self::UC_CLIENT_RELEASE."&input=$input&appid=".$this->appId.$extra;
    	return $post;
    }

    private function uc_api_input($data) {
    	$s = urlencode(self::uc_authcode($data.'&agent='.md5($_SERVER['HTTP_USER_AGENT'])."&time=".time(), 'ENCODE', $this->key));
    	return $s;
    }

    private function uc_fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
    	$__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
    	if($__times__ > 2) {
    		return '';
    	}
    	$url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
    	return $this->uc_fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
    }

    private function uc_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
    	$return = '';
    	$matches = parse_url($url);
    	!isset($matches['host']) && $matches['host'] = '';
    	!isset($matches['path']) && $matches['path'] = '';
    	!isset($matches['query']) && $matches['query'] = '';
    	!isset($matches['port']) && $matches['port'] = '';
    	$host = $matches['host'];
    	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    	$port = !empty($matches['port']) ? $matches['port'] : 80;
    	if($post) {
    		$out = "POST $path HTTP/1.0\r\n";
    		$out .= "Accept: */*\r\n";
    		//$out .= "Referer: $boardurl\r\n";
    		$out .= "Accept-Language: zh-cn\r\n";
    		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
    		$out .= "Host: $host\r\n";
    		$out .= 'Content-Length: '.strlen($post)."\r\n";
    		$out .= "Connection: Close\r\n";
    		$out .= "Cache-Control: no-cache\r\n";
    		$out .= "Cookie: $cookie\r\n\r\n";
    		$out .= $post;
    	} else {
    		$out = "GET $path HTTP/1.0\r\n";
    		$out .= "Accept: */*\r\n";
    		//$out .= "Referer: $boardurl\r\n";
    		$out .= "Accept-Language: zh-cn\r\n";
    		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
    		$out .= "Host: $host\r\n";
    		$out .= "Connection: Close\r\n";
    		$out .= "Cookie: $cookie\r\n\r\n";
    	}

    	if(function_exists('fsockopen')) {
    		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    	} elseif (function_exists('pfsockopen')) {
    		$fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    	} else {
    		$fp = false;
    	}

    	if(!$fp) {
    		return '';
    	} else {
    		stream_set_blocking($fp, $block);
    		stream_set_timeout($fp, $timeout);
    		@fwrite($fp, $out);
    		$status = stream_get_meta_data($fp);
    		if(!$status['timed_out']) {
    			while (!feof($fp)) {
    				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
    					break;
    				}
    			}

    			$stop = false;
    			while(!feof($fp) && !$stop) {
    				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
    				$return .= $data;
    				if($limit) {
    					$limit -= strlen($data);
    					$stop = $limit <= 0;
    				}
    			}
    		}
    		@fclose($fp);
    		return $return;
    	}
    }

    /**
     * ucenter通信数据加密解密函数
     */
    public function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    	$ckey_length = 4;

    	$key = md5($key ? $key : $this->key);//修改处,对比官方
    	$keya = md5(substr($key, 0, 16));
    	$keyb = md5(substr($key, 16, 16));
    	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    	$cryptkey = $keya.md5($keya.$keyc);
    	$key_length = strlen($cryptkey);

    	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    	$string_length = strlen($string);

    	$result = '';
    	$box = range(0, 255);

    	$rndkey = array();
    	for($i = 0; $i <= 255; $i++) {
    		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
    	}

    	for($j = $i = 0; $i < 256; $i++) {
    		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
    		$tmp = $box[$i];
    		$box[$i] = $box[$j];
    		$box[$j] = $tmp;
    	}

    	for($a = $j = $i = 0; $i < $string_length; $i++) {
    		$a = ($a + 1) % 256;
    		$j = ($j + $box[$a]) % 256;
    		$tmp = $box[$a];
    		$box[$a] = $box[$j];
    		$box[$j] = $tmp;
    		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    	}

    	if($operation == 'DECODE') {
    		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                $result = mb_convert_encoding($result, 'utf-8', $this->charset);
    			return substr($result, 26);
    		} else {
    			return '';
    		}
    	} else {
    		return $keyc.str_replace('=', '', base64_encode($result));
    	}
    }

    public function uc_serialize($arr, $htmlon = 0) {
    	include_once __DIR__.'/lib/xml.class.php';
    	return xml_serialize($arr, $htmlon);
    }

    public function uc_unserialize($s)
    {
    	include_once __DIR__.'/lib/xml.class.php';
    	return xml_unserialize($s);
    }
}
