<?php
/**
 * 门店控制器
 */
class CompanyController extends AdminController{
	public $controllerName = '';

	public $modelName = 'CompanyExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '公司';
		// $this->cates = CHtml::listData(LeagueExt::model()->normal()->findAll(),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$status='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('name', $value);
            } elseif($type=='code') {
            	$criteria->addCondition('code=:code');
				$criteria->params[':code'] = $value;
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
			$criteria->addCondition('type=:cid');
			$criteria->params[':cid'] = $cate;
		}
		if(is_numeric($status)) {
			$criteria->addCondition('status=:status');
			$criteria->params[':status'] = $status;
		}
		$criteria->order = 'sort desc,updated desc';
		$infos = $modelName::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'status'=>$status]);
	}

	public function actionEdit($id='')
	{
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			// $info->time =  is_numeric($info->time)?$info->time : strtotime($info->time);
			if($info->getIsNewRecord()) {
				if(Yii::app()->db->createCommand("select id from company where name='".$info->name."'")->queryScalar()) {
					$this->setMessage('公司名已存在','error');
				} else {
					if($info->save()) {
						$this->setMessage('操作成功','success',['list']);
					} else {
						$this->setMessage(array_values($info->errors)[0][0],'error');
					}
				}
			} else {
				if(Yii::app()->db->createCommand("select id from company where id<>".$info->id." and name='".$info->name."'")->queryScalar()) {
					$this->setMessage('公司名已存在','error');
				} else {
					if($info->save()) {
						$this->setMessage('操作成功','success',['list']);
					} else {
						$this->setMessage(array_values($info->errors)[0][0],'error');
					}
				}
			}
					
		} 
		$this->render('edit',['article'=>$info,]);
	}

	public function actionSetCode($id='')
	{
		if($id) {
			$info = CompanyExt::model()->findByPk($id);
			if($info->code) {
				$this->setMessage('门店码已存在','error');
				return ;
			}

			$code = $info->type==1 ? 800000 + rand(0,99999) :  600000 + rand(0,99999) ;
			// var_dump($code);exit;
			while (CompanyExt::model()->find('code='.$code)) {
				$code = $info->type==1 ? 800000 + rand(0,99999) :  600000 + rand(0,99999) ;
			}
			$info->code = $code;
			$info->save();
			$this->setMessage('操作成功','success');
		}
	}

	public function actionRecall($msg='',$id='')
	{
		if($id) {
			$info = CompanyExt::model()->findByPk($id);
			if($msg && $info && $info->adduid) {
				Yii::app()->controller->sendNotice($msg,$info->adduid);
				// $info->phone && SmsExt::sendMsg('公司注册未通过',$info->phone);
				CompanyExt::model()->deleteAllByAttributes(['id'=>$id]);
				$this->setMessage('操作成功');
			} else {
				$this->setMessage('操作失败');
			}
			$this->redirect('list');
			
		}
	}
}