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
            $criteria->addCondition('is_jl=:cate11');
            $criteria->params[':cate11'] = $cate;
        }
        // 已离职员工
        if($oldres = $this->company->old_users) {
            $oldids = [];
            foreach ($oldres as $o) {
                $criteria->addCondition('id='.$o->uid,'OR');
            }
        }
        $thiscid = $this->company->id;
        $criteria->order = "ABS(cid-$thiscid) asc,is_manage desc,is_jl asc,updated desc";
        $infos = $modelName::model()->undeleted()->getList($criteria,20);
        $scjls = [];
        $acjls = [];
        if($infos->data) {
            foreach ($infos->data as $d) {
                if($d->is_jl==1) {
                    $scjls[] = ['name'=>$d->name,'id'=>$d->id];
                }elseif($d->is_jl==2) {
                    $acjls[] = ['name'=>$d->name,'id'=>$d->id];
                }
            }
        }
        array_unshift($scjls, ['id'=>'0','name'=>'暂无']);
        array_unshift($acjls, ['id'=>'0','name'=>'暂无']);
        $this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'scjls'=>$scjls,'acjls'=>$acjls]);
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

    public function actionSetJl($id='')
    {
        if($id) {
            $info = UserExt::model()->findByPk($id);
            if($info->is_jl) {
                $this->setMessage('该用户已设定为经理，请勿重复操作','error');
            }elseif($info->is_manage) {
                $this->setMessage('该用户为店长','error');
            }else{
                $info->is_jl = 1;
                $info->save();
                $this->setMessage('操作成功');
            }
        }
    }
    public function actionSetGroup($id='',$parent='')
    {
        if($id&&is_numeric($parent)) {
            $info = UserExt::model()->findByPk($id);
            $info->parent = $parent;
            $info->save();
            $this->setMessage('操作成功');
        }
    }
    public function actionSetType($id='',$type='')
    {
        if($id&&is_numeric($type)) {
            $info = UserExt::model()->findByPk($id);
            $info->is_jl = $type;
            $info->save();
            $this->setMessage('操作成功');
        }
    }
    public function actioneditPwd($id='')
    {
        if($id) {
            $info = UserExt::model()->findByPk($id);
            if(Yii::app()->request->getIsPostRequest()) {
                $info->attributes = Yii::app()->request->getPost('UserExt',[]);
                $info->pwd && $info->pwd = md5($info->pwd);
                $info->cid = Yii::app()->user->cid;
                // $info->getIsNewRecord() && $info->status = 1;
                // $info->pwd = md5($info->pwd);
                if($info->save()) {
                    $this->setMessage('操作成功','success');
                    Yii::app()->user->logout();
                    $this->redirect(array('/vip/common/login'));
                } else {
                    $this->setMessage(array_values($info->errors)[0][0],'error');
                }
            } 
            $this->render('editpwd',['article'=>$info]);
        }
    }

    public function actionLeave($id='')
    {
        if($id) {
            $info = UserExt::model()->findByPk($id);
            if($info->cid==$this->company->id) {
                UserExt::model()->updateAll(['parent'=>0],'parent=:pa',[':pa'=>$id]);
                $info->cid = 0;
                $info->is_jl = 0;
                $info->is_manage = 0;
                $info->parent = 0;
                if($info->save()) {
                    $log = new UserLogExt;
                    $log->from = $this->company->id;
                    $log->uid = $id;
                    $log->to = 0;
                    $log->save();
                    $this->setMessage('操作成功','success');
                }
            }
        }
    }
}