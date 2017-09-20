<?php
class UserController extends ApiController{

	public function actionCheckPhone($phone='')
	{
		if(UserExt::model()->undeleted()->find("phone='$phone'")) {
			 $this->returnError('手机号已存在');
		} else {
			$this->returnSuccess('手机号可用');
		}
	}

	public function actionAddOne($phone='',$type='1')
	{
		$arr = [1=>'注册',2=>'找回密码'];
		if(!SmsExt::addOne($phone,$arr[$type])) {
			$this->returnError('操作失败');
		}
		// $this->returnSuccess('操作成功');
	}

	public function actionCheckCode($phone='',$code='')
	{
		if(!SmsExt::checkPhone($phone,$code)) {
			$this->returnError('验证码错误');
		}
	}

	public function actionCheckCompanyCode($code='')
	{
		if(!CompanyExt::model()->find("code='$code'")) {
			$this->returnError('门店码不存在');
		}
	}

	public function actionRegis()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$obj = Yii::app()->request->getPost('UserExt',[]);
			if($obj) {
				$user = new UserExt;
				$company = '';
				if($obj['type']<3) {
					$code = $obj['companycode'];
					unset($obj['companycode']);
					if(!$code||!is_numeric($code)) {
						$this->returnError('门店码有误');
						return ;
					}
					if($obj['type'] == '1') {
						if(substr($code, 0,1)!='8') {
							$this->returnError('门店码有误');
							return ;
						}
					} elseif($obj['type'] == '2') {
						if(substr($code, 0,1)!='6') {
							$this->returnError('门店码有误');
							return ;
						}
					}
					$company = CompanyExt::getCompanyByCode($code);
					if($company) {
						$user->cid = $company->id;
					}
					$user->status = 1;
				}
				$user->attributes = $obj;
				!$user->pwd && $user->pwd = 'jjqxftv587';
				$user->pwd = md5($user->pwd);
				if(!$user->save()) {
					$this->returnError('操作失败');
				} else {
					if($company && $company->phone) {
						SmsExt::sendMsg('门店新增员工',$company->phone,['staff'=>$user->name.$staff->phone,'code'=>$code,'tel'=>SiteExt::getAttr('qjpz','site_phone')]);
					}
				}
			}
		}
	}

	public function actionLogin()
	{
		$phone = $pwd = '';
		if(Yii::app()->request->getIsPostRequest()) {
			$phone = $this->cleanXss(Yii::app()->request->getPost('name'));
			$pwd = $this->cleanXss(Yii::app()->request->getPost('pwd'));
			$rememberMe = $this->cleanXss(Yii::app()->request->getPost('rememberMe',''));
			$model = new ApiLoginForm();
			$model->username = $phone;
			$model->password = $pwd;
			$model->rememberMe = $rememberMe;
			if($model->login()) {
				$this->returnSuccess('登陆成功');
			}
			else {
				$this->returnError('用户名或密码错误');
			}
		}
	}

	public function actionEditPwd()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$phone = $this->cleanXss(Yii::app()->request->getPost('phone',''));
			$pwd = Yii::app()->request->getPost('pwd','');
			if($phone && $pwd) {
				$user = UserExt::model()->find('phone=:phone',[':phone'=>$phone]);
				$user->pwd = md5($pwd);
				if($user->save()){
					$this->returnSuccess('操作成功');
				}
				else {
					$this->returnError('操作失败');
				}
			}	
		}
	}
}