<?php
/**
 * 相册控制器
 */
class PlotController extends HomeController
{
	public function actionInfo()
	{
		$py = Yii::app()->request->getQuery('py','');
		$info = PlotExt::model()->find("pinyin='$py'");
		$user = UserExt::model()->findByPk(1300);
		$this->render('info',['info'=>$info,'user'=>$user]);
		// var_dump($info);exit;
	}
}