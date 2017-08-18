<?php
/**
 * 迅搜搜索条件对象
 * 使用方法类似CDbCriteria
 * ```
 * $criteria = new XsCriteria(array(
 * 		'order'=>array('created'=>false,'id'=>true),
 * 		'facetsField' => 'status'
 * ));
 * $criteria->addRage('status', 1, 1);
 * $dataProvider = CActiveRecord::model()->getXsList($criteria,15);
 * ```
 *
 * @author tivon
 * @date 2015-12-23
 */
class XsCriteria extends CComponent
{
    /**
     * 搜索关键词
     * @var string
     */
    public $query = '';
    /**
     * 设置排序字段，该参数将应用于迅搜setMultiSort函数上
     * @var array
     */
    public $order = array();
    /**
     * 分面记数字段
     * @var string 该值必须是在ini配置文件中存在的字段
     */
    public $facetsField;
    /**
     * addrange查询条件
     * @var array
     */
    public $rangeCondition = array();

    /**
     * 构造方法
     * @param array $data
     */
    public function __construct($data=array())
	{
		foreach($data as $name=>$value)
			$this->$name=$value;
	}

    /**
     * 增加字段筛选条件
     * @param string $field 字段名，需要在ini中配置过
     * @param mixed $from  	起始值(不包含), 若设为 null 则相当于匹配 <= to (字典顺序)
     * @param mixed $to    结束值(包含), 若设为 null 则相当于匹配 >= from (字典顺序)
     */
    public function addRange($field,$from,$to)
	{
        $this->rangeCondition[] = array($field, $from, $to);
	}

    /**
     * 合并查询条件
     * @param  mixed $criteria  需要合并的查询条件，可以是数组也可以使XsCriteria对象
     * @return void
     */
    public function mergeWith($criteria)
    {
        if(is_array($criteria))
            $criteria = new self($criteria);
        $this->query = $criteria->query;
        $this->order = array_merge($this->order, $criteria->order);
        if($criteria->facetsField)
            $this->facetsField = $criteria->facetsField;
        $this->rangeCondition = array_merge($this->rangeCondition, $criteria->rangeCondition);
    }
}
