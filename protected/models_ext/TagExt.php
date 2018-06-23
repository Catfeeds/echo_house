<?php 
/**
 * 标签类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.5)
 */
class TagExt extends Tag
{
    /**
     * 新房标签分类
     * @var array
     */
    static $xinfangCate = [
        //直接式标签
        'direct' => [
            'wzlm' => '项目列表顶部标签',
            'wylx' => '物业类型',
            'zxzt' => '装修状态',
            'xszt' => '销售状态',
            
            'sfprice' => '首付金额',
            'pricetype' => '付款选项',
            // 'hjxl' => '红酒系列',
            // 'jzdq' => '酒庄地区',
            // 'jzdj' => '酒庄等级',
            // 'hjdq' => '红酒地区',
            // 'hjjg' => '红酒价格',
            // 'zdpz' => '站点配置'
        ],
        //区间式标签，区间式标签可以增删
        'range' => [
            'price' => '均价筛选',
        ]
    ];

    /**
     * 标签状态
     * @var array
     */
    static $status = array(
        0 => '禁用',
        1 => '启用',
    );

    /**
     * 标签状态样式
     * @var array
     */
    static $statusStyle = array(
        0 => 'btn btn-sm grey',
        1 => 'btn btn-sm blue',
    );

    /**
     * 关联关系
     * @return array
     */
    public function relations()
    {
        return array(
            'anum'=>array(self::STAT, 'ArticleTagExt', 'tid'),
            'arel'=>array(self::HAS_MANY, 'ArticleTagExt', 'tid'),
            // 'plot_rel' => array(self::HAS_MANY, 'TagRelExt', 'tag_id', 'joinType'=>'INNER JOIN'),//关联中间表，一对多
        );
    }

    /**
     * 返回当前类的实例
     * @param string $className active record class name.
     * @return CActiveRecord
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 根据标签分类拼音获取分类名称
     * @param  [type] $pinyin [description]
     * @return [type]         [description]
     */
    public static function getCateNameByPinyin($pinyin)
    {
        if($name = self::getXinfangCateNameByPinyin($pinyin)) {
            return $name;
        } else {
            return self::getResoldCateNameByPinyin($pinyin);
        }
    }

    /**
     * 根据拼音名获取分类名称
     * @param  string $pinyin 标签分类的拼音标识
     * @return string         直接返回分类名称，若不存在则返回空字符串
     */
    private static function getXinfangCateNameByPinyin($pinyin)
    {
        return isset(self::$xinfangCate[$pinyin]) ? self::$xinfangCate[$pinyin] : '';
    }

    /**
     * 根据拼音名获取标签分类名称
     * @param  string $pinyin 标签分类的拼音标识
     * @return string         直接返回分类名称，若不存在则返回空字符串
     */
    private static function getResoldCateNameByPinyin($pinyin)
    {
        foreach(self::$resoldCate as $cates) {
            if(isset($cates[$pinyin])){
                return $cates[$pinyin];
            }
        }
        return '';
    }

    /**
     * beforeValidate事件
     */
    public function beforeValidate()
    {
        // $this->name && $this->pinyin = Pinyin::get($this->name);
        // $this->cate = 'wzlm';
        if($this->getIsNewRecord())
            $this->created = $this->updated = time();
        else
            $this->updated = time();
        return parent::beforeValidate();
    }

    public function init()
    {
        parent::init();
        $this->onAfterDelete = [$this, 'deleteAllByTagId'];
    }

    /**
     * 删除关联标签id
     * @return
     */
    public function deleteAllByTagId()
    {
    }

    /**
     * 命名范围，根据分类获取该分类下的所有标签
     * @param  string $cate 标签分类标识，{@see TagExt::$cate}
     * @return TagExt
     */
    public function getTagByCate($cate)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'cate=:cate',
            'order' => 'id ASC',
            'params' => array(':cate'=>$cate)
        ));
        return $this;
    }

    public static function tagCache()
    {
        return CacheExt::gas('allTag','TagExt',0,'标签缓存',function(){
            $list = self::model()->normal()->findAll(['order'=>'sort asc']);
            return $list;
        });
    }

    public static function getPtId(){
        return CacheExt::gas('PtId','TagExt',0,'配套分类id',function(){
            $arr = self::model()->normal()->find('name="配套图"');
            return $arr ? $arr->id : 0;
        });

    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            //正常启用的
            'normal' => array(
                'condition' => "{$alias}.status=1"
            ),
            'sorted' => array(
                'order' => "{$alias}.sort desc,{$alias}.updated desc"
            )
        );
    }

    /**
     * 更改标签状态
     * @return boolean 成功返回true，失败返回false
     */
    public function changeStatus()
    {
        if($this->status==1)
            $this->status = 0;
        else
            $this->status = 1;
        return $this;
    }

    /**
     * 绑定行为类
     */
    public function behaviors() {
        return array(
            'BaseBehavior'=>'application.behaviors.BaseBehavior',
        );
    }

    /**
     * 是否启用
     * @return array
     */
    public function getIsEnabled()
    {
        return $this->status==1;
    }

    /**
     * [getCateByTag 根据tagid获取分类]
     * @param  [type] $tag_id [description]
     * @return [type]         [description]
     */
    public static function getCateByTag($tag_id)
    {
        $tag = self::model()->findByPk($tag_id);
        return isset($tag) ? $tag->cate : '' ;
    }

    /*
     * 是否是直接式标签
     * @return boolean 是返回true，否返回false，则为区间式标签
     */
    public function getIsDirectTag()
    {
        if(isset(self::$xinfangCate['direct'][$this->cate]))
            return isset(self::$xinfangCate['direct'][$this->cate]);
    }

    /**
     * 根据cate分成的数组
     */
    public static function getAllByCate(){
        $all =  self::tagCache();
        $result = array();
        foreach ($all as $item){
            $result[$item->cate][] = $item->attributes;
        }
        return $result;
    }
    /*
    * 根据tagid获取name
    */
    public static function getNameByTag($tagid,$by_cate=false){
        if(!is_array($tagid)){
            if(empty($tagid))
                return null;
            return self::model()->normal()->find([
                'select'=>'name',
                'condition'=>'id=:id',
                'params'=>[':id'=>$tagid]
            ])?self::model()->normal()->find([
                'select'=>'name',
                'condition'=>'id=:id',
                'params'=>[':id'=>$tagid]
            ])->name:'';
        }else{
            $tagname = [];
            $criteria = new CDbCriteria;
            $id = implode(',',$tagid);
            $criteria->addInCondition('id',$tagid);
            $tagnames = self::model()->normal()->findAll($criteria);
            foreach($tagnames as $k=>$v){
                $by_cate ? $tagname[$v->cate][$v->id] = $v->name : $tagname[] = $v->name;
            }
            return $tagname;
        }
    }

    public static function getTagArrayByCate($cate)
    {
        $tags=self::model()->getTagByCate($cate)->normal()->findAll();
        $tagArray=[];
        foreach($tags as $tag){
            $tagArray[$tag->id]=$tag->name;
        }
        return $tagArray;
    }

    public static function getIdByPinyin($value='')
    {
        return TagExt::model()->find("pinyin='$value'")->id;
    }

    public function afterSave()
    {
        parent::afterSave();
    }
}