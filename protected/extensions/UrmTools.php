<?php 
/**
 * 用于垂直项目与URM后台的通信交互
 * @author tivon
 * @date   2015-07-10
 */
class UrmTools
{
	/**
	 * 生成请求签名，用于请求URM部分接口
	 * @return array
	 */
	public static function buildSignature()
	{
		$timestamp = time();
		$nonce = rand(10000,99999);
		$token = md5(SM::urmConfig()->siteID());
		$tmpArr = array($timestamp, $nonce, $token);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$signature = sha1($tmpStr);
		return array('signature'=>$signature, 'timestamp'=>$timestamp,'nonce'=>$nonce,'token'=>$token,'project'=>Yii::app()->name);
	}

	/**
	 * 解析请求URM返回的数据，一般情况下URM使用内置response函数返回消息
	 * @param  array $r 返回的结果集，一般情况下使用内置的HttpHelper请求返回的结果集
	 * @return mixed   返回所需的数据内容
	 */
	public static function parseResult($r)
	{
		if(empty($r['content']))
			return null;
		elseif(is_array(CJSON::decode($r['content'])))
			return CJSON::decode($r['content']);
		else
			return $r['content'];
	}

	/**
	 * 获得URM配置
	 * @return mixed
	 */
	public static function getSiteConfig()
	{
		HttpHelper::model;
	}
}
?>