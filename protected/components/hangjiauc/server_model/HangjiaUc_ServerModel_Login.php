<?php
/**
 * 登录处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_Login extends HangjiaUc_ServerModel
{
    public $result = [
        'uid' => '{uid}',
        'username' => '{username}',
        'phone' => '{phone}',
        'icon' => '{icon}',
        'nickname' => '{nickname}',
        'html' => '{html}'
    ];

    /**
     * 可用用户名和手机号登录
     */
    public function run($username=null, $password=null, $isUid = false)
    {
        if($username!==null && $password!==null) {
            if($user = UcUserExt::findByPhone($username)) {
                $username = $user->uid;
                $isUid = true;
            }
            $info = $this->passport->login($username, $password, $isUid);
            if(isset($info['uid']) && $info['uid']>0) {
                $user = $user ? $user : UcUserExt::findByUid($info['uid']);
                $html = $this->passport->synLogin($info['uid']);
                return array_replace($this->result, [
                    'uid' => $info['uid'],
                    'username' => $info['username'],
                    'icon' => $info['icon'],
                    'html' => (string)$html,
                    'nickname' => $user&&$user->nick_name ? $user->nick_name : $info['username'],
                    'phone' => $user&&$user->phone ? $user->phone : '',
                    'unionid' => $user&&$user->unionid ? $user->unionid : '',
                    'openid' => $user&&$user->openid ? $user->openid : '',
                ]);
            } else {
                $this->log('登录失败', [
                    'passport返回内容' => (int)$info['uid'],
                    'username' => $username
                ]);
                return $this->error((int)$info['uid']);
            }
        } else {
            $this->log('登录失败，用户名或密码为null，请检查传参');
            return $this->error();
        }

    }

    public function getErrorCode($code)
    {
        $map = [
            -1 => 2001,
            -2 => 2002,
            -3 => 2003,
        ];
        return isset($map[$code]) ? $map[$code] : $code;
    }
}
