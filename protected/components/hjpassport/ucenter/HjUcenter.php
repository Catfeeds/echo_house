<?php
class HjUcenter extends Ppt
{
    public $ucServer = '';
    public $key = '';
    public $charset = 'utf-8';
    public $appId = 0;

    private $_uc;

    public function __construct(array $options)
    {
        foreach($options as $name=>$value){
            if(isset($this->$name)) $this->$name = $value;
        }
        $this->_uc = Yii::createComponent('hjpassport.ucenter.16.HjUc',$this->ucServer, $this->key, $this->appId, $this->charset);
    }

    /**
     * 用户登录
     * @param  string  $username 用户名或uid
     * @param  string  $password 明文密码
     * @param  boolean $isUid    是否用uid登录，true为用uid登录，false为用用户名登录
     * @return [type]            [description]
     */
    public function login($username, $password, $isUid=false)
    {
        $isUid = $isUid===false ? 0 :1;
        @list($uid, $username, $password, $email) = $this->_uc->uc_user_login($username, $password, $isUid);
        $info = array(
            'uid' => $uid,
            'username' => '',
            'icon' => '',
        );
        if( $uid>0 ){
            $avatars = $this->getAvatars($uid);
            $icon = array_pop($avatars);
            $info = array(
                'uid' => $uid,
                'username' => $username,
                'icon' => $icon ? $icon : '',
            );
        }elseif( $uid==-1 ){
            $this->addError('用户不存在或被删除');
        }elseif( $uid==-2 ){
            $this->addError('密码错误');
        }else{
            $this->addError('未知错误');
        }
        return $info;
    }

    /**
     * 用户同步登陆
     * @param  [type] $uid [description]
     * @return string  返回HTML代码
     */
    public function synLogin($uid)
    {
        return $this->_uc->uc_user_synlogin($uid);
    }

    /**
     * 同步退出
     * @return string 返回HTML代码
     */
    public function synLogout()
    {
        return $this->_uc->uc_user_synlogout();
    }

    /**
     * 根据uid或用户名获取用户信息
     * @param  string  $uids  uid或用户名 均以英文逗号分隔，如"31,24,121,325"或"admin,veitor,测试帐号"
     * @param  boolean $isUid 使用用户uid获取时为true，使用用户名获取时为false
     * @return array   [13=>['uid'=>13', 'username'=>'admin', 'avatar'=>'xxx.jpg']]
     */
    public function getUserInfoes($uids, $isUid=true)
    {
        $uids = explode(',', trim($uids,','));
        $data = array();
        foreach($uids as $uid){
            $user = $this->_uc->uc_get_user($uid, $isUid?1:0);
            if($user){
                $avatar = $this->getAvatars($user[0]);
                $data[$user[0]] = array(
                    'uid' => $user[0],
                    'username' => $user[1],
                    'icon' => $avatar[$user[0]],
                );
            }
        }
        if(empty($data)) {
            $this->addError('用户不存在');
        }
        return $data;
    }

    /**
     * 获取头像
     * @param  integer $uid  用户uid
     * @param  string $size 可选项small、middle、big
     * @return array
     */
    public function getAvatars($uid)
    {
        $uid = (int)$uid;
        return $this->_uc->getAvatars($uid);
    }

    /**
     * 注册用户
     * @param $username 注册的用户名
     * @param $password 注册的密码（明文）
     * @param $username 注册的邮箱地址（可为空，程序会根据用户名自动生成）
     * @return boolean 返回用户uid，若uid小于0则会有相关错误记录，通过{@see getErrors()}获取错误信息
     */
    public function register($username, $password, $email='')
    {
        if($email==''){
            $email = $this->generateEmailByUsername($username);
        }
        $uid = $this->_uc->uc_user_register($username, $password, $email);
        if($uid<=0){
            switch($uid) {
                case -1:
                    $error = '用户名不合法';
                    break;
                case -2:
                    $error = '包含不允许注册的词语';
                    break;
                case -3:
                    $error = '用户名已经存在';
                    break;
                case -4:
                    $error = 'Email格式有误';
                    break;
                case -5:
                    $error = 'Email不允许注册';
                    break;
                case -6:
                    $error = '该Email已经被注册';
                    break;
                default:
                    $error = '未知错误';
            }
            $this->addError($error);
        }
        return $uid;
    }

    /**
     * 修改用户密码
     * @param  string $username 用户名
     * @param  string $newPwd   新密码
     * @param  string $oldPwd   旧密码（可为空，不验证）
     * @return boolean 修改成功返回1，修改失败返回小于1的值，具体错误通过{@see getErrors()}获取
     */
    public function updatePwd($username, $newPwd, $oldPwd = null, $isUid=false)
    {
        if($isUid) {
            $u = $this->getUserInfoes($username);
            if(is_array($u) && $u && $u = array_pop($u)) {
                $username = $u['username'];
            }
        }
        $ignoreOldPwd = $oldPwd === null ? 1 : 0;
        $id = $this->_uc->uc_user_edit($username, $oldPwd, $newPwd, '', $ignoreOldPwd);
        if($id<=0){
            switch($id){
                case 0:
                    //no break;
                case -7:
                    $error = '没有做任何修改';
                    break;
                case -1:
                    $error = '旧密码不正确';
                    break;
                case -4:
                    $error = 'Email格式有误';
                    break;
                case -5:
                    $error = 'Email不允许注册';
                    break;
                case -6:
                    $error = '该Email已经注册';
                    break;
                case -8:
                    $error = '该用户受保护无权限更改';
                    break;
                default:
                    $error = '未知错误';
            }
            $this->addError($error);
        }
        return $id;
    }

    /**
     * 根据用户名自动生成email
     * 注：Ucenter用户表邮箱字段只有32个字符
     */
    private function generateEmailByUsername($username)
    {
        $serverName = Yii::app()->request->getServerName();
        $email = substr(md5($username.mt_rand()),0,10) . '@' . substr($serverName, strpos($serverName, '.')+1);
        // var_dump($email);die;
        return $email;
    }

    public function getUc()
    {
        return $this->_uc;
    }
}
