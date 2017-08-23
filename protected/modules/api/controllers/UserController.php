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

	public function actionAddOne($phone='')
	{
		if(!SmsExt::addOne($phone)) {
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
				$code = isset($obj['companyCode']) ? $obj['companyCode'] : '';
				unset($obj['companyCode']);
				if($code && $obj['type']<3) {
					$company = CompanyExt::getCompanyByCode($code);
					if($company) {
						$user->cid = $company->id;
					}
				}
				$user->attributes = $obj;
				if(!$user->save()) {
					$this->returnError('操作失败');
				}
			}
		}
	}
}