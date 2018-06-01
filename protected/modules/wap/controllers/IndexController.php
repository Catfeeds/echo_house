<?php
class IndexController extends WapController{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionFindCode($kw='')
	{
		$this->layout = 'layouts/nobase';
		$tmp = '';
		if($kw) {
			$criteria = new CDbCriteria;
			if(is_numeric($kw)) {
				$criteria->addSearchCondition('code',$kw);
			} else {
				$criteria->addSearchCondition('name',$kw);
			}
			if($company = CompanyExt::model()->findAll($criteria)) {
				if($company) {
					foreach ($company as $key => $value) {
						$tmp .= '<h1>'.$value->name.' '.$value->code.'<br></h1>';
						// echo('<h1>'.$value->name.' '.$value->code.'<br></h1>');
					}
				}
			} 
		}
		$this->render('findcode',['tmp'=>$tmp,'kw'=>$kw]);
	}
}