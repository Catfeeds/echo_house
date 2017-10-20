 <?php
/**
 * 后台验证登录类
 * @author tivon
 * @date 2015-04-22
 */
class VipIdentity extends CUserIdentity
{
	/**
	 * 验证身份
	 * @return bool
	 */
	public function authenticate()
	{
		if($user = UserExt::model()->normal()->find("is_manage=1 and phone='".$this->username."'") ){
			if($user->pwd == md5($this->password)) {
				$company = CompanyExt::model()->findByPk($user->cid);
				if($company) {
					$expire = Yii::app()->db->createCommand("select expire from company_package where cid=".$company->id)->queryScalar();
					if(!$expire||$expire<=time()) {
						$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
					} else {
						$this->errorCode = self::ERROR_NONE;
						$this->setState('id',$user->id);
						$this->setState('username',$user->name);
						$this->setState('avatar','');
						$this->setState('cid',$user->cid);
						$this->setState('cname',$company->name);
					}

				} else {
					$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
				}
						
				return $this->errorCode;
			}
		}

		$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
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
