<?php
/**
 * 航加应用客户端类
 * 规定服务端返回格式
 * {
 *     code:200,
 *     version:3.2
 *     data:xxx
 * }
 * code:返回状态码|version:客户端版本|msg:服务端返回的数据
 * @author tivon
 * @date 2015-11-04
 */
class ClientMobile extends CApplicationComponent
{
    /**
     * 客户端组件版本
     * 服务端会携带最新所需Client版本号返回
     * 若客户端判断到非最新或不支持部分功能，则会提示更新此类
     * @var float
     */
    private $_version = 1.1;
    /**
     * curl实例
     * @var curl
     */
    private $_ch;

    /**
     * 生成请求签名，用于请求URM部分接口
     * @return array 返回数组参数
     */
    private function buildSignature()
    {
        $timestamp = time();
        $nonce = rand(10000,99999);
        $token = md5(SM::urmConfig()->siteID());
        $tmpArr = array($timestamp, $nonce, $token);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $signature = sha1($tmpStr);
        return array(
            'signature'=>$signature,
             'timestamp'=>$timestamp,
             'nonce'=>$nonce,
             'token'=>$token,
             'project'=>Yii::app()->name
        );
    }

    /**
     * 内置curl，不采用各应用中的，以保证稳定性
     * @return mixed 返回服务端返回的内容
     */
    private function get($url, $post = '', $extra = array())
    {
        $ch = curl_init(); // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $ms);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.122 Safari/537.36');
        return $this->execute($ch);
    }

    /**
     * 内置curl的post
     * @return mixed 返回服务端返回的内容
     */
    private function post()
    {
        $ch = curl_init(); // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        return $this->execute($ch);
    }

    /**
     * 执行curl{@see _curl}设置的请求
     * @param curl $ch curl实例
     * @return mixed 请求返回的结果，如果有错误则返回false
     */
    private function execute($ch)
    {
        $cookieValue = str_replace('&', '; ', http_build_query($_COOKIE));
        curl_setopt($ch, CURLOPT_COOKIE, $cookieValue);//携带用户cookie
        curl_setopt($ch, CURLOPT_USERAGENT, Yii::app()->request->userAgent);//携带用户useragent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不将返回的内容直接输出
        curl_setopt($ch, CURLOPT_HEADER, 1);//请求时携带头信息
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);//链接前等待的秒数，若时间内无响应则断开
        curl_setopt($ch, CURLOPT_TIMEOUT, $ms);//连接上后总的连接时间，超时后不管怎样都会断开
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            Yii::log('[HjClient]'.curl_error($ch));
            return false;
        }
        curl_close($ch);
        return $result;
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
     * 发送短信
     * @param  integer $phone 手机号码
     * @param  string $msg    短信内容
     * @param  integer $max   当日发送最大量，默认使用URM系统中设置的
     * @param  integer $interval 发送间隔时间，默认使用URM系统中设置的
     * @return boolean  发送成功true，失败false
     */
    public static function sendSms($phone, $msg, $max=100, $interval=0)
    {
        $api = Yii::app()->params['urmHost'].Yii::app()->params['smsApi'];
        // var_dump($api.'?'.http_build_query(self::buildSignature()));die;
        try
        {
            $params = array_merge(UrmTools::buildSignature(),array(
                'phone'=>$phone,
                'msg'=>$msg,
                'dailyMax' => $max,
                'sendInterval' => $interval,
                'project' => Yii::app()->name,
            ));
            $r = HttpHelper::get($api.'?'.http_build_query($params));
            $result = UrmTools::parseResult($r);
            // var_dump($result);die;
            return $result;//URM端使用response函数
        }
        catch(Exception $e)
        {
            Yii::log($e->getMessage(), 'error', 'mobile');
        }
    }

    /**
     * 随机验证码
     */
    public static function getCode($randLength = 6){
        $chars = '0123456789';
        $len = strlen($chars);
        $randStr = mt_rand(1,9);
        for ($i = 0; $i < $randLength - 1; $i++) {
            $randStr.=$chars[rand(0, $len - 1)];
        }
        return $randStr;
    }
}
