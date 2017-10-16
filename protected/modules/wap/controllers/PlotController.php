<?php
class PlotController extends WapController{
	public function actionParamter($hid='')
	{
		$info = PlotExt::model()->findByPk($hid);
		if(!$info) {
			$this->redirect('/subwap/list.html');
		}
		$fields = [
			'open_time','is_new','delivery_time','developer','brand','manage_company','sale_tel','size','capacity','green','household_num','carport','price','manage_fee','property_years','dk_rule','id','floor_desc','building_num','transit'
		];
		$data = [];
		foreach ($fields as $key => $value) {
			$data[$value] = $info->$value;
		}
		$jzlb = [];
		if($jzlbs = $info->jzlb) {
			if(!is_array($jzlbs))
				$jzlbs = [$jzlbs];
			foreach ($jzlbs as $key => $value) {
				$tmp = TagExt::model()->findByPk($value);
				$tmp && $jzlb[] = $tmp->name;
			}
		}
		$zxzt = [];
		if($zxzts = $info->zxzt) {
			if(!is_array($zxzts))
				$zxzts = [$zxzts];
			foreach ($zxzts as $key => $value) {
				$tmp = TagExt::model()->findByPk($value);
				$tmp && $zxzt[] = $tmp->name;
			}
		}
		$data['open_time'] && $data['open_time'] = date('Y-m-d',$data['open_time']);
		if($data['delivery_time'] && $data['delivery_time']>time()) {
			$data['delivery_time'] = date('Y-m-d',$data['delivery_time']);
		} else {
			$data['delivery_time'] = '现房';
		}
		$data['zxzt'] = $zxzt;
		$data['jzlb'] = $jzlb;
		$this->render('paramter',$data);
	}

	public function actionComment($hid='')
	{
		$info = PlotExt::model()->findByPk($hid);
		if(!$info) {
			$this->redirect('/subwap/list.html');
		}
		$this->render('comment',['info'=>$info]);
	}
	public function actionPay($hid='')
	{
		$info = PlotExt::model()->findByPk($hid);
		if(!$info) {
			$this->redirect('/subwap/list.html');
		}
		$this->render('pay',['info'=>$info]);
	}

	public function actionInfo($id='')
	{
		$info = PlotExt::model()->findByPk($id);
		if(!$info) {
			$this->redirect('/subwap/list.html');
		}
		$this->render('info',['info'=>$info]);
	}

	public function actionMap($hid='')
	{
		$info = PlotExt::model()->findByPk($hid);
		if(!$info) {
			$this->redirect('/subwap/list.html');
		}
		$this->render('map',['info'=>$info]);
	}
}