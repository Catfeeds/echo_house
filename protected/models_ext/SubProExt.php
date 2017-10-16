<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class SubProExt extends SubPro{
    public static $status = [
        '报备',
        '到访',
        '认筹',
        '认购',
        '成交',
        '结佣',
        '退定',
        '跟进',
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'sub'=>array(self::BELONGS_TO, 'SubExt', 'sid'),
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
        if($this->getIsNewRecord()) {
            if($this->status==7) {
                // 跟进通知分销业务员和分销店长
                $sub = $this->sub;
                $user = $this->user;
                $plotname = $sub->plot->title;
                $salename = $user->name.$user->phone;
                $cumsname = $sub->name.$sub->phone;
                $words = "{$plotname}的案场销售【{$salename}】对您的客户【{$cumsname}】跟进内容如下：".$this->note;
                $fx = $sub->user;
                if($fx) {
                    $fx->qf_uid && Yii::app()->controller->sendNotice($words,$fx->qf_uid);
                    $bosss = UserExt::model()->normal()->findAll('is_manage=1 and cid='.$fx->cid);
                    if($bosss) {
                        foreach ($bosss as $key => $value) {
                            $value->qf_uid && Yii::app()->controller->sendNotice($words,$value->qf_uid);
                        }
                    }
                }
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

}