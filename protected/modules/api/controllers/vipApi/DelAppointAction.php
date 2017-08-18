<?php
/**
 * 取消预约
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class DelAppointAction extends CAction
{
	public function run()
	{
		$staff = $this->controller->staff;
		$fid = Yii::app()->request->getQuery('fid',0);
		$type = Yii::app()->request->getQuery('type',0);
		$id = Yii::app()->request->getQuery('id',0);
		
		if(!$id)
		{
			if(!$fid || !$type)
				$this->controller->returnError('参数错误');
			if(ResoldAppointExt::model()->deleteALLByAttributes(['fid'=>$fid,'uid'=>$staff->uid,'type'=>$type,'status'=>0]))
			{
				$model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
				$model->appoint_time = 0;
				$model->save();
			}
			else
				$this->controller->returnError('删除失败');
		}
		else
		{	
			$appoint = ResoldAppointExt::model()->findByPk($id);
			if(!$appoint)
				return $this->controller->returnError('未存在此条预约');
			ResoldAppointExt::model()->deleteALLByAttributes(['id'=>$id]);// bug 房源信息中的appoint_time无用
			// $model = $appoint->type==1 ? ResoldEsfExt::model()->findByPk($appoint->fid) : ResoldZfExt::model()->findByPk($appoint->fid);
			// $model->appoint_time = 0;
			// $model->save();
		}

	}
}