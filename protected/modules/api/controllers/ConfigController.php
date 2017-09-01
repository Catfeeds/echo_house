<?php
class ConfigController extends ApiController{
	public function actionIndex()
	{
		$data = [
			'login_img'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','login_img')),
			'regis_words'=>SiteExt::getAttr('qjpz','regis_words'),
			'report_words'=>SiteExt::getAttr('qjpz','report_words'),
			'is_user'=>!Yii::app()->user->getIsGuest(),
		];
		$this->frame['data'] = $data;
	}
	
	public function actionTest()
	{
		$ht = 'http://api.map.baidu.com/geocoder/v2/?ak=DvCxyFxjXZ0eqtg8Z3eSG4OAnXvi0das&callback=renderReverse&location=39.983424,116.322987&output=json&pois=1';
		var_dump(HttpHelper::get($ht));exit;
	}

}