<?php
/**
 * 门店控制器
 */
class CompanyController extends VipController{
	public $controllerName = '';

	public $modelName = 'CompanyExt';

	public function init()
	{
		parent::init();
		$this->controllerName = '公司';
		// $this->cates = CHtml::listData(LeagueExt::model()->normal()->findAll(),'id','name');
		// $this->cates1 = CHtml::listData(TeamExt::model()->normal()->findAll(),'id','name');
	}
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='')
	{
		$modelName = $this->modelName;
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('name', $value);
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
		$infos = $modelName::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,]);
	}

	public function actionEdit($id='')
	{
		// $id = $this
		$modelName = $this->modelName;
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		// $info = $this->company;
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
						$this->setMessage('操作成功','success');
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
}