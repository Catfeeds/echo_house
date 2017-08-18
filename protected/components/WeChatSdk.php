<?php
/**
 * 获取微信相关参数的类
 */
class WeChatSdk extends CComponent
{
    private $appId;
    private $appSecret;
    private $url;
    const TICKET_CACHE_KEY = 'WeChat.ticket';
    const ACCESS_TOKEN_CACHE_KEY = 'WeChat.accessToken';

    public function __construct($appId, $appSecret, $url='')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        if(!$url)
            $this->url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        else
            $this->url = $url;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();


        $url = $this->url;

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
          "appId"     => $this->appId,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = Yii::app()->cache->get(self::TICKET_CACHE_KEY) ? Yii::app()->cache->get(self::TICKET_CACHE_KEY) : (object)array('expire_time'=>0,'jsapi_ticket'=>'');
        $ticket = '';
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            if (isset($res->ticket)) {
                $data->expire_time = time() + 7000;
                $ticket = $data->jsapi_ticket = $res->ticket;
                Yii::app()->cache->set(self::TICKET_CACHE_KEY, $data, 7000);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        // $data = json_decode(file_get_contents("access_token.json"));
        $data = Yii::app()->cache->get(self::ACCESS_TOKEN_CACHE_KEY) ? Yii::app()->cache->get(self::ACCESS_TOKEN_CACHE_KEY) : (object)array('expire_time'=>0,'access_token'=>'');
        $access_token = '';
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            if (isset($res->access_token)) {
                $data->expire_time = time() + 7000;
                $access_token = $data->access_token = $res->access_token;
                Yii::app()->cache->set(self::ACCESS_TOKEN_CACHE_KEY, $data, 7000);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}
