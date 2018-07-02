<?php 
/**
 * 相册类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.5)
 */
class CompanyExt extends Company{
    public static $type = [
        1=>'总代公司',
        2=>'分销公司'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'users'=>array(self::HAS_MANY, 'UserExt', 'cid','condition'=>'users.deleted=0 and users.status=1'),
            'package'=>array(self::HAS_ONE, 'CompanyPackageExt', 'cid'),
            'plotnum'=>array(self::STAT, 'PlotExt', 'company_id','condition'=>'deleted=0'),
            'managers'=>array(self::HAS_MANY, 'UserExt', 'cid','condition'=>'managers.deleted=0 and managers.status=1 and managers.is_manage=1'),
            'old_users'=>array(self::HAS_MANY, 'UserLogExt', 'from'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            // array('name', 'unique', 'message'=>'{attribute}已存在')
        ));
    }

    /**
     * 返回指定AR类的静态模型
     * @param string $className AR类的类名
     * @return CActiveRecord Admin静态模型
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function afterFind() {
        parent::afterFind();
    }

    public function beforeValidate() {
        if($this->status==1 && !$this->code) {
            $code = $this->type==1 ? 800000 + rand(0,99999) :  600000 + rand(0,99999) ;
            // var_dump($code);exit;
            while (CompanyExt::model()->find('code='.$code)) {
                $code = $this->type==1 ? 800000 + rand(0,99999) :  600000 + rand(0,99999) ;
            }
            $this->code = $code;
            // if($this->adduid) {
            //     $user = UserExt::model()->find("qf_uid=".$this->adduid);
            //     if($user) {
            //         Yii::app()->controller->sendNotice('您好，贵公司门店码为'.$this->code.'，公司其他员工也可以通过此门店码加入经纪圈新房通，点这里立即前往绑定门店码：'.Yii::app()->request->getHostInfo().'/subwap/register.html?phone='.$user->phone,$this->adduid);
            //         SmsExt::sendMsg('公司注册通过',$user->phone,['code'=>$this->code]);
            //     }
            // }
            // $this->adduid && Yii::app()->controller->sendNotice('您好，贵公司门店码为'.$this->code.'，公司其他员工也可以通过此门店码加入经纪圈新房通，点这里立即前往绑定门店码：'.Yii::app()->request->getHostInfo().'/subwap/register.html?phone='.Yii::app()->db->createCommand("select phone from user where qf_uid=".$this->adduid)->queryScalar(),$this->adduid);
            // $this->phone && SmsExt::sendMsg('公司注册通过',$this->phone,['code'=>$code]);
        }
        if(($this->getIsNewRecord() && $this->status==1) || ($this->status==1 && Yii::app()->db->createCommand("select status from company where id=".$this->id)->queryScalar()==0)) {

            if($this->addphone&&$this->adduid) {
                Yii::app()->controller->sendNotice('您好，贵公司门店码为'.$this->code.'，公司其他员工也可以通过此门店码加入经纪圈新房通，点这里立即前往绑定门店码：'.Yii::app()->request->getHostInfo().'/subwap/register.html?phone='.$this->addphone,$this->adduid);
                SmsExt::sendMsg('公司注册通过',$this->addphone,['code'=>$this->code]);
            }
        }
        if(!$this->addphone&&$this->adduid) {
            $p = $this->adduid;
            $user = UserExt::model()->find("qf_uid=$p");
            if($user) {
                $this->addphone = $user->phone;
            }
        }
        if($this->getIsNewRecord()) {
            
            if($this->status==0) {
                
                $res = Yii::app()->controller->sendNotice('有新的公司提交合作申请，请登陆后台审核','',1);
            }
            if(!$this->created && !$this->updated)
                $this->created = $this->updated = time();
        }
        else
            $this->updated = time();
        return parent::beforeValidate();
    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'sorted' => array(
                'order' => "{$alias}.sort desc,{$alias}.updated desc",
            ),
            'normal' => array(
                'condition' => "{$alias}.status=1 and {$alias}.deleted=0",
                'order'=>"{$alias}.sort desc,{$alias}.updated desc",
            ),
            'undeleted' => array(
                'condition' => "{$alias}.deleted=0",
                // 'order'=>"{$alias}.sort desc,{$alias}.updated desc",
            ),
        );
    }

    /**
     * 绑定行为类
     */
    public function behaviors() {
        return array(
            'CacheBehavior' => array(
                'class' => 'application.behaviors.CacheBehavior',
                'cacheExp' => 0, //This is optional and the default is 0 (0 means never expire)
                'modelName' => __CLASS__, //This is optional as it will assume current model
            ),
            'BaseBehavior'=>'application.behaviors.BaseBehavior',
        );
    }

    public function getMangerArr()
    {
        $id = $this->id;
        return Yii::app()->db->createCommand("select id,name from user where cid=$id and is_manage=1")->queryRow();
    }

    public static function getCompanyByCode($code='')
    {
        if($code) {
            return CompanyExt::model()->normal()->find("code='$code'");
        }
    }

    public function getScjl($arr=1)
    {
        $data = [];
        if($users = $this->users) {
            foreach ($users as $key => $value) {
                if($arr==1)
                    $value->is_jl==1&&$data[$value->id] = $value->name;
                else
                    $value->is_jl==1&&$data[] = ['id'=>$value->id,'name'=>$value->name];
            }
        }
        return $data;
    }
    public function getSczy()
    {
        $data = [];
        if($users = $this->users) {
            foreach ($users as $key => $value) {
                $value->is_jl==3&&$data[$value->phone] = $value->name;
            }
        }
        return $data;
    }
    public function getAcsales()
    {
        $data = [];
        if($users = $this->users) {
            foreach ($users as $key => $value) {
                $value->is_jl==5&&$data[] = ['id'=>$value->id,'name'=>$value->name.$value->phone];
            }
        }
        return $data;
    }

}