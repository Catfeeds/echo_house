<?php
/**
 * 登录表单模型
 *
 * @author weibaqiu
 * @version 2015-02-27 21:12:17
 */
class StaffLoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * 验证规则
	 * @return [type] [description]
	 */
	public function rules()
	{
		return array(
			array('username, password', 'required', 'message'=>'用户名和密码必填'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * 声明特性标签
	 */
	public function attributeLabels()
	{
		return array(
            'username' => '用户名',
            'password' =>  '密码',
		);
	}


	/**
	 * 验证帐号密码
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
            $this->_identity=new StaffIdentity($this->username,$this->password);
            $this->_identity->authenticate();
            if($this->_identity->errorCode)
				$this->addError('error','用户名或密码错误');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new StaffIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===StaffIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24 : 0; // 24小时
			Yii::app()->user->login($this->_identity, $duration);
			return true;
		}
		else
			return false;
	}
}
