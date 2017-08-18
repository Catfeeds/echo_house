<?php
/**
 * 添加收藏
 * @author steven allen <[<email address>]>
 * @date(2016.11.3)
 */
class CheckAction extends CAction
{
	public function run()
	{
		$fid = Yii::app()->request->getQuery('fid',0);
		$type = Yii::app()->request->getQuery('category',0);
		if(!$fid || !$type)
			return $this->controller->returnError('参数错误');
		$collect = ResoldUserCollectionExt::model()->conditionUid($this->getController()->uid)->find('house_type=:type and house_id=:house_id',array(':type'=>$type,':house_id'=>$fid));
		if($collect)
			return $this->controller->frame['data'] = ['code'=>1,'id'=>$collect->id];
		else
			return $this->controller->frame['data'] = ['code'=>0];
	}
}