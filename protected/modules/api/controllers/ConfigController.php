<?php
class ConfigController extends ApiController{
	public function actionIndex($phone='')
	{
		$userinfo = '';
		$oths = CacheExt::gas('wap_all_config','AreaExt',0,'wap配置缓存',function (){
	            $tmp = [
					'login_img'=>ImageTools::fixImage(SiteExt::getAttr('qjpz','login_img')),
					'regis_words'=>SiteExt::getAttr('qjpz','regis_words'),
					'report_words'=>SiteExt::getAttr('qjpz','report_words'),
					'coo_words'=>SiteExt::getAttr('qjpz','coo_words'),
					'add_market_words'=>SiteExt::getAttr('qjpz','add_market_words'),
					'add_subscribe_words'=>SiteExt::getAttr('qjpz','add_subscribe_words'),
					'add_vip_words'=>SiteExt::getAttr('qjpz','add_vip_words'),
					'our_uids'=>SiteExt::getAttr('qjpz','ouruids'),
					'kf_id'=>SiteExt::getAttr('qjpz','kfuid'),
				];
		            return $tmp;
		        });
		if(!$phone) {
			if(!empty($_COOKIE['phone'])) {
				$phone = $_COOKIE['phone'];
			}
		}
		if($phone)
			$userinfo = Yii::app()->db->createCommand("select id,status from user where  phone='$phone'")->queryRow();
		if($userinfo && $userinfo['status']==0) {
			$is_jy = 1;
		} else {
			$is_jy = 0;
		}
		$data = [
			'is_user'=>!Yii::app()->user->getIsGuest(),
			'user'=>$this->staff,
			'companyname'=>isset($this->staff->companyinfo)?$this->staff->companyinfo->name:'',
			'user_image'=>isset($this->staff->ava)?$this->staff->ava:'',
			'is_jy'=>$is_jy,
			'uid'=>$userinfo?$userinfo['id']:0,
		];
		$data = array_merge($oths,$data);
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
	public function actionQr($url='')
	{
		if($url) {
			$value = $url; //二维码内容 

			$errorCorrectionLevel = 'L';//容错级别 

			$matrixPointSize = 6;//生成图片大小 

			//生成二维码图片 

			QRcode::png($value, 'img/qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 

			$logo = 'logo.png';//准备好的logo图片 

			$QR = '/img/qrcode.png';//已经生成的原始二维码图 

			// var_dump(Yii::app()->basePath.'/../'.$QR);exit;
			// $img = Yii::app()->basePath.'/../'.$QR;
			$this->frame['data'] = Yii::app()->request->getHostInfo().$QR;
			
			// unlink($QR);
		}
	}

	

}