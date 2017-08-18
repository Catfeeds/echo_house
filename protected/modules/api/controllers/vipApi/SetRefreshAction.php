<?php
/**
 * 刷新 操作
 * @author steven allen <[<email address>]>
 * @date 2016.10.24
 */
class SetRefreshAction extends CAction
{
	public function run()
	{
		$staff = $this->controller->staff;
		$fid = Yii::app()->request->getQuery('fid');
		$type = Yii::app()->request->getQuery('type',0);
		$refreshInterval = SM::resoldConfig()->resoldRefreshInterval->value*60;
		$expireTime = SM::resoldConfig()->resoldExpireTime() * 86400;
		if(!$fid || !$type)
			$this->controller->returnError('参数错误');
		else
		{
			if(strstr($fid,','))
			{
				$fid = explode(',', $fid);
				$success = $error = 0;
				foreach (array_filter($fid) as $key => $v) {
					$model = $type==1 ? ResoldEsfExt::model()->findByPk($v) : ResoldZfExt::model()->findByPk($v);
					$table = $type==1 ? 'resold_esf' : 'resold_zf';
					if(time() - $model->refresh_time >= $refreshInterval)
					{
						if(Yii::app()->db->createCommand('update '.$table.' set refresh_time='.time().',expire_time='.(time()+$expireTime).' where id='.$model->id)->execute()) {
							$success += 1;
						}
					}
					else
					{
						$error += 1;
					}
				}
				$this->controller->frame['msg'] = '刷新成功'.$success.'条，失败'.$error.'条';
			}
			else
			{
				$model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
				if(time() - $model->refresh_time < $refreshInterval)
					$this->controller->returnError('房源在刷新状态，请稍后操作');
				else
				{
					$model->refresh_time = time();
					$model->expire_time = time() + $expireTime;
					$model->save();
					$this->controller->frame['msg'] = '保存成功';
				}
			}
			
		}

	}
}