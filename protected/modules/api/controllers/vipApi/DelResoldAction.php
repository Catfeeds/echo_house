<?php
/**
 * 删除房源
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class DelResoldAction extends CAction
{
	public function run()
	{
		$fid = Yii::app()->request->getPost('fid');
		$type = Yii::app()->request->getPost('type');
		if(!$fid || !$type)
			$this->controller->returnError('参数错误');
		else
		{
			$fid = explode(',', $fid);
			if(is_array($fid))
            {
                foreach (array_filter($fid) as $key => $v) {
                    $model = $type==1 ? ResoldEsfExt::model()->findByPk($v) : ResoldZfExt::model()->findByPk($v);
                    // 删除就回收且未审核
	                $model->sale_status = 3;
	                $model->status = 0;
                    if(!$model->save())
                    {
			            $errors = $model->getErrors();
			            return $this->getController()->returnError(current($errors)[0]);
                    }
                }
            }
            else
            {
            	$model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
				// 删除就回收且未审核
                $model->sale_status = 3;
                $model->status = 0;
				if(!$model->save())
                {
		            $errors = $model->getErrors();
		            return $this->getController()->returnError(current($errors)[0]);
                }
            }
            $this->controller->frame['msg'] = '删除成功';
		}

	}
}