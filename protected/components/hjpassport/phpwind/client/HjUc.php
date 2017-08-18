<?php
/**
 * 发送请求到Ucenter
 * 封装了uc客户端函数库
 * @author tivon
 * @version 2016-02-05
 */
class HjUc extends CComponent
{
    public $ucServer;
    public $appId;
    public $key;
    private $_synloginHtml = '';

    private $_uc;

    public function __construct($ucServer, $appId, $key)
    {
        $this->ucServer = trim($ucServer,'\/').'/hack.php?H_name=hangjiaapi&action=ajax';
        $this->appId = $appId;
        $this->key = $key;
    }

    /**
     * 登录
     * @param  [type] $username [description]
     * @param  [type] $password [description]
     * @return 成功返回uid，失败返回等于null的值
     */
    public function login($username, $password, $isUid=false)
    {
        $result = $this->postApi('member','login',array(
            'pwuser' => $username,
            'pwpwd' => $password,
            'isuid' => $isUid===false ? 0 :1
        ));
        $data = CJSON::decode($result);
        $info = array(
            'uid' => 0,
            'username' => '',
            'icon' => '',
        );
        if(isset($data['uid'])&&$data['uid']){
            if(isset($data['synlogin'])){
                $this->_synloginHtml = $data['synlogin'];
            }
            $info['uid'] = $data['uid'];
            if(isset($data['username'])) $info['username'] = $data['username'];
            if(isset($data['icon'])) $info['icon'] = $data['icon'];
        }
        return $info;
    }

    /**
     * 同步登陆
     * @param  [type] $uid 该参数在pw中不需要用
     * @return string   直接获得HTML代码
     */
    public function synLogin($uid=0)
    {
        $result = $this->postApi('member','synLogin',array(
            'uid' => $uid,
        ));
        $data = $result->body;
        if($data){
            return $data;
        } else {
            return '';
        }
    }

    public function synLogout()
    {
        $result = $this->postApi('member', 'synLogout');
        return $result->body ? $result->body : '';
    }

    public function regiseter($username, $password, $email='')
    {
        $result = $this->postApi('member', 'register', [
            'username' => $username,
            'password' => $password,
            'email' => $email,
        ]);
        return $result->body ? $result->body : '';
    }

    public function updatePwd($username, $newPwd, $oldPwd=null, $isUid=false, $ignoreOldPwd=false)
    {
        $result = $this->postApi('member', 'updatePwd', [
            'username' => $username,
            'newpwd' => $newPwd,
            'oldpwd' => $oldPwd,
            'ignorepwd' => $ignoreOldPwd===false ? 0 :1,
            'isuid' => $isUid===false ? 0 :1,
        ]);
        return $result->body!=='' ? $result->body : '';
    }

    /**
     * 获得头像
     * @param  string $uids 用户uid，可通过逗号分割来指定多个用户
     * @return array  多个头像数组，数组key必须为用户uid,value为头像地址，格式如下
     * array(
     * 		42 => 'http://xxxxx.jpg',
     * 		121 => 'http://aaaaa.jpg',
     * )
     */
    public function getAvatars($uids)
    {
        // $uids = 1;
        $result = $this->getUserInfoes($uids);
        $data = array();
        foreach($result as $v){
            $data[$v['uid']] = $v['icon'];
        }
        return $data;
    }

    /**
     * 根据用户uids获得用户基本信息
     * @param  string $uids 用户uid，可通过逗号分割来指定多个用户
     * @return array  多个用户信息数组，格式如下
     * array(
     * 		uid => 3,
     * 		username => 'admin',
     * 		icon => 'http://xxxx.jpg',
     * )
     */
    public function getUserInfoes($uids, $isUid = true)
    {
        $uids = implode(',', explode(',',trim($uids,',')));
        $result = $this->postApi('member', 'userinfoes', array(
            'uids' => $uids,
            'isuid' => $isUid ? 1 : 0,
        ));
        if($data = CJSON::decode($result)){
            return $data;
        }
        return $data;
    }

    /**
     * 发送请求服务端地址
     * @return [type] [description]
     */
    public function postApi($controller, $action, $params = array())
    {
        $code['route'] = $controller.'/'.$action;
        $code['time'] = time();
        $code = $this->authcode(http_build_query($code), 'ENCODE');
        return $this->getCurl()->post($this->ucServer, array(
            'code' => $code
        )+$params);
    }

    /**
     * 获取curl
     */
    public function getCurl()
    {
        Yii::import('hjpassport.phpwind.curl.*');
        return new Curl();
    }

    /**
     * 加密解密函数
     */
    function authcode($string, $operation = 'DECODE', $key = 'hangjia', $expiry = 0)
    {
        $ckey_length = 4;

        $key = md5($key ? $key : $this->key);
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
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}
