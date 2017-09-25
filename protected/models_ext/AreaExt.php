<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class AreaExt extends Area{
    /**
     * 状态
     * @var array
     */
    public static $status = array(
        0 => '禁用',
        1 => '启用',
    );
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'team'=>array(self::BELONGS_TO, 'TeamExt', 'tid'),
            'childArea' => array(self::HAS_MANY, 'AreaExt', 'parent','condition'=>'childArea.status=1 and childArea.deleted=0','order'=>'childArea.sort asc'),//子级区域
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
        if($this->getIsNewRecord())
            $this->created = $this->updated = time();
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
            'parent' =>array(
              'condition'=>  "{$alias}.parent = 0"
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

    /**
     * 根据父级id获取下级区域
     * @param  integer $parent 父级区域id
     * @return AreaExt
     */
    public function getByParent($parent)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'parent=:parent',
            'params' => array(':parent'=>$parent)
        ));
        return $this;
    }

    public static function getAll() {
        return CacheExt::gas('all_area','AreaExt',0,'全部区域',function (){
            $data = [];
            foreach (AreaExt::model()->normal()->findAll() as $key => $value) {
                $data[$value->id] = $value->name;
            }
            return $data;
        });
    }

}