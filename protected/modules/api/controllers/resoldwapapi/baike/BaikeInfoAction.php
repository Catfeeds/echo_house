<?php
/**
 * 百科详情页
 * @author steven allen <[<email address>]>
 * @date 2016.10.25
 */
class BaikeInfoAction extends CAction
{
	public function run()
	{
		$id = Yii::app()->request->getQuery('id',0);
		if(!$id)
			$this->controller->returnError('参数错误');
		$baike = BaikeExt::model()->findByPk($id);
		if(!$baike)
			$this->controller->returnError('无词条百科');
		else
		{
			$data = $baike->attributes;
			$data['tag'] = [];
			$tags = $baike->tag?explode(',', $baike->tag):[];
			if($tags)
				foreach ($tags as $key => $value) {
					//此处留坑
					$tag = BaikeTagExt::model()->find(['condition'=>'name=:name','params'=>[':name'=>$value],'order'=>'created desc','limit'=>1]);
					$data['tag'][] = ['id'=>$tag->id,'name'=>$value];
				}
			$this->controller->frame['data'] = $data;
		}
	}
}