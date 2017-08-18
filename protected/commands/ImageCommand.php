<?php 
/**
 * 自动抓取图片到七牛
 */
class ImageCommand extends CConsoleCommand{
	/**
	 * [actionNews 文章]
	 * @return [type] [description]
	 */
	public function actionNews()
	{
		$page = 0;
		begin:
		$limit = $page*200;
		// 文章图片
		$imgs = Yii::app()->db->createCommand("select id,image from article where image like 'http%' limit $limit,200")->queryAll();
		if($imgs) {
			foreach ($imgs as $key => $value) {
				$image = Yii::app()->file->fetch($value['image']);
				// var_dump($image);exit;
				$thisid = $value['id'];
				Yii::app()->db->createCommand("update article set image='$image' where id=$thisid")->execute();
			}
			$page++;
			$count = $page*200;
			echo "finished $count===============\n";
			
			goto begin;
		} else {
			echo "finished";
		}

		
	}
	/**
	 * [actionAlbum 图库]
	 * @return [type] [description]
	 */
	public function actionAlbum()
	{
		$page = 0;
		begin:
		$limit = $page*200;
		// 文章图片
		$imgs = Yii::app()->db->createCommand("select id,url from album where url like 'http%' limit $limit,200")->queryAll();
		if($imgs) {
			foreach ($imgs as $key => $value) {
				$image = Yii::app()->file->fetch($value['url']);
				// var_dump($image);exit;
				$thisid = $value['id'];
				Yii::app()->db->createCommand("update album set url='$image' where id=$thisid")->execute();
			}
			$page++;
			$count = $page*200;
			echo "finished $count===============\n";
			
			goto begin;
		} else {
			echo "finished";
		}

		
	}
}