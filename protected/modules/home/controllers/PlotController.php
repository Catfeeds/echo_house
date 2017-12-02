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
		if(!$info) {
			$this->redirect($this->createUrl('/api/index/index'));
		}
		// $user = UserExt::model()->findByPk(1300);
		$host = Yii::app()->request->getHostInfo();
		if($obj = UrlPlotExt::model()->find("hid=".$info->id)) {
			// var_dump($obj);exit;
			$thaturl = $obj->url;
			if($thaturl->url!=$host) {
				$this->redirect($this->createUrl('/api/index/index'));
			} else {
				$user = $thaturl->user;
				$userdata = [];
				if($users = $info->users) {
					foreach ($users as $key => $value) {
						$str = substr($value->phone, -4,4);
						$phone = str_replace($str, '****', $value->phone);
						$userdata[] = ['name'=>$value->name,'phone'=>$phone,'time'=>date('m-d H:i',$value->created)];
					}
					
				}
				if(count($users)<10) {
						$userdata += [
						['name'=>'张先生','phone'=>'1386122****','time'=>'11-01 11:08'],
						['name'=>'李先生','phone'=>'1770127****','time'=>'09-23 16:28'],
						['name'=>'杨女士','phone'=>'1335788****','time'=>'12-02 13:11'],
						['name'=>'张女士','phone'=>'1515192****','time'=>'11-12 21:09'],
						['name'=>'贾先生','phone'=>'1396112****','time'=>'11-12 19:43'],
						['name'=>'左女士','phone'=>'1386114****','time'=>'11-20 08:19'],
						['name'=>'史先生','phone'=>'1886212****','time'=>'11-13 15:38'],
						['name'=>'张先生','phone'=>'1396122****','time'=>'11-15 14:24'],
						['name'=>'赵女士','phone'=>'1550623****','time'=>'11-12 12:01'],
						['name'=>'曾先生','phone'=>'1770725****','time'=>'11-14 11:23'],
						];
					}	
				// var_dump($userdata);exit;
				if($this->redirectWap()) {
					$this->render('wapinfo',['info'=>$info,'user'=>$user,'url'=>$thaturl,'userdata'=>$userdata]);
				} else
					$this->render('info',['info'=>$info,'user'=>$user,'url'=>$thaturl,'userdata'=>$userdata]);
			}
		} else {
			$this->redirect($this->createUrl('/api/index/index'));
		}
	}

	public function actionAddUser()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('PlotUser',[]);
			if($obj = PlotUserExt::model()->find("phone='".$values['phone']."' and hid=".$values['hid']."")) {
				echo json_encode(['status'=>'error','msg'=>'请勿重复提交']);
			} else {
				$obj = new PlotUserExt;
				$obj->attributes = $values;
				if($obj->save()) {
					echo json_encode(['status'=>'success','msg'=>'提交成功']);
				}
			}
		}
	}
}