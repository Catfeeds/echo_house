<?php
/**
 * 用户控制器
 */
class UserController extends VipController{
    
    public $cates = [];

    public $controllerName = '';

    public $modelName = 'UserExt';

    public function init()
    {
        parent::init();
        $this->controllerName = '用户';
        // $this->cates = CHtml::listData(ArticleCateExt::model()->normal()->findAll(),'id','name');
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
        if(Yii::app()->user->id>1) {
            $criteria->addCondition('cid=:cid');
            $criteria->params[':cid'] = Yii::app()->user->cid;
        }
        if($cate) {
            $criteria->addCondition('cid=:cid');
            $criteria->params[':cid'] = $cate;
        }
        $infos = $modelName::model()->undeleted()->getList($criteria,20);
        $this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,]);
    }

    public function actionEdit($id='')
    {
        $modelName = $this->modelName;
        $info = $id ? $modelName::model()->findByPk($id) : new $modelName;
        if(Yii::app()->request->getIsPostRequest()) {
            $info->attributes = Yii::app()->request->getPost($modelName,[]);
            !$info->pwd && $info->pwd = md5('jjqxftv587');
            $info->cid = Yii::app()->user->cid;
            $info->getIsNewRecord() && $info->status = 1;
            // $info->pwd = md5($info->pwd);
            if($info->save()) {
                $this->setMessage('操作成功','success',['list']);
            } else {
                $this->setMessage(array_values($info->errors)[0][0],'error');
            }
        } 
        $this->render('edit',['cates'=>$this->cates,'article'=>$info]);
    }
}