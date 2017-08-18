<?php
/**
 * AR基础行为类
 * 通过一个基础行为类，来实现类似继承效果
 * 由于历史原因，生成的model类是直接继承框架中的AR类的，所以就不改其继承的父类了，而是通过行为类来实现。
 * @author tivon
 * @version 2016-11-11
 */
class BaseBehavior extends CActiveRecordBehavior
{
    //==========================基本方法=============================
    /**
     * 获取数据表对应业务上的中文名称
     * @return string
     */
    public function getTableChineseName()
    {
        $map = [
            'ArticleExt' => '文章资讯',
            'AskExt' => '问答',
            'BaikeExt' => '百科',
            'PlotExt' => '楼盘',
            'PlotBuildingExt' => '楼栋',
            'PlotImgExt' => '楼盘相册',
            'PlotHouseTypeExt' => '楼盘户型',
            'PlotKanExt' => '看房团',
            'PlotRedExt' => '楼盘红包',
            'PlotSpecialExt' => '特价房',
            'PlotTuanExt' => '特惠团',
        ];
        $class = get_class($this->owner);
        return isset($map[$class]) ? $map[$class] : '';
    }

    //==========================查询辅助============================
    /**
	 * 迅搜查询条件
	 * @var XsCriteria
	 */
	private $_xsCriteria;

	/**
     * 获得指定条件的列表
     * @param  CDbCriteria $criteria 查询条件类
     * @param  integer     $pageSize 分页每页的显示数量
     * @return DataProvider
     */
    public function getList($criteria=array(), $pageSize=15)
    {
    	// var_dump($this->owner->getDbCriteria());die;
        if(is_array($criteria))
            $criteria = new CDbCriteria($criteria);
        return new CActiveDataProvider($this->getOwner(), array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $pageSize,
                'pageVar' => 'page',
            )
        ));
    }

	/**
	 * 获得迅搜指定条件搜索结果列表
	 * @param  $projectName string  迅搜项目名称
	 * @param  XsCriteria  $criteria 迅搜查询条件类或数组
	 * @param  integer $pageSize 分页每页的显示数量
	 * @return DataProvider
	 */
	public function getXsList($projectName,$criteria=array(), $pageSize=15)
	{
		$this->getXsCriteria()->mergeWith($criteria);
		return new XsDataProvider($projectName, $this->getOwner(), array(
			'criteria' => $this->getXsCriteria(),
			'pagination' => array(
				'pageSize' => $pageSize,
				'pageVar' => 'page',
			)
		));
	}

	/**
	 * 获得迅搜查询条件对象
	 * @return XsCriteria
	 */
	public function getXsCriteria()
	{
		if($this->_xsCriteria===null)
			$this->_xsCriteria = new XsCriteria;
		return $this->_xsCriteria;
	}

    /**
	 * API获取Attributes
	 */
	public function getAPIAttributes($params=array(), $relations = false , $exclude=false)
	{
		$attributes = array();
		foreach ($this->getOwner()->attributes as $k=>$v)
		{
			if($exclude){
				if(in_array($k,$params))
					continue;
				$attributes[$k] = $v;
			}else {
				if (in_array($k, $params)) {
					$attributes[$k] = $v;
				}
				continue;
			}
		}

		if ($relations != false)
		{
			foreach ($relations as $relation=>$params)
			{
				if (is_integer($relation))
				{
					$relation = $params;
					$params = array();
				}

				if (is_array($this->getOwner()->$relation))
				{
					$attributes[$relation] = array();
					foreach ($this->getOwner()->$relation as $k)
						$attributes[$relation][] = $k->getAPIAttributes($params);
				}
				else
				{
					if (isset($this->getOwner()->$relation))
						$attributes[$relation] = $this->getOwner()->$relation->getAPIAttributes($params);
					else
						$attributes[$relation] = array();
				}
			}
		}
		// isset($attributes['image']) && $attributes['image'] && $attributes['image'] = ImageTools::fixOldImage($attributes['image']);
		return $attributes;
	}
}
