<?php

/**
 * User: fanqi
 * Date: 2016/9/29
 * Time: 14:18
 * 登录接口
 */
class LoginAction extends CAction
{
    public function run()
    {
        $user = Yii::app()->request->getPost('user',[]);
        $vipLoginForm = new VipLoginForm();
        $vipLoginForm->attributes = $user;
        if (!$vipLoginForm->validate() || !$vipLoginForm->login()) {
            $errors = $vipLoginForm->errors;
            $this->getController()->frame['status'] = 'error';
            $this->getController()->frame['msg'] = '登录错误';
        }
    }
}