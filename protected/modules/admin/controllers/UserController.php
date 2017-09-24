<?php
/**
 * 用户控制器
 */
class UserController extends AdminController{
    
    public $cates = [];

    public $controllerName = '';

    public $modelName = 'UserExt';

    public function init()
    {
        parent::init();
        $this->controllerName = '用户';
        // $this->cates = CHtml::listData(ArticleCateExt::model()->normal()->findAll(),'id','name');
    }
    public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$status='')
    {
        $modelName = $this->modelName;
        $criteria = new CDbCriteria;
        if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('name', $value);
            } elseif($type=='phone') {
                $criteria->addSearchCondition('phone', $value);
            } elseif($type=='com') {
                $cre = new CDbCriteria;
                $cre->addSearchCondition('name', $value);
                $coms = CompanyExt::model()->undeleted()->findAll($cre);
                $ids = [];
                if($coms) {
                    foreach ($coms as $c) {
                        $ids[] = $c->id;
                    }
                    $criteria->addInCondition('cid', $ids);
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
        if(is_numeric($cate)) {
            $criteria->addCondition('type=:cid');
            $criteria->params[':cid'] = $cate;
        }
        if(is_numeric($status)) {
            $criteria->addCondition('status=:status');
            $criteria->params[':status'] = $status;
        }        $criteria->order = 'sort desc,updated desc';
        $infos = $modelName::model()->undeleted()->getList($criteria,20);
        $this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'status'=>$status]);
    }

    public function actionEdit($id='')
    {
        $modelName = $this->modelName;
        $info = $id ? $modelName::model()->findByPk($id) : new $modelName;
        if(Yii::app()->request->getIsPostRequest()) {
            $info->attributes = Yii::app()->request->getPost($modelName,[]);
            !$info->pwd && $info->pwd = md5('jjqxftv587');
            // $info->pwd = md5($info->pwd);
            if($info->save()) {
                $this->setMessage('操作成功','success',['list']);
            } else {
                $this->setMessage(array_values($info->errors)[0][0],'error');
            }
        } 
        $this->render('edit',['cates'=>$this->cates,'article'=>$info]);
    }

    public function actionRecall($msg='',$id='')
    {
        if($id) {
            $info = UserExt::model()->findByPk($id);
            if($msg && $info && $info->qf_uid) {
                Yii::app()->controller->sendNotice($msg,$info->qf_uid);
                UserExt::model()->deleteAllByAttributes(['id'=>$id]);
                $this->setMessage('操作成功');
            } else {
                $this->setMessage('操作失败');
            }
            $this->redirect('list');
            
        }
    }
}