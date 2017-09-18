<?php
/**
 * 排序操作 
 * 支持批量 和 getpost操作
 */
class SetSortAction extends CAction{
	public function run()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$id = Yii::app()->request->getPost('id','');
			$class = Yii::app()->request->getPost('class','');
			$sort = Yii::app()->request->getPost('sort',0);
		} else {
			$id = Yii::app()->request->getQuery('id','');
			$class = Yii::app()->request->getQuery('class','');
			$sort = Yii::app()->request->getQuery('sort',0);
		}
		if(!$id||!$class) {
			return $this->controller->setMessage('参数错误','error');
		}
		if(!is_array($id))
			if(strstr($id,',')) {
				$ids = explode(',', $id);
			} else {
				$ids = [$id];
			}
		foreach ($ids as $key => $id) {
			$model = $class::model()->findByPk($id);
			$model->sort = $sort;
			if(!$model->save())
				$this->controller->setMessage(current(current($model->getErrors())),'error');
		}
		$this->controller->setMessage('操作成功','success');	
	}
}