<?php

class VipModule extends CWebModule
{
	/**
	 * 初始化方法
	 */
	public function init()
	{
		Yii::app()->setComponents(array(
			//授权管理配置
			'authManager' => array(
				'class' => 'CDbAuthManagerExt',
				'connectionID' => 'db',
				'itemTable' => 'auth_item',
	            'itemChildTable' => 'auth_item_child',
	 	    	'assignmentTable' => 'auth_assignment',
			),
			//系统js设置
			'clientScript' => array(
				'scriptMap' => array(
					'jquery.js' => false,//不加载系统自带的jquery
					'jquery.min.js' => false,//不加载系统自带的jquery
				),
			'coreScriptPosition' => CClientScript::POS_END,	//核心js加载到网页尾部
			),
			'user' => array(
				'allowAutoLogin' => true,
				'loginUrl' => Yii::app()->createUrl('vip/common/login'),
				'authTimeout' => 3600 * 2,//用户登录后2小时不活动则过期，需要重新登陆
				'stateKeyPrefix' => '_vip'
			),
			'errorHandler' => array(
				  'errorAction' => 'vip/common/error',
			),
		));

		$this->setImport(array(
			'vip.models.*',
			'vip.models_ext.*',
			'vip.components.*',
			'vip.widgets.*',
		));

		$this->preloadComponents();
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}
}
