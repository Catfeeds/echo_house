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
        if(!self::API_SYNLOGIN) {
			echo self::API_RETURN_FORBIDDEN;
		}

        @$uid = $this->get['uid'];
        @$username = $this->get['username'];

        $loginForm = new PptLoginForm('synlogin');
        $arr = array(
            'uid' => $uid,
        );
        $loginForm->attributes = $arr;
        $msg = $loginForm->login() ? '成功' : '失败';

        //同步登录代码逻辑
        Yii::log('同步登录'.$msg.'：用户id：'.$this->get['uid'].',用户名：'.$this->get['username'],'info','passport');

        echo self::API_RETURN_SUCCEED;
    }
}
