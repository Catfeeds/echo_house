<?php
class PptIdentity extends CBaseUserIdentity
{
    private $_loginForm;

    public function __construct(PptLoginForm $loginForm)
    {
        $this->_loginForm = $loginForm;
    }

    public function authenticate()
    {
        //login直接登录的，需要调取uc组件，从form获取用户名和密码
        if($this->_loginForm->getIsLoginScenario()){
            // $result = Yii::app()->passport->login($this->username, $this->password);
            $result = Yii::app()->uc->login($this->_loginForm->username, $this->_loginForm->password);
            // var_dump($result);die;
            if(isset($result['uid']) && $result['uid']>0){
                $this->setState('id', $result['uid']);
                $this->setState('uid', $result['uid']);
                $this->setState('username', $result['username']);
                $this->setState('icon', $result['icon']);
                $this->setState('phone', $result['phone']);
                // $this->setState('html', $result['html']);
                $this->setState('openid', $result['openid']);
                $this->setState('unionid', $result['unionid']);
                Yii::app()->uc->user->setFlash('synloginHtml', $result['html']);

                $this->errorCode = self::ERROR_NONE;
            }
        }else{//synlogin被通知登录的，只需要从form获取一个uid
            $result = Yii::app()->uc->getUser($this->_loginForm->uid, 2);
            if(!Yii::app()->uc->hasError() && is_array($result) && $result){
                $result = current($result);
                $this->setState('id', $result['uid']);
                $this->setState('uid', $result['uid']);
                $this->setState('username', $result['username']);
                $this->setState('phone', $result['phone']);
                $this->setState('icon', $result['icon']);
                $this->setState('unionid', $result['unionid']);
                $this->setState('openid', $result['openid']);

                $this->errorCode = self::ERROR_NONE;

            } else {
                $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
            }
        }

        return $this->errorCode;
    }

    public function getLoginForm()
    {
        return $this->_loginForm;
    }

    public function getId()
    {
        return $this->getState('uid');
    }

    public function getName()
    {
        return $this->getState('username');
    }
}
