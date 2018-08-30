<?php 
/**
 * 用户类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class UserExt extends User{
    /**
     * @var array 状态
     */
    static $status = array(
        0 => '禁用',
        1 => '启用',
        2 => '回收站',
    );
    /**
     * @var array 状态按钮样式
     */
    static $statusStyle = array(
        0 => 'btn btn-sm btn-warning',
        1 => 'btn btn-sm btn-primary',
        2 => 'btn btn-sm btn-danger'
    );
    public static $ids = [
        '1'=>'总代公司',
        '2'=>'分销公司',
        '3'=>'独立中介',
    ];
    public static $is_jls = [
        0=>'暂无',
        1=>'市场部经理',
        2=>'案场部经理',
        3=>'市场专员',
        4=>'案场助理',
        5=>'案场销售',
    ];
    public static $sex = [
    '未知','男','女'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'houseInfo'=>array(self::BELONGS_TO, 'HouseExt', 'house'),
            'news'=>array(self::HAS_MANY, 'ArticleExt', 'uid'),
            'comments'=>array(self::HAS_MANY, 'CommentExt', 'uid'),
            'companyinfo'=>array(self::BELONGS_TO, 'CompanyExt', 'cid'),
            'plotplaces'=>array(self::HAS_MANY, 'PlotPlaceExt', 'uid'),
            'plotsales'=>array(self::HAS_MANY, 'PlotSaleExt', 'uid'),
            'subscribes'=>array(self::HAS_MANY, 'SubscribeExt', 'uid'),
            'plots'=>array(self::HAS_MANY, 'PlotExt', 'uid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array('phone', 'unique', 'message'=>'{attribute}已存在'),
            array('qf_uid', 'required')
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
        if(!$this->image){
            $this->image = SiteExt::getAttr('qjpz','userImg');
        }
    }

    public function beforeValidate() {
        // 总代必有虚拟号
        // if($this->type==1) {
        //     if(!$this->virtual_no) {
        //         $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
        //         if($vps) {
        //             $vp = $vps->phone;
        //             $nowext = $vps->max?($vps->max+1):1;
        //             $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
        //             // var_dump($nowext);exit;
        //             // 生成绑定
        //             unset(Yii::app()->msg);
        //             $obj = Yii::app()->axn;
        //             $res = $obj->bindAxnExtension('默认号码池',$this->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));

        //             if($res->Code=='OK') {
        //                 $this->virtual_no = $res->SecretBindDTO->SecretNo;
        //                 $this->virtual_no_ext = $res->SecretBindDTO->Extension;
        //                 $this->subs_id = $res->SecretBindDTO->SubsId;

        //                 // $user->save();
        //                 // Yii::log($this->virtual_no);
        //                 $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$this->virtual_no'"]);
        //                 if($newvps && $this->virtual_no_ext) {
        //                     $newvps->max = $this->virtual_no_ext;
        //                     $newvps->save();
        //                 }
                        
                        
        //             } else {
        //                 Yii::log(json_encode($res));
        //             }
        //         }
        //     }
        // }
        if(!$this->type) {
            $cinfo = $this->companyinfo;
            if(!$cinfo) {
                $this->type = 3;
            } else {
                $this->type = $cinfo->type;
            }
        }
        if($this->deleted==1) {
            $this->status = 1;
        }
        if(strstr($this->vip_expire,'-')) {
            $this->vip_expire = strtotime($this->vip_expire);
        }
        if(strstr($this->vip_expire_new,'-')) {
            $this->vip_expire_new = strtotime($this->vip_expire_new);
        }
        if($this->getIsNewRecord()) {
            SmsExt::sendMsg('新用户注册',$this->phone,['name'=>$this->name,'num'=>PlotExt::model()->normal()->count()+800]);
            // Yii::log($this->phone);
            // exit;
            if(!$this->qf_uid && !empty($_COOKIE['qf_uid'])) {
                $this->qf_uid = $_COOKIE['qf_uid'];
            }
            if($this->status==0) {
                $res = Yii::app()->controller->sendNotice('有新的用户注册，请登陆后台审核','',1);
            }
            !$this->pwd && $this->pwd = md5('jjqxftv587');
            if(!$this->created && !$this->updated)
                $this->created = $this->updated = time();
        }
        else {
            $this->updated = time();
            if($this->type==1 && $this->vip_expire_new && $this->status==1 && Yii::app()->db->createCommand('select vip_expire_new from user where id='.$this->id)->queryScalar()!=$this->vip_expire_new) {
                Yii::app()->db->createCommand("update plot_makert_user set expire=".$this->vip_expire_new." where status=1 and uid=".$this->id)->execute();
                SmsExt::sendMsg('充值会员成功',$this->phone,['phone'=>SiteExt::getAttr('qjpz','site_wx'),'name'=>$this->name]);
                Yii::app()->controller->sendNotice($this->companyinfo->name.'-'.$this->name.$this->phone.'会员支付成功,到期时间为'.date('Y-m-d',$this->vip_expire_new),'',1);

            }
            // var_dump(Yii::app()->db->createCommand('select status from user where id='.$this->id)->queryScalar());exit;
            if($this->status==1 && !(Yii::app()->db->createCommand('select status from user where id='.$this->id)->queryScalar())) {
                $res = Yii::app()->controller->sendNotice('您的新房通账号已通过审核，欢迎访问经纪圈新房通',$this->qf_uid);
                // var_dump(SmsExt::sendMsg('经纪人注册通过',$this->phone););exit;
                SmsExt::sendMsg('经纪人注册通过',$this->phone);
                // HttpHelper::get('http://fang.jj58.com.cn/api/index/sendNotice?uid='.$this->qf_uid.'&words=您的账号已通过审核，欢迎访问经纪圈新房通');
            }
        }
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

    public function afterSave()
    {
        
        parent::afterSave();
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

    public static function makeVirtual($user)
    {
        if(!$user->virtual_no) {
            $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
            $vp = $vps->phone;
            $nowext = $vps->max?($vps->max+1):1;
            $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
            // var_dump($nowext);exit;
            // 生成绑定
            $obj = Yii::app()->axn;
            $res = $obj->bindAxnExtension('默认号码池',$user->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));
            if($res->Code=='OK') {
                $user->virtual_no = $res->SecretBindDTO->SecretNo;
                $user->virtual_no_ext = $res->SecretBindDTO->Extension;
                $user->subs_id = $res->SecretBindDTO->SubsId;
                $user->save();
                $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$user->virtual_no'"]);
                if($newvps && $user->virtual_no_ext) {
                    $newvps->max = $user->virtual_no_ext;
                }
                $newvps->save();
            } else {
                // Yii::log(json_encode($res));
            }
        }
    }

    // 获取能发布的数量
    public function getCanSubNum()
    {
        if($this->vip_expire>time()) {
            return 1000;
        } elseif ($this->vip_expire_new>time()) {
            // 上架的项目
            $num1 = PlotExt::model()->count('status=1 and uid='.$this->id);
            // 上架的项目的对接人
                    
            $num2 = Yii::app()->db->createCommand('select count(m.id) from plot_makert_user m left join plot p on m.hid=p.id where m.status=1 and m.deleted=0 and m.expire>'.time().' and m.uid='.$this->id.' and p.status=1 and p.uid!='.$this->id)->queryScalar();
            return ($this->can_sub-$num1-$num2)<=0?0:($this->can_sub-$num1-$num2);
        } else {

            return 0;
        }
    }
}