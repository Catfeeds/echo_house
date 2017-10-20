<?php
/**
 * 服务的控制器
 */
class ServeController extends HomeController{
	/**
	 * 服务项目
	 */
	public function actionIndex()
	{
		$info = ArticleExt::model()->getFw()->normal()->find();
		// var_dump($info->attributes);exit;
		$this->render('index',['info'=>$info]);
	}
	/**
	 * 业务项目
	 */
	public function actionInfo($id='')
	{
		$infos = ArticleExt::model()->getYw()->normal()->findAll();
		$cates = $info = [];
		if($infos) {
			foreach ($infos as $key => $value) {
				$cates[$value->id] = $value->attributes;
			}
		}
		if($id && isset($cates[$id])) {
			$info = $cates[$id];
		} elseif($infos) {
			$info = $infos[0];
		}
		$this->render('info',['cates'=>$cates,'info'=>$info]);
	}
}