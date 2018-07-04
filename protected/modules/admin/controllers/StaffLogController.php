<?php
/**
 * 员工流水控制器
 */
class StaffLogController extends AdminController{
	
	public $cates = [];

	public $cates1 = [];

	public $controllerName = '';

	public $modelName = 'StaffLogExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '员工流水';
		// $this->cates = CHtml::listData(LeagueExt::model()->normal()->findAll(),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$sid='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$cre = new CDbCriteria;
            	// $cre
                $cre->addSearchCondition('phone', $value);
                $ids = [];
                if($ress = UserExt::model()->findAll($cre)) {
                	foreach ($ress as $res) {
                		$ids[] = $res['id'];
                	}
                }
                $criteria->addInCondition('uid',$ids);
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
		if($sid) {
			$criteria->addCondition('sid=:sid');
			$criteria->params[':sid'] = $sid;
		}
        $criteria->order = 'updated desc';
		$infos = $modelName::model()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'sid'=>$sid]);
	}

	public function actionEdit($id='')
	{
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		if(Yii::app()->request->getIsPostRequest()) {
			$res = Yii::app()->request->getPost($modelName,[]);
			// $res['arr'] = json_encode($res['arr']);
			$info->attributes = $res;
			// $info->time =  is_numeric($info->time)?$info->time : strtotime($info->time);
			if($info->save()) {
				$this->setMessage('操作成功','success',['list']);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('edit',['cates'=>$this->cates,'article'=>$info,'cates1'=>$this->cates1,]);
	}
	public function actionPlotlist($type='title',$value='',$time_type='created',$time='',$cate='',$sid='')
	{
		$modelName = 'PlotExt';
		$criteria = new CDbCriteria;
		$criteria->addCondition('staff_id>0');
		if($value = trim($value))
            if ($type=='title') {
            	$criteria->addSearchCondition('title',$value);
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
		if($sid) {
			$criteria->addCondition('staff_id=:sid');
			$criteria->params[':sid'] = $sid;
		}
        $criteria->order = 'updated desc';
		$infos = $modelName::model()->getList($criteria,20);
		$this->render('plotlist',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'sid'=>$sid]);
	}
	public function actionPlotdtlist($type='title',$value='',$time_type='created',$time='',$cate='',$sid='')
	{
		$modelName = 'PlotNewsExt';
		$criteria = new CDbCriteria;
		$criteria->addCondition('staff_id>0');
		if($value = trim($value))
            if ($type=='title') {
            	$cre = new CDbCriteria;
            	// $cre
                $cre->addSearchCondition('title', $value);
                $ids = [];
                if($ress = PlotExt::model()->findAll($cre)) {
                	foreach ($ress as $res) {
                		$ids[] = $res['id'];
                	}
                }
                $criteria->addInCondition('hid',$ids);
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
		if($sid) {
			$criteria->addCondition('staff_id=:sid');
			$criteria->params[':sid'] = $sid;
		}
        $criteria->order = 'updated desc';
		$infos = $modelName::model()->getList($criteria,20);
		$this->render('plotdtlist',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'sid'=>$sid]);
	}

	// public function actionEdit($id='')
	// {
	// 	$modelName = $this->modelName;
	// 	$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
	// 	if(Yii::app()->request->getIsPostRequest()) {
	// 		$res = Yii::app()->request->getPost($modelName,[]);
	// 		// $res['arr'] = json_encode($res['arr']);
	// 		$info->attributes = $res;
	// 		// $info->time =  is_numeric($info->time)?$info->time : strtotime($info->time);
	// 		if($info->save()) {
	// 			$this->setMessage('操作成功','success',['list']);
	// 		} else {
	// 			$this->setMessage(array_values($info->errors)[0][0],'error');
	// 		}
	// 	} 
	// 	$this->render('edit',['cates'=>$this->cates,'article'=>$info,'cates1'=>$this->cates1,]);
	// }

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

	public function actionRecall($msg='',$id='')
    {
        if($id) {
            $info = ReportExt::model()->findByPk($id);
            if($msg && $info && $user = $info->user) {
                $user->qf_uid && Yii::app()->controller->sendNotice($msg,$user->qf_uid);
                $info->status = 1;
                $info->save();
                $this->setMessage('操作成功');
            } else {
                $this->setMessage('操作失败');
            }
            $this->redirect('list');
            
        }
    }
}