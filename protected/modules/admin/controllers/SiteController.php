<?php
/**
 * 站点配置
 * @author steven.allen
 * @date 2017.2.13
 */
class SiteController extends AdminController
{
	public function actionList()
	{
		
		// 页面初始化
		$sites = [];
		foreach (SiteExt::$cateTag as $key => $value) {
			$sites[$key] = SiteExt::$cateName[$key];
		}

		$this->render('list',['sites'=>$sites]);
	}

	public function actionEdit($type='')
	{
		$model = SiteExt::model()->find(['condition'=>'name=:name','params'=>[':name'=>$type]]) ? SiteExt::model()->find(['condition'=>'name=:name','params'=>[':name'=>$type]]) : new SiteExt;
		$model->name = $type;
		// post请求
		if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('SiteExt',[]);
			
			// var_dump($values);exit;
			if($values) {
				$model->name = $type;
				$model->value = json_encode($values);
				if(!$model->save())
					$this->setMessage(array_values($model->errors)[0][0],'error');
			}
			$this->setMessage('操作成功！','success');
			$this->redirect('/admin/site/list');
			Yii::app()->end();
		}

		if($type) {
			

			$this->render('edit',['cate'=>$type,'model'=>$model]);
		} else {
			$this->redirect('list');
		}
	}
}