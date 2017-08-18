<?php
/**
 * 模块配置
 * @author weibaqiu
 * @date 2015-09-24
 */
class WapModule extends CWebModule
{
	public function init() {
		$this->setImport(array(
			'wap.models.*',
			'wap.models_ext.*',
			'wap.components.*',
			'wap.widgets.*',
		));

		Yii::app()->setComponents(array(
			'errorHandler' => array(
				'errorAction' => 'home/error/error',
			),
//			'viewRenderer' => array(
//				'class' => 'application.extensions.ESmartyViewRenderer',
//				'fileExtension' => '.html',
//			),
            'request' => array(
                'enableCsrfValidation' => false,
                'csrfTokenName' => 'CSRF_TOKEN'
            ),
            'user' => array(
				'allowAutoLogin' => true,
				'loginUrl' => Yii::app()->createUrl('wap/staff/login'),
				'authTimeout' => 3600 * 2,//用户登录后2小时不活动则过期，需要重新登陆
			),
		));

		Yii::app()->user->setStateKeyPrefix('_wap');
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
