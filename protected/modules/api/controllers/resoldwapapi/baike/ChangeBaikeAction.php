<?php
/**
 * 百科换一换
 * @author steven allen <[<email address>]>
 * @date 2016.10.25
 */
class ChangeBaikeAction extends CAction
{
	public function run()
	{
		$tag = Yii::app()->request->getQuery('tag',0);
		$cate = Yii::app()->request->getQuery('cate',0);
		$type = Yii::app()->request->getQuery('type',0);
		$data = [];
		if((!$tag || !$type) && !$cate)
		{
			$this->controller->returnError('参数错误');
		}
		if($tag)
		{
			
			$tags = BaikeTagExt::model()->findAll(['condition'=>'cate=:cate','params'=>[':cate'=>$type-1],'order'=>'RAND()','limit'=>20]);
			if($tags)
				foreach ($tags as $key => $v) {
					 $data[] = ['id'=>$v->id,'name'=>$v->name]; 
				}
			
		}
		elseif($cate)
		{
			$criteria = new CDbCriteria();
			$criteria->addCondition('cid=:cid');
            $criteria->params[':cid'] = $cate;
			$criteria->order = 'RAND()';
			$baikes = BaikeExt::model()->getList($criteria,3);
			if($baikes = $baikes->data)
			{
				$baike = [];
				foreach ($baikes as $key => $v) {
					$baike[] = ['id'=>$v->id,'title'=>$v->title,'description'=>$v->description,'image'=>$v->image];
				}
				$data['baike'] = $baike;
			}

		}
		$this->controller->frame['data'] = $data;
	}
}