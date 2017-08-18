<?php
/**
 * 后台验证登录类
 * @author tivon
 * @date 2015-04-22
 */
class HomeIdentity extends CUserIdentity
{
	/**
	 * 验证身份
	 * @return bool
	 */
	public function authenticate()
	{
		//内置帐号
		// if($this->username=='admin' && ($this->password=='admin123'))
		// {
		// 	$this->errorCode = self::ERROR_NONE;
		// 	$this->setState('id',1);
		// 	$this->setState('username','管理员');
		// 	return $this->errorCode;
		// }
		// var_dump(2);exit;
		if(is_numeric($this->username)) {
			$info = UserExt::model()->normal()->find("phone='{$this->username}'");
		} else {
			$info = UserExt::model()->normal()->find("name='{$this->username}'");
		} 

		if($info) {

			if($info->pwd!=md5($this->password)) {
				$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
			} else {
				$this->errorCode = self::ERROR_NONE;
				$this->setState('id',$info->id);
				$this->setState('username',$info->name);
				return $this->errorCode;
			}
		}else {
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
		}

		$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
		// exit;
		return $this->errorCode;
	}

	public function getId()
	{
		return $this->getState('id');
	}

	public function getName()
	{
		return $this->getState('username');
	}

	public function getIp()
    {
        $ip = '127.0.0.1';
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
