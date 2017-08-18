<?php
/**
 * 迅搜专用的DataProvider
 * @author tivon
 * @date 2015-09-24
 */
class XsDataProvider extends CDataProvider
{
    /**
     * mysql数据库查询条件
     * @var CDbCriteria
     */
    public $criteria;
    /**
     * 迅搜实例
     * @var XunSeach
     */
    private $xs;
    /**
     * 用到的AR类模型类名
     * @var string
     */
    private $modelClass;
    /**
     * 用到的AR类对象
     * @var CActiveRecord
     */
    private $model;

    /**
     * 构造函数
     * @param string $projectName 迅搜项目名称
     * @param CActiveRecord $class  模型对象
     * @param array  $config [description]
     */
    public function __construct($projectName,CActiveRecord $modelClass, $config = array())
    {
        $this->xs = Yii::app()->search->$projectName;
		$this->modelClass=get_class($modelClass);
		$this->model=$modelClass;
		$this->setId(CHtml::modelName($this->model));
		foreach($config as $key=>$value)
			$this->$key=$value;
        if(empty($this->criteria->facetsField)){
            throw new Exception("facets 字段未设置");
        }
    }



    /**
     * 查询数据
     * @return CActiveRecord[]
     */
    public function fetchData()
    {
		$this->xs->setQuery($this->criteria->query);
		$this->xs->setFacets(array($this->criteria->facetsField), true);
		foreach($this->criteria->rangeCondition as $condition)
		{
			list($field, $from ,$to) = $condition;
			$this->xs->addRange($field, $from, $to);
		}
        //先加条件再统计数量
        $this->calculateTotalItemCount();
        //排序放在统计之后
        if($this->criteria->order){
            $this->xs->setMultiSort($this->criteria->order);
        }
        $this->xs->setLimit($this->getPagination()->pageSize, $this->getPagination()->offset);
        $xsResults = $this->xs->search();
        //迅搜结果转数据库结果
        $data = array();
        $ids = array();
        foreach($xsResults as $k=>$v)
        {
            $ids[] = $v['id'];
        }
        $alias = $this->model->getTableAlias();
        $criteria = new CDbCriteria();
        if($this->criteria->order) {
            $order = [];
            foreach($this->criteria->order as $field=>$value){
                $order[] = $field.' '.($value?'asc':'desc');
            }
            if($order){
                $criteria->order = implode(',', $order);
            }
        }
        $criteria->addInCondition($alias.'.id', $ids ? $ids : array(0));
        return $this->model->findAll($criteria);
    }

    public function fetchKeys()
    {

    }

    /**
     * 需要模型定义xsCount()函数，用于统计数量
     * @return void
     */
    protected function calculateTotalItemCount()
    {
        $this->xs->search();//分面搜索前需要调用一次search
        $total = array_sum($this->xs->getFacets($this->criteria->facetsField));
        $this->getPagination()->setItemCount($total);
    }
}
?>
