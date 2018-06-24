<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotMarketUserExt extends PlotMakertUser{
    public static $status = [
        '审核中','通过'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
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
        // if(!$this->image){
        //     $this->image = SiteExt::getAttr('qjpz','productNoPic');
        // }
    }

    public function beforeValidate() {
        if(strstr($this->expire,'-')) {
            $this->expire = strtotime($this->expire);
        }
        if($this->is_manager==1&&(!$this->plot->uid)) {
            $this->plot->uid = $this->uid;
            $this->plot->save();
        }
        if($this->getIsNewRecord()) {
            if(($plot = $this->plot) && ($user = $this->user)) {
                if($this->is_manager && $user->qf_uid)
                    Yii::app()->controller->sendNotice('恭喜您，您已经成为'.$plot->title.'的付费对接人！
点这里预览项目详情：'.Yii::app()->request->getHostInfo().'/subwap/detail.html?id='.$plot->id,$user->qf_uid);
                // 绑定分机号，如果用户有分机号则不管，如果没有分机号，自动分配号码
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
                        $newvps->max = $user->virtual_no_ext;
                        $newvps->save();
                    } else {
                        // Yii::log(json_encode($res));
                    }
                }

            }
            $res = Yii::app()->controller->sendNotice(($this->plot?$this->plot->title:'').'有新的对接人'.($this->user?($this->user->name.$this->user->phone):'').'支付成功，请知晓','',1);
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

    public function afterSave()
    {
        parent::afterSave();
        // if($phone = $this->user->phone) {
        //     $market_users = $this->plot->market_users;
        //     $pharr = array_filter(explode(' ', $market_users));
        //     if($this->status==1) {
        //         if(!in_array($phone, $pharr)) {
        //             $pharr[] = $this->user->name.$phone;
        //             foreach ($pharr as $key => $value) {
        //                 preg_match('/[0-9]+/', $value,$tmp);
        //                 if(!Yii::app()->db->createCommand("select id from plot_makert_user where hid=".$this->plot->id." and phone='$tmp' and expire>".time())->queryScalar()) {
        //                     unset($pharr[$key]);
        //                 }
        //             }
        //             $this->plot->market_users = implode(' ', $pharr);

        //             $this->plot->save();
        //         }
        //     } else {
        //         if(in_array($this->user->name.$phone, $pharr)) {
        //             $market_users = str_replace($this->user->name.$phone, '', $market_users);
        //             $newarr = array_filter(explode(' ', $market_users));
        //             $this->plot->market_users = implode(' ', $newarr);
        //             $this->plot->save();
        //         }
        //     }
        //     // var_dump($pharr,$phone);exit;
                

        // }
    }

}