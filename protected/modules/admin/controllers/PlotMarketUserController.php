<?php
/**
 * 申请对接控制器
 */
class PlotMarketUserController extends AdminController{
	
	public $cates = [];

	public $cates1 = [];

	public $controllerName = '';

	public $modelName = 'PlotMarketUserExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '申请对接';
		// $this->cates = CHtml::listData(LeagueExt::model()->normal()->findAll(),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$expire='',$hid='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$criter = new CDbCriteria;
            	$criter->addSearchCondition('title',$value);
            	$plot = PlotExt::model()->find($criter);
            	if($plot) {
            		$criteria->addCondition('hid='.$plot->id);
            	}
            } elseif ($type=='phone') {
            	$criter = new CDbCriteria;
            	$criter->addSearchCondition('phone',$value);
            	$plot = UserExt::model()->find($criter);
            	if($plot) {
            		$criteria->addCondition('uid='.$plot->id);
            	}
            }
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
		if($cate) {
			$criteria->addCondition('status=:cid');
			$criteria->params[':cid'] = $cate;
		}
		if($hid) {
			$criteria->addCondition('hid=:hid');
			$criteria->params[':hid'] = $hid;
		}
		if(is_numeric($expire)) {
			if($expire)
				$criteria->addCondition('expire<=:ex');
			else
				$criteria->addCondition('expire>=:ex');
			$criteria->params[':ex'] = time();
		}
		$criteria->order = 'updated desc';
		$infos = $modelName::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'expire'=>$expire]);
	}

	public function actionEdit($id='',$hid='')
	{
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		if(Yii::app()->request->getIsPostRequest()) {
			$userphone = Yii::app()->request->getPost('userphone');
			$uid = '';
			if($userphone) {
				$user = UserExt::model()->find("phone='$userphone'");
				$uid = $user->id;
			}
			
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->uid = $uid;
			// $info->time =  is_numeric($info->time)?$info->time : strtotime($info->time);
			if($info->save()) {
				$this->setMessage('操作成功','success',['list']);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('edit',['cates'=>$this->cates,'article'=>$info,'cates1'=>$this->cates1,'userphone'=>isset($info->user->phone)?$info->user->phone:'','hid'=>$hid]);
	}

	public function actionAjaxStatus($kw='',$ids='')
	{
		if(!is_array($ids))
			if(strstr($ids,',')) {
				$ids = explode(',', $ids);
			} else {
				$ids = [$ids];
			}
		foreach ($ids as $key => $id) {
			$model = SubExt::model()->findByPk($id);
			$model->status = $kw;
			if(!$model->save())
				$this->setMessage(current(current($model->getErrors())),'error');
		}
		$this->setMessage('操作成功','success');	
	}
}