<?php
/**
 * 缓存行为类
 */
class CacheBehavior extends CActiveRecordBehavior
{
    Const CACHE_MAP_NAME = 'modelUpdateMap';

    public $cacheExp = 0;
    public $modelName = false;
    public $modelUpdateMap = false;

    /**
     * afterSave事件
     */
    public function afterSave($event)
    {    
      $this->updateModelMap();
      return parent::afterSave($event);
    }

    /**
     * afterDelete事件
     */
    public function afterDelete($event)
    {
      $this->updateModelMap();
      return parent::afterDelete($event);
    }

    /**
     * 更新模型修改时间
     * @return true
     */
    protected function updateModelMap()
    {
      if($this->modelName === false)
        $this->modelName = get_class($this->owner);

      $this->modelUpdateMap = Yii::app()->cache->get(self::CACHE_MAP_NAME);

      if($this->modelUpdateMap === false)
        $this->modelUpdateMap = array();

      $this->modelUpdateMap = CMap::mergeArray($this->modelUpdateMap, array($this->modelName=>time()));

      Yii::app()->cache->set(self::CACHE_MAP_NAME, $this->modelUpdateMap, $this->cacheExp);

      return true;
    }
}
