<?php
/**
 * 百科标签
 * @author steven allen <[<email address>]>
 * @date 2016.10.25
 */
class BaikeTagListAction extends CAction
{
	public function run()
	{
		$type = Yii::app()->request->getQuery('type',0);
		$recom = Yii::app()->request->getQuery('recom',0);
		$limit = Yii::app()->request->getQuery('limit',20);
		if(!$type)
			$this->controller->returnError('参数错误');
		$data = [];
		$criteria = new CDbCriteria;
		$criteria->addCondition('cate=:cate');
		$criteria->params[':cate'] = $type-1;
		if($recom)
		{
			$criteria->addCondition('recom>0');
		}
		$criteria->order = 'recom desc,sort desc';
		$criteria->limit = $limit;
		$tags = BaikeTagExt::model()->findAll($criteria);
		if($tags)
			foreach ($tags as $key => $v) {
				 $data[] = ['id'=>$v->id,'name'=>$v->name]; 
			}
		$this->controller->frame['data'] = $data;
	}
}