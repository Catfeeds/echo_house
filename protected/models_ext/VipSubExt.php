<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class VipSubExt extends VipSub{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'user'=>array(self::BELONGS_TO, 'UserExt', ['user_phone'=>'phone']),
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            // 'images'=>array(self::HAS_MANY, 'AlbumExt', 'pid'),
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
        if($this->getIsNewRecord()){
            if($user = $this->user) {
                $user->qf_uid && Yii::app()->controller->sendNotice('尊敬的经纪人'.$user->name.'，您好。您分享的【'.$this->plot->title.'】项目页面，收集到新的看房客户了。客户姓名：'.$this->name.' 手机号码：'.$this->vip_phone,$user->qf_uid);
            }
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
                'condition' => "{$alias}.status=1 ",
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

}