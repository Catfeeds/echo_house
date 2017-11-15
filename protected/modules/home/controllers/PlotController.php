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
		$this->render('info',['info'=>$info]);
		// var_dump($info);exit;
	}
}