<?php
class ConfigController extends ApiController{
	public function actionIndex()
	{
		$data = [
			'login_img'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','login_img')),
			'regis_words'=>SiteExt::getAttr('qjpz','regis_words'),
		];
	}
}