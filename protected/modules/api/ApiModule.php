<?php

class ApiModule extends CWebModule {
	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
				 'api.models.*',
				 'api.models_ext.*',
				 'api.components.*',
		 ));

		Yii::app()->setComponents(array(
				  'errorHandler' => array(
						  'errorAction' => 'api/default/error',
				  ),
				  'user' => array(
		                'allowAutoLogin' => true,
		                // 'loginUrl' => Yii::app()->createUrl('api/vipApi/login'),
		                'authTimeout' => 3600 * 2,//用户登录后2小时不活动则过期，需要重新登陆
		                'stateKeyPrefix' => '_vip',
	              ),
		  ));
		// Yii::app()->user->setStateKeyPrefix('_api');
	}

	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
