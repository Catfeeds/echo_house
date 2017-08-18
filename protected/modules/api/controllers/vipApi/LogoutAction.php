<?php
/**
 * 退出登录
 * @author steven.allen
 * @date 2016.10.24
 */
class LogoutAction extends CAction
{
    public function run()
    {
        Yii::app()->user->logout();
    }
}