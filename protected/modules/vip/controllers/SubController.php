<?php
/**
 * 快速报备控制器
 */
class SubController extends VipController{
	
	public $cates = [];

	public $cates1 = [];

	public $controllerName = '';

	public $modelName = 'SubExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '客户';
		// $this->cates = CHtml::listData(LeagueExt::model()->normal()->findAll(),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$hid='',$cj=0,$jl='',$sczy='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$comuids = [];
            	$cri = new CDbCriteria;
            	$cri->addSearchCondition('name',$value);
            	$comobj = CompanyExt::model()->find($cri);
            	if($comobj) {
            		$comus = $comobj->users;
            		if($comus) {
            			foreach ($comus as $comu) {
            				$comuids[] = $comu['id'];
            			}
            			$criteria->addInCondition('uid',$comuids);
            		}
            	}
            } 
        if(Yii::app()->user->id>1) {
        	$hidsarr = Yii::app()->db->createCommand("select id,title from plot where deleted=0 and company_id=".Yii::app()->user->cid)->queryAll();
        	$hids = [];
        	if($hidsarr) {
        		foreach ($hidsarr as $h) {
        			$hids[] = $h['id'];
        		}
        	}
        	$criteria->addInCondition('hid',$hids);
        }
        if($hid) {
        	$criteria->addCondition('hid=:hid');
			$criteria->params[':hid'] = $hid;
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
        if($cj) {
			$criteria->addCondition('status>=3 and status<=5');
		}
		if($sczy) {
			$criteria->addCondition('notice='.$sczy);
		}
		if($jl) {
			$uidsarr = Yii::app()->db->createCommand("select phone from user where parent=".$jl)->queryAll();

			$uids = [];
			if($uidsarr) {
				foreach ($uidsarr as $uida) {
					$uids[] = $uida['phone'];
				}
			}
			$criteria->addInCondition('notice',$uids);
		}
		if($cate) {
			if($cate==1) {
				$criteria->addCondition('status>=:cid');
			}else
			$criteria->addCondition('status=:cid');
			$criteria->params[':cid'] = $cate;
		}
		$criteria->order = 'updated desc';
		// var_dump($hidsarr);ex
		$infos = $modelName::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'plots'=>$hidsarr,'hid'=>$hid,'jl'=>$jl,'sczy'=>$sczy]);
	}

	public function actionEdit($id='')
	{
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->time =  is_numeric($info->time)?$info->time : strtotime($info->time);
			if($info->save()) {
				$this->setMessage('操作成功','success',['list']);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('edit',['cates'=>$this->cates,'article'=>$info,'cates1'=>$this->cates1,]);
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

	public function actionPro($id='')
	{
		if($id) {
			$sub = SubExt::model()->findByPk($id);
			$criteria = new CDbCriteria;
			$criteria->addCondition('sid=:sid');
			$criteria->params[':sid'] = $id;
			$dats = SubProExt::model()->getList($criteria);
			$infos = $dats->data;
			$plot = $sub->plot;
			$this->render('pro',['infos'=>$infos,'pager'=>$dats->pagination,'sub'=>$sub,'plot'=>$plot]);
		}
	}
}