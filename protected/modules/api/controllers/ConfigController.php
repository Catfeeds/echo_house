<?php
class ConfigController extends ApiController{
	public function actionIndex()
	{
		$data = [
			'login_img'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','login_img')),
			'regis_words'=>SiteExt::getAttr('qjpz','regis_words'),
			'report_words'=>SiteExt::getAttr('qjpz','report_words'),
			'coo_words'=>SiteExt::getAttr('qjpz','coo_words'),
			'is_user'=>!Yii::app()->user->getIsGuest(),
		];
		$this->frame['data'] = $data;
	}
	
	public function actionGetP($lat='',$lng='')
	{
		$ht = "http://api.map.baidu.com/geocoder/v2/?ak=sr6PAhqtv1uXzOKwORUeOPrtKYbiIr1B&callback=renderReverse&location=$lat,$lng&output=json&pois=1";
		$res = HttpHelper::get($ht);
		$res = str_replace('renderReverse&&renderReverse(', '', $res['content']);
		$res = trim($res,')');
		$res = json_decode($res,true);
		$uid = $res['result']['pois'][0]['uid'];
		$this->frame['data'] = $uid;
	}

}