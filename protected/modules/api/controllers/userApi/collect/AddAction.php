<?php
/**
 * 添加收藏
 * @author steven allen <[<email address>]>
 * @date(2016.11.3)
 */
class AddAction extends CAction
{
	public function run()
	{
		$fid = Yii::app()->request->getPost('fid',0);
		$type = Yii::app()->request->getPost('category',0);
		$uid = $this->controller->uid;
		// var_dump($uid);exit;
		if(!$fid || !$type)
			return $this->controller->returnError('参数错误');
		$model = new ResoldUserCollectionExt;
		$model->house_id = $fid;
		$model->uid = $uid;
		$model->house_type = (int)$type;
		if(!$model->save())
		{
			$errors = $model->getErrors();
            return $this->getController()->returnError(current($errors)[0]);
		}
		else
			return $this->controller->frame = array(
				'status'=>'success',
				'msg'=>'收藏成功',
				'data'=>['id'=>$model->id]
			);
	}
}