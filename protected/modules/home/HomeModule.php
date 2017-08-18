<?php
/**
 * 模块配置
 * @author tivon
 * @date 2015-09-24
 */
class HomeModule extends CWebModule
{
	public function init() {
		$this->setImport(array(
			'home.models.*',
			'home.models_ext.*',
			'home.components.*',
			'home.widgets.*',
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
		));

		Yii::app()->user->setStateKeyPrefix('_ppt');
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
