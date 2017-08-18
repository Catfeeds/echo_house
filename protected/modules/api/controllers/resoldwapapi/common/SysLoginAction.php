<?php 
/**
 * 同步登陆
 * @author steven allen <[<email address>]>
 * @date(2016.12.9)
 */
class SysLoginAction extends CAction
{
	public function run()
	{
		$info = Yii::app()->request->getPost('info');
        $info = CJSON::decode($info);
        $msg = 'fail';

        if (isset($info['userId']) && $info['userId']) {
            //设置用户登录状态
            $loginForm = new PptLoginForm('synlogin');
            $loginForm->attributes = array(
                'uid' => $info['userId'],
            );
            $loginForm->login();
            $msg = 'success';
        } else {
            Yii::app()->uc->user->logout(false);
        }
        $this->controller->frame['msg'] = $msg;
	}
}