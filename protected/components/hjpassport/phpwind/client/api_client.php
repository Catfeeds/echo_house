<?php
class api_client {

	var $type;
	var $apikey;
	var $charset;
	var $db;
	var $classdb;
    var $siteappkey;

    public function __construct($key, $appId)
    {
        $this->apikey = $key;
        $this->siteappkey = $appId;
		$this->type		= '';
		$this->db		= null;
		$this->classdb	= array();
		$this->charset	= S_CHARSET;
    }

	function run($request) {
		// global $config;
		$request = $this->strips($request);
		if (isset($request['type']) && $request['type'] == 'uc') {
			$this->type		= 'uc';
			// $this->apikey	= $config['uc_key'];
		} else {
			$this->type		= 'app';
			// $this->apikey	= $config['uc_appid'];
            // $this->siteappkey = $config['uc_key'];
		}
		/***
		if ($this->type == 'app' && !$GLOBALS['o_appifopen']) {
			return new ErrorMsg(API_CLOSED, 'App Closed');
		}
		***/
		ksort($request);
		reset($request);
		$arg = '';
		foreach ($request as $key => $value) {
			if ($value && $key != 'sig') {
				$arg .= "$key=$value&";
			}
		}

		if (empty($this->apikey) || md5($arg . $this->apikey) != @$request['sig']) {
			return new ErrorMsg(API_SIGN_ERROR, 'Error Sign');
		}
		$mode	= $request['mode'];
		$method	= $request['method'];

		$params = isset($request['params']) ? unserialize($request['params']) : array();

        if (isset($params['appthreads'])) {
			require_once(R_P.'api/pw_api/class_json.php');
			$json = new Services_JSON(true);
			$params['appthreads'] = $json->decode(@gzuncompress($params['appthreads']));
        }

		if ($params && isset($request['charset'])) {
			$params = $this->pwConvert($params, $this->charset, $request['charset']);
		}

        return array('action'=>$method, 'params'=>$params);//后期更改的
		return $this->callback($mode, $method, $params);
	}

	public function pwConvert($str, $toEncoding, $fromEncoding, $ifMb = true) {
		if (strtolower($toEncoding) == strtolower($fromEncoding)) {return $str;}
		if (is_array($str)) {
			foreach ($str as $key => $value) {
				$str[$key] = $this->pwConvert($value, $toEncoding, $fromEncoding, $ifMb);
			}
			return $str;
		} else {
			return mb_convert_encoding($str, $toEncoding, $fromEncoding);
		}
	}

	function callback($mode, $method, $params) {

		if (!isset($this->classdb[$mode])) {
			if (!file_exists(R_P.'api/pw_api/class_' . $mode . '.php')) {
				return new ErrorMsg(API_MODE_NOT_EXISTS, "Class($mode) Not Exists");
			}
			require_once Pcv(R_P.'api/pw_api/class_' . $mode . '.php');
			$this->classdb[$mode] = new $mode($this);
		}

		if (!method_exists($this->classdb[$mode], $method)) {
			return new ErrorMsg(API_METHOD_NOT_EXISTS, "Method($method of $mode) Not Exists");
		}
		!is_array($params) && $params = array();

		return @call_user_func_array(array(&$this->classdb[$mode], $method), $params);
	}

	function dataFormat($data) {
		$res = array(
			'charset' => $this->charset
		);
		if (strtolower(get_class($data)) == 'apiresponse') {
			$res['result'] = $data->getResult();
		} else {
			$res['errCode'] = $data->getErrCode();
			$res['errMessage'] = $data->getErrMessage();
		}
		return serialize($res);
	}

	function strips($param) {
		if (is_array($param)) {
			foreach ($param as $key => $value) {
				$param[$key] = $this->strips($value);
			}
		} else {
			$param = stripslashes($param);
		}
		return $param;
	}

	function strcode($string, $encode = true) {
		!$encode && $string = base64_decode($string);
		$code = '';
		$key  = substr(md5($_SERVER['HTTP_USER_AGENT'] . $this->apikey),8,18);
		$keylen = strlen($key);
		$strlen = strlen($string);
		for ($i = 0; $i < $strlen; $i++) {
			$k		= $i % $keylen;
			$code  .= $string[$i] ^ $key[$k];
		}
		return ($encode ? base64_encode($code) : $code);
	}
}
