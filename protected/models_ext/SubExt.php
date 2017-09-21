<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class SubExt extends Sub{
    public static $status = [
        '报备',
        '到访',
        '认筹',
        '认购',
        '签约',
        '结佣',
        '退定',
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'pros'=>array(self::HAS_MANY, 'SubProExt', 'sid','order'=>'pros.created desc'),
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
            $res = Yii::app()->controller->sendNotice(($this->plot?$this->plot->title:'').'有新的报备，请登陆后台审核','',1);
            $this->created = $this->updated = time();
        }
        else
            $this->updated = time();
        if($this->status==2) {
            $company = CompanyExt::model()->findByPk($this->plot->company_id);
            $managers = $company->managers;
            if($managers) {
                $uidss = '';
                foreach ($managers as $key => $value) {
                    $value->qf_uid && $uidss .= $value->qf_uid.',';
                }
                $uidss = trim($uidss,',');
                Yii::app()->controller->sendNotice('您好，您的项目'.($this->plot?$this->plot->title:'').'有新的认购',$uidss);
            }
            Yii::app()->controller->sendNotice(($this->plot?$this->plot->title:'').'有新的认筹，请登陆后台审核','',1);
        }
        if($this->status==3) {
            $company = CompanyExt::model()->findByPk($this->plot->company_id);
            $managers = $company->managers;
            if($managers) {
                $uidss = '';
                foreach ($managers as $key => $value) {
                    $value->qf_uid && $uidss .= $value->qf_uid.',';
                }
                $uidss = trim($uidss,',');
                Yii::app()->controller->sendNotice('您好，您的项目'.($this->plot?$this->plot->title:'').'有新的认购',$uidss);
            }
            Yii::app()->controller->sendNotice(($this->plot?$this->plot->title:'').'有新的认购，请登陆后台审核','',1);
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