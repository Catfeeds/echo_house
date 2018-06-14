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
    public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$viptime='')
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
        if(is_numeric($viptime)) {
            if($viptime==0) {
                $criteria->addCondition('vip_expire>='.time());
            } else {
                $criteria->addCondition('vip_expire>0 and vip_expire<='.time());
            }
        }
        if(is_numeric($status)) {
            $criteria->addCondition('status=:status');
            $criteria->params[':status'] = $status;
        }        $criteria->order = 'sort desc,created desc,updated desc';
        $infos = $modelName::model()->undeleted()->getList($criteria,20);
        $this->render('list',['cate'=>$cate,'infos'=>$infos->data,'cates'=>$this->cates,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'status'=>$status,'viptime'=>$viptime]);
    }

    public function actionEdit($id='',$type='')
    {
        $modelName = $this->modelName;
        $info = $id ? $modelName::model()->findByPk($id) : new $modelName;
        if(Yii::app()->request->getIsPostRequest()) {
            $info->attributes = Yii::app()->request->getPost($modelName,[]);
            !$info->pwd && $info->pwd = md5('jjqxftv587');
            // $info->pwd = md5($info->pwd);
            if($info->save()) {
                if($info->getIsNewRecord()) {
                    SmsExt::sendMsg('新用户注册',$info->phone,['name'=>$info->name,'num'=>PlotExt::model()->normal()->count()+800]);
                }
                $this->setMessage('操作成功','success',[$type=='admin'?'/admin/plot/list':'list']);
            } else {
                $this->setMessage(array_values($info->errors)[0][0],'error');
            }
        } 
        $this->render('edit',['cates'=>$this->cates,'article'=>$info,'type'=>$type]);
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

    public function actionExport($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$viptime='')
    {
        $modelName = $this->modelName;
        $criteria = new CDbCriteria;
        if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('t.name', $value);
            } elseif($type=='phone') {
                $criteria->addSearchCondition('t.phone', $value);
            } elseif($type=='com') {
                $cre = new CDbCriteria;
                $cre->addSearchCondition('t.name', $value);
                $coms = CompanyExt::model()->undeleted()->findAll($cre);
                $ids = [];
                if($coms) {
                    foreach ($coms as $c) {
                        $ids[] = $c->id;
                    }
                    $criteria->addInCondition('t.cid', $ids);
                }
                
            }
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("t.{$time_type}>=:beginTime");
            $criteria->addCondition("t.{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
        if(is_numeric($cate)) {
            $criteria->addCondition('t.type=:cid');
            $criteria->params[':cid'] = $cate;
        }
        if(is_numeric($viptime)) {
            if($viptime==0) {
                $criteria->addCondition('t.vip_expire>='.time());
            } else {
                $criteria->addCondition('t.vip_expire>0 and t.vip_expire<='.time());
            }
        }
        if(is_numeric($status)) {
            $criteria->addCondition('t.status=:status');
            $criteria->params[':status'] = $status;
        }        $criteria->order = 't.sort desc,t.created desc,t.updated desc';

        $typeArr = UserExt::$ids;
        $criteria->with = 'companyinfo';
        // $criteria->limit = 5000;
        // $criteria->offset = 10001;
        // $infos = $modelName::model()->undeleted()->findAll($criteria);
        //     $data = []; 
        //     if($infos) {
        //         foreach ($infos as $ss) {
        //             if(!$ss||!$ss->companyinfo||!is_numeric($ss->type)||!isset($typeArr[$ss->type])){
        //                 continue;
        //             }
        //             $data[] = [$ss->id,$ss->name,$typeArr[$ss->type],$ss->phone,$ss->companyinfo?$ss->companyinfo->name:'-',$ss->vip_expire?date('Y',$ss->vip_expire):'-',$ss->vip_expire?date('m-d',$ss->vip_expire):'-',date('Y-m-d',$ss->created)];
        //         }
                
        //     }
        //     ExcelHelper::cvs_write_browser(date("YmdHis",time()),['id','姓名','用户类型','电话','公司','到期时间/年','到期时间/月日','创建时间'],$data); 
        // var_dump($modelName::model()->undeleted()->count($criteria));exit;
        if($modelName::model()->undeleted()->count($criteria)>5000) {
            // $this->setMessage('最大不超过5000条数据','error');
            // Yii::app()->end();
            var_dump('最大不超过5000条数据');exit;
        } else {
            $infos = $modelName::model()->undeleted()->findAll($criteria);
            $data = []; 
            if($infos) {
                foreach ($infos as $ss) {
                    if(!$ss||!$ss->companyinfo||!is_numeric($ss->type)||!isset($typeArr[$ss->type])){
                        continue;
                    }
                    $data[] = [$ss->id,$ss->name,$typeArr[$ss->type],$ss->phone,$ss->companyinfo?$ss->companyinfo->name:'-',$ss->vip_expire?date('Y',$ss->vip_expire):'-',$ss->vip_expire?date('m-d',$ss->vip_expire):'-',date('Y-m-d',$ss->created)];
                }
                
            }
            ExcelHelper::cvs_write_browser(date("YmdHis",time()),['id','姓名','用户类型','电话','公司','到期时间/年','到期时间/月日','创建时间'],$data); 
        }

    }

}