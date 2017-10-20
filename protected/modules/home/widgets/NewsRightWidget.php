<?php
class NewsRightWidget extends CWidget
{
	public $cid = '0';
	public $infoid = '';

	public function run()
	{
		// 热门文章
		$criteria = new CDbCriteria;
		$criteria->limit = 6;
		$criteria->order = 'hits desc';
		$criteria->addCondition('id<>:id');
		$criteria->params[':id'] = $this->infoid;
		if($this->cid) {
			$criteria->addCondition('cid=:cid');
			$criteria->params[':cid'] = $this->cid;
		}
		$news = ArticleExt::model()->normal()->findAll($criteria);
		$albums = TkExt::model()->normal()->findAll(['limit'=>10]);
		$tags = Yii::app()->db->createCommand("select tid,name,count(tid) as ct from article_tag group by tid order by ct desc limit 20")->queryAll();
		// $tagsArr = [];
		// if($tags) {
		// 	foreach ($tags as $key => $value) {
		// 		$tagsArr[] = ['id'=>$value['id'],'name'=>$value['name']];
		// 	}
		// }
		$this->render('newsright',['news'=>$news,'tags'=>$tags,'albums'=>$albums]);
	}
}