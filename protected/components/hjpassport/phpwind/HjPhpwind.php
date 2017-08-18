<?php
class HjPhpwind extends Ppt
{
    public $ucServer = '';
    public $appId = '';
    public $key = '';

    private $_uc;

    public function __construct(array $options)
    {
        foreach($options as $name=>$value){
            if(isset($this->$name)) $this->$name = $value;
        }
        $this->_uc = Yii::createComponent('hjpassport.phpwind.client.HjUc',$this->ucServer,$this->appId,$this->key);
    }

    public function login($username, $password, $isUid=false)
    {
        return $this->_uc->login($username, $password, $isUid);
    }

    public function synLogin($uid=0)
    {
        return $this->_uc->synLogin($uid);
    }

    public function synLogout()
    {
        return $this->_uc->synLogout();
    }

    public function getUserInfoes($uids, $isUid=true)
    {
        return $this->_uc->getUserInfoes($uids, $isUid);
    }

    public function getAvatars($uids)
    {
        return $this->_uc->getAvatars($uids);
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
        $uid = $this->_uc->regiseter($username, $password, $email);
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
        $ignoreOldPwd = $oldPwd === null ? true : false;
        $oldPwd = $oldPwd === null ? '' : $oldPwd;
        $id = $this->_uc->updatePwd($username, $newPwd, $oldPwd, $isUid, $ignoreOldPwd);
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

    public function getUc()
    {
        return $this->_uc;
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
}
