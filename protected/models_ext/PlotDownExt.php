<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotDownExt extends PlotDown{
    public static $status = [
        '未下架','已下架'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
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
        if($this->getIsNewRecord()) {
            // $this->status = 1;
            $this->created = $this->updated = time();
            $res = Yii::app()->controller->sendNotice('有新的用户对'.$this->plot->title.'进行下架操作，请登陆后台审核','',1);
        }
        else
            $this->updated = time();
        if($this->status==1) {
            $this->plot->status = 0;
            $this->plot->save();
        }
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