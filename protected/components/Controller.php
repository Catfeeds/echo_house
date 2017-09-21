<?php
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
include_once("./CCPRestSmsSDK.php");
/**
 * @author wibaqiu
 * @date 2015-09-07
 */
class Controller extends CController
{

	//主帐号,对应开官网发者主账号下的 ACCOUNT SID
	public $accountSid= '8a48b5514c6a0c54014c780d1e0c0955';

	//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
	public $accountToken= '102fd7705b4144479a672933d3a77f88';

	//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
	//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
	public $appId='8a216da85e4be3b3015e510cf9ed02a4';

	//请求地址
	//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
	//生产环境（用户应用上线使用）：app.cloopen.com
	public $serverIP='app.cloopen.com';


	//请求端口，生产环境和沙盒环境一致
	public $serverPort='8883';

	//REST版本号，在官网文档REST介绍中获得。
	public $softVersion='2013-12-26';
		/**
	 * 站点配置
	 * @var array
	 */
	public $siteTag,$siteArea,$siteStreet;

	public $imgstyle = 'height:390px';

	public $iswap = 0;

	public $ispad = 0;

	public $hideloginhead = 0;

	/**
	 * 初始化操作
	 */
	public function init()
	{
		parent::init();
		//判断使用主题
		Yii::app()->theme = 'v2';
		if($this->redirectWap())
		{
			$this->imgstyle = '';
			$this->iswap = 1;
		// 	if(Yii::app()->request->getUrl() == '/')
		// 		$this->redirect('/wap');
		// 	else
		// 		$this->redirect(str_replace('/home','/wap',Yii::app()->request->getUrl()));
		// 	Yii::app()->end();
		}
	}

	/**
     * 重写以配合新版升级。2016年8月23日完成全部升级，将两版代码弄成一版的
     * @param  [type] $actionID [description]
     * @return Action
     */
    public function createAction($actionID)
	{
		//存在两版代码时可以用下面方法
        // if($this->getIsLatest()){
        //     $action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
        //     if($action!==null) return $action;
        // }
		return parent::createAction($actionID);
	}

	/**
     * 是否升级最新
     * 该函数将在新版上线一段时间失效，上线是有时间限制的，各个站点需要在指定时间段内自行升级
     * 过了时间所有站点将会被自动更新
     * @return boolean true表示已升级，false表示未升级
     */
    public function getIsLatest()
    {
		return true;
    }

	/**
	 * 获取分站选项
	 * @return array SubstationExt[]
	 */
	public function getSubstations()
	{
		$defaultArea = (object)array('pinyin'=>'');
		$default = (object)array('name'=>'主站','area_id'=>0,'area'=>$defaultArea);
		$substations[0] = $default;
		$substations = $substations+SubstationExt::model()->findAll(array('index'=>'area_id','with'=>array('area')));
		return $substations;
	}

	/**
	 * 设置停留在PC的Cookie标识，因为默认行为是WAP访问PC会跳转PC。
	 */
	protected function setStayInPc()
	{
		$cookie = new CHttpCookie('stayInPc', '1');
		$cookie->expire = time()+3600*12;
		Yii::app()->request->cookies['stayInPc'] = $cookie;
	}

	/**
	 * 是否停留在pc
	 * @return boolean 返回true则表示停留，false表示按默认行为
	 */
	protected function getStayInPc()
	{
		$cookie = Yii::app()->request->getCookies();
		return isset($cookie['stayInPc']) && $cookie['stayInPc']=='1';
	}

	/**
	 * 清除停留在PC的标识
	 */
	protected function unsetStayInPc()
	{
		$cookie = Yii::app()->request->getCookies();
		if(isset($cookie['stayInPc'])){
			unset($cookie['stayInPc']);
		}
	}

	/*
     * 判断是否要跳转WAP
     * @return boolean true表示跳转，false表示不跳转
     */
    public function redirectWap()
	{
        require_once Yii::getPathOfAlias('application.vendor') . DIRECTORY_SEPARATOR . 'Mobile_Detect.php';
        $detect = new Mobile_Detect();
        if($detect->isTablet())
        	$this->ispad = 1;
		return $this->getIsInQianFan() || ($detect->isMobile() || $detect->isTablet() && (!$this->getStayInPc() && $this->getModule()->id!='wap'));
    }

	/**
	 * 是否在千帆客户端内
	 * @return boolean
	 */
	public function getIsInQianFan()
	{
		return (strpos(Yii::app()->request->getUserAgent(),'QianFan')!==false);
	}

	/**
     * 设置提示以及跳转
     * 当处理ajax请求使用该函数时，设置$type为success时会调用setFlash以及response函数
     * 客户端可根据response返回的json数据刷新页面会得到提示信息
     * response返回json格式如下：
     * [
     *     code: true,
     *     msg:'修改成功'
     * ]
     * @param string $msg       自定义信息内容,为空只跳转
     * @param string $type      success,info,warning,error不同的类型颜色不一样
     * @param string $redirect  自定义跳转，值为false时不跳转，值为true时跳转到{@see Yii::app()->user->returnUrl}，值为
     *                          数组时跳转到解析数组后得到的模块控制器方法，如数组"array('/admin/recom/list')"则跳转到
     *                          admin模块、recom控制器、list方法
     * @return null             没有返回值
     */
    public function setMessage($msg = '', $type = 'success', $redirect = false) {
        if(!in_array($type, array('success', 'info', 'warning', 'error')))
            $type = 'success';
        if($redirect===true)
            $redirect = Yii::app()->user->getReturnUrl(Yii::app()->request->getUrlReferrer());
        $msg && Yii::app()->user->setFlash($type, $msg);
        if(Yii::app()->request->getIsAjaxRequest())
            $this->response($type=='success', $msg);
        $redirect!==false && $this->redirect($redirect);
    }

	/**
	 * 输出response
	 * @param  string $code code
	 * @param  string $msg  msg
	 * @param  string $type 输出数据格式类型，暂时有json、xml
	 */
	public function response($code, $msg, $type="json",$jsoncallback='') {
		if(in_array(strtolower($type),array('json','xml','jsonp')))
		{
			if (!headers_sent())
				header("Content-Type: application/" . $type . "; charset=utf-8");
			switch ($type)
			{
				case 'xml':
					echo self::xml_encode(array('code' => $code, 'msg' => $msg));
					break;
				case 'jsonp':
                    echo $jsoncallback.'('.CJSON::encode(array('code'=>$code, 'msg'=>$msg)).')';
                    break;
				case 'json':
				default:
					echo CJSON::encode(array('code' => $code, 'msg' => $msg));
					break;
			}
			Yii::app()->end(0);
		}
		else
			throw new CException($type.' is not supported');
	}

	/**
	 * xml
	 */
	private static function xml_encode($data, $encoding = 'utf-8', $root = 'yii') {
		$xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
		$xml .= '<' . $root . '>';
		$xml .= self::data_to_xml($data);
		$xml .= '</' . $root . '>';
		return $xml;
	}

	/**
	 * 防XSS攻击
	 * @param  string $string 传入的字符串
	 * @return string 替换后的文字文案
	 */
	public function cleanXss($string)
	{
		if (! is_array ( $string ))
		{
			$string = trim ( $string );
			$string = strip_tags ( $string );
			$string = htmlspecialchars ( $string );
			$string = str_replace ( array ('"', "\\", "'", "/", "..", "../", "./", "//"), '', $string );
			$no = '/%0[0-8bcef]/';
			$string = preg_replace ( $no, '', $string );
			$no = '/%1[0-9a-f]/';
			$string = preg_replace ( $no, '', $string );
			$no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
			$string = preg_replace ( $no, '', $string );
			return $string;
		}
		$keys = array_keys ( $string );
		foreach ( $keys as $key )
		{
			clean_xss ( $string [$key] );
		}
		return $string;
	}

	/**
	 * [rapFrame rap接口框架]
	 * @return [type] [description]
	 */
	public function rapFrame()
	{
		$arr = ['msg'=>'','status'=>'success','data'=>[]];
		return $arr;
	}

		/**
     * [actionQnUpload 七牛图片上传]
     * @return [type] [description]
     */
    public function createQnKey()
    {
        $auth = new Auth(Yii::app()->file->accessKey,Yii::app()->file->secretKey);
        $policy = array(
            'mimeLimit'=>'image/*',
            'fsizeLimit'=>10000000,
            'saveKey'=>Yii::app()->file->createQiniuKey(),
        );
        $token = $auth->uploadToken(Yii::app()->file->bucket,null,3600,$policy);
        return $token;
    }

    public function sfImage($img='',$refer = '')
    {
    	$opt=array("http"=>array("header"=>"Referer: " . $refer,"timeout"=>3)); 
		$context=stream_context_create($opt);
	    set_error_handler(  
	        create_function(  
	            '$severity, $message, $file, $line',  
	            'throw new ErrorException($message, $severity, $severity, $file, $line);'  
	        )  
	    );  
	      
	    try {  
	        $file_contents = file_get_contents($img,false, $context);
	    }  
	    catch (Exception $e) {  
	        echo $e->getMessage();  
	    }  
  
    	restore_error_handler(); 
		// try{
		// 	$file_contents = file_get_contents($img,false, $context);
		// } catch(Exception $e){
		// 	echo $e->getMessage();
		// 	return '';
		// }
		if(!isset($file_contents)||!$file_contents) {
			return;
		}
		$name = str_replace('.', '', microtime(1)) . rand(100000,999999).'.jpg';
		$path = '/mnt/sfimages\/';
		if (! file_exists ( $path )) 
        	mkdir ( "$path", 0777, true );
		file_put_contents($path.$name, $file_contents);
		$fileName = Yii::app()->file->getFilePath().str_replace('.', '', microtime(1)) . rand(100000,999999).'.jpg';

		$upManager = new UploadManager();
		try{
			list($ret, $error) = $upManager->putFile($this->createQnKey(),$fileName, $path.$name);
		} catch(Exception $e) {
			echo $e->getMessage();
			return '';
		}
	    
	    if(!$error){
	    	unlink($path.$name);
	    	return $ret['key'];
	    }
	    else
	    	return '';
    }

    function unicode_decode($name){
	    $json = '{"str":"'.$name.'"}';
	    $arr = json_decode($json,true);
	    if(empty($arr)) return '';
	    return $arr['str'];
	}

	  	function characet($data)
  	{
	  	if( !empty($data) ){
		    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
		    if( $fileType != 'UTF-8'){
		      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
		    }
		}
		return $data;
	}
	public function post($url, $post_data = '', $timeout = 5){//curl
 
        $ch = curl_init();
 
        curl_setopt ($ch, CURLOPT_URL, $url);
 
        curl_setopt ($ch, CURLOPT_POST, 1);
 
        if($post_data != ''){
 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 
        }
 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
 
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 
        curl_setopt($ch, CURLOPT_HEADER, false);
 
        $file_contents = curl_exec($ch);
 
        curl_close($ch);
 
        return $file_contents;
 
    }

    function sendTemplateSMS($to,$datas,$tempId)
	{
	     // 初始化REST SDK
	     global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
	     $rest = new REST($serverIP,$serverPort,$softVersion);
	     $rest->setAccount($accountSid,$accountToken);
	     $rest->setAppId($appId);
	    
	     // 发送模板短信
	     echo "Sending TemplateSMS to $to <br/>";
	     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
	     if($result == NULL ) {
	         echo "result error!";
	         break;
	     }
	     if($result->statusCode!=0) {
	         echo "error code :" . $result->statusCode . "<br>";
	         echo "error msg :" . $result->statusMsg . "<br>";
	         //TODO 添加错误处理逻辑
	     }else{
	         echo "Sendind TemplateSMS success!<br/>";
	         // 获取返回信息
	         $smsmessage = $result->TemplateSMS;
	         echo "dateCreated:".$smsmessage->dateCreated."<br/>";
	         echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
	         //TODO 添加成功处理逻辑
	     }
	}

	public function sendNotice($words='',$uid='',$is_mak=0)
    {
    	if($is_mak) {
    		$uidsarr = SiteExt::getAttr('qjpz','ouruids');
    		$uid = str_replace(' ', ',', $uidsarr);
    	}
        $url = 'http://jj58.qianfanapi.com/api1_2/easemob/send-message';
        $key = '495e6105d4146af1d36053c1034bc819';
        $arr = ['sender'=>SiteExt::getAttr('qjpz','kfuid'),'receiver'=>$uid,'message'=>$words];
        $res = $this->get_response($key,$url,[],$arr);
        return $res;
    }

    public function get_response($secret_key, $url, $get_params, $post_data = array())
    {
        $nonce         = rand(10000, 99999);
        $timestamp  = time();
        $array = array($nonce, $timestamp, $secret_key);
        sort($array, SORT_STRING);
        $token = md5(implode($array));
        $params['nonce'] = $nonce;
        $params['timestamp'] = $timestamp;
        $params['token']     = $token;
        $params = array_merge($params,$get_params);  
        $url .= '?';
        foreach ($params as $k => $v) 
        {
            $url .= $k .'='. $v . '&';
        }
        $url = rtrim($url,'&');   
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);   
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);   
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);   
        curl_setopt($curlHandle, CURLOPT_POST, count($post_data));  
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_data);  
        $data = curl_exec($curlHandle);    
        $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);    
        return $data;
    }

}
