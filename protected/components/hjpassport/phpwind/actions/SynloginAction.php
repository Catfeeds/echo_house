<?php
/**
 * UC接口动作
 * @author tivon
 * @version 2016-01-19
 */
class SynloginAction extends UcReceiverAction
{
    /**
     * 运行方法
     * $get['uid']
     * $get['username']
     */
    public function run()
    {
        $data = $this->getController()->strcode($this->data['user'], false);
        @list($uid, $username, $pwd) = explode("\t", $data);
        //同步登录代码逻辑
        $loginForm = new PptLoginForm('synlogin');
        $arr = array(
            'uid' => $uid,
        );
        $loginForm->attributes = $arr;
        $msg = $loginForm->login() ? '成功' : '失败';

        Yii::log('No'.$this->getController()->requestId.'，同步登录'.$msg.'，用户id'.$uid,'info','passport');
    }


}
