<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotAskExt extends PlotAsk{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'answers'=>array(self::HAS_MANY, 'PlotAnswerExt', 'aid','condition'=>'answers.status=1','order'=>'answers.sort desc,answers.updated desc'),
            // 'answers_count'=>array(self::STAT, 'PlotAnswerExt', 'aid','condition'=>'answers_count.status=1'),
            
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
            // $this->status = 1;
            $this->created = $this->updated = time();
            $res = Yii::app()->controller->sendNotice('有新的用户对'.$this->plot->title.'进行提问，请登陆后台审核','',1);
        }
        else
            $this->updated = time();
        return parent::beforeValidate();
    }

    public function afterSave()
    {
        parent::afterSave();
        
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
                'condition' => "{$alias}.status=1",
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