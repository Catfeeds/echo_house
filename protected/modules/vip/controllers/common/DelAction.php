<?php
/**
 * 删除操作 
 * 支持批量 和 getpost操作
 */
class DelAction extends CAction{
	public function run()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$id = Yii::app()->request->getPost('id','');
			$class = Yii::app()->request->getPost('class','');
		} else {
			$id = Yii::app()->request->getQuery('id','');
			$class = Yii::app()->request->getQuery('class','');
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
			$model->deleted = 1;
			if(!$model->save())
				$this->controller->setMessage(current(current($model->getErrors())),'error');
		}
		$this->controller->setMessage('操作成功','success');	
	}
}