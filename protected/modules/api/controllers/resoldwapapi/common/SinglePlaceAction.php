<?php
/**
 * 获取单个位置接口
 * @author steven allen <[<email address>]>
 * @date(2016.10.31)
 */
class SinglePlaceAction extends CAction
{
	public function run()
	{
        $areaid = Yii::app()->request->getQuery('areaid',0);
        $streetid = Yii::app()->request->getQuery('streetid',0);
    	$area = AreaExt::model()->findByPk($areaid);
    	$street = AreaExt::model()->findByPk($streetid);
    	$areaInfo = ['id'=>$areaid,'name'=>$area?$area->name:'不限'];
    	$streetInfo = ['id'=>$streetid,'name'=>$street?$street->name:'不限'];
    	$this->controller->frame['data'] = ['area'=>$areaInfo,'street'=>$streetInfo];
    }
}