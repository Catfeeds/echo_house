<?php
/**
 * 推荐接口
 * @author steven allen <[<email address>]>
 * @date 2016.10.20
 */
class RecomAction extends CAction
{
	public function run()
	{
		// 推荐分类
		$cate = Yii::app()->request->getQuery('cate','');
		$category = Yii::app()->request->getQuery('category','');
		$limit = Yii::app()->request->getQuery('limit',6);

		$data = $esfs = $zfs = [];

		if(!$cate)
		{
			$this->controller->frame['status'] = 'error';
			$this->controller->frame['msg'] = '请求参数错误';

		}
		else
		{
			$sql = "select r.* from resold_recom r left join resold_recom_cate c on r.cid=c.id where c.pinyin='$cate' order by r.sort desc,r.created desc limit $limit";
			$recoms = Yii::app()->db->createCommand($sql)->queryAll();
			// $recoms = ResoldRecomExt::model()->getRecom($cate)->findAll(['limit'=>$limit,'order'=>'t.sort desc,t.created desc']);
			if($cate=='wapsyzb')
			{
				$top = ResoldRecomExt::model()->getRecom('wapsyzbtldb')->find(['limit'=>1,'order'=>'t.sort desc,t.created desc']);
				$left = ResoldRecomExt::model()->getRecom('wapsyzbtlzb')->find(['limit'=>1,'order'=>'t.sort desc,t.created desc']);
				$right = ResoldRecomExt::model()->getRecom('wapsyzbtlyb')->find(['limit'=>1,'order'=>'t.sort desc,t.created desc']);
				$data['top'] = $top?['image'=>ImageTools::fixImage($top->image),'url'=>$top->url]:[];
				$data['left'] = $left?['image'=>ImageTools::fixImage($left->image),'url'=>$left->url]:[];
				$data['right'] = $left?['image'=>ImageTools::fixImage($right->image),'url'=>$right->url]:[];
				return $this->controller->frame['data'] = $data;
			}
			if($recoms)
				foreach ($recoms as $key => $value) {
					$recomesfs = $recomzfs = $model = [];
					$model = $value;
					$config = $model['config'];
					$model['isAd'] = isset($config['isAd'])?$config['isAd']:0;
					$model['image'] = ImageTools::fixImage($model['image']);
					if($value['fid'] && $value['type']==1)
						$recomesfs[] = ResoldEsfExt::model()->findByPk($value['fid']);
					if($value['fid'] && $value['type']==2)
						$recomzfs[] = ResoldZfExt::model()->findByPk($value['fid']);
					
					if($recomesfs)
						foreach ($recomesfs as $key => $v) {
							$image = $model['image'] ? $model['image'] : $v->image;
							$farea = AreaExt::model()->findByPk($v->area);
							$esfs[] = ['id'=>$v->id,'title'=>$model['title'],'image'=>ImageTools::fixImage($image,88,66),'bedroom'=>$v->bedroom,'livingroom'=>$v->livingroom,'bathroom'=>$v->bathroom,'area'=>$farea?$farea->name:'无','price'=>$v->price,'size'=>$v->size,'isAd'=>$model['isAd']];
						}
					if($recomzfs)
						foreach ($recomzfs as $key => $v) {
							$image = $model['image'] ? $model['image'] : $v->image;
							$farea = AreaExt::model()->findByPk($v->area);
							$zfs[] = ['id'=>$v->id,'title'=>$model['title'],'image'=>ImageTools::fixImage($image,88,66),'bedroom'=>$v->bedroom,'livingroom'=>$v->livingroom,'bathroom'=>$v->bathroom,'area'=>$farea?$farea->name:'无','price'=>$v->price,'size'=>$v->size,'isAd'=>$model['isAd']];
						}
					// if($esfs)
					// 	$model['info'] = $esfs;
					// if($zfs)
					// 	$model['info'] = $zfs;
					$data[] = $model;
				}	
			
			$this->controller->frame['data'] = $esfs?$esfs:($zfs?$zfs:$data);
		}
	}
}