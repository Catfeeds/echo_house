<?php
/**
 * 二手房/租房百科分类文章
 * @author steven allen <[<email address>]>
 * @date 2016.10.25
 */
class BaikeListAction extends CAction
{
	public function run()
	{
		$type = Yii::app()->request->getQuery('type',0);
		$limit = Yii::app()->request->getQuery('limit',20);
		$cate = Yii::app()->request->getQuery('cate','');
		$kw = Yii::app()->request->getQuery('kw','');
		$tag = Yii::app()->request->getQuery('tag','');
		$data = [];
		if($tag)
		{
			$xsCriteria = new XsCriteria;
			$xsCriteria->addRange('status',1,1);
			$xsCriteria->facetsField = 'status';
			$xsCriteria->order = array('created'=>false);
			if($tag!='') {
				$xsCriteria->query = ' tag:'.$tag;
			}
			$dataProvider = BaikeExt::model()->getXsList('house_baike', $xsCriteria, 6);
			$baikes = $dataProvider->data;
			if($baikes)
				foreach ($baikes as $key => $v) {
					$data['list'][] = ['id'=>$v->id,'title'=>$v->title,'description'=>$v->description,'image'=>$v->image];
				}
			$data['tag'] = $tag;
		}
		else
		{
			if(!$type)
				$this->controller->returnError('参数错误');
			// 所有分类
			$allCates = [];
			$typeName = $type==2?'ershoufang':($type==1?'maixinfang':'zufang');
			$allCates = BaikeCateExt::model()->with('parentCate')->findAll(['condition'=>'parentCate.pinyin=:py','params'=>[':py'=>$typeName],'order'=>'t.sort desc']);
			if($cate)
			{
				if($allCates)
				foreach ($allCates as $key => $value) {
					$data['cates'][] = ['id'=>$value->id,'name'=>$value->name];
				}
				// 当前分类
				$cateModel = BaikeCateExt::model()->findByPk($cate);
				$data['cate_name'] = $cateModel->name;
				// 百科文章
				$criteria = new CDbCriteria();
				$criteria->addCondition('cid=:cid');
	            $criteria->params[':cid'] = $cate;
				$criteria->order = 'created DESC';
				$datas = BaikeExt::model()->getList($criteria,6);
				$baikes = $datas->data;
				if($baikes)
					foreach ($baikes as $key => $v) {
						$data['list'][] = ['id'=>$v->id,'title'=>$v->title,'description'=>$v->description,'image'=>$v->image];
					}
			}
			elseif(!$kw)
			{
				if($allCates)
					foreach ($allCates as $key => $value) {
						$tmp = ['id'=>$value->id,'name'=>$value->name];
						$criteria = new CDbCriteria();
						$criteria->addCondition('cid=:cid');
			            $criteria->params[':cid'] = $value->id;
						$criteria->order = 'created DESC';
						$baikes = BaikeExt::model()->getList($criteria,3);
						if($baikes = $baikes->data)
						{
							$baike = [];
							foreach ($baikes as $key => $v) {
								$baike[] = ['id'=>$v->id,'title'=>$v->title,'description'=>$v->description,'image'=>$v->image];
							}
							$tmp['baike'] = $baike;
						}
						if($baikes)
							$data[] = $tmp;	
					}

			}
			else
			{
				$ids = $data['list'] = [];
				$tmp = [];
				if($allCates)
					foreach ($allCates as $key => $value) {
						$ids[] = $value->id;
					}
				$criteria = new CDbCriteria();
				$criteria->addSearchCondition('title',$kw);

	            if($ids)
	            	$criteria->addInCondition('cid',$ids);

				$criteria->order = 'created DESC';
				$baikes = BaikeExt::model()->getList($criteria,$limit);

				if($baikes = $baikes->data)
				{
					$baike = [];
					foreach ($baikes as $key => $v) {
						$baike[] = ['id'=>$v->id,'title'=>$v->title,'description'=>$v->description,'image'=>$v->image];
					}
					if($baike)
						$data['list'] = $baike;
				}
				$data['kw'] = $kw;	
			}
		}
			
		$this->controller->frame['data'] = $data;
	}
}