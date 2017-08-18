<?php
/**
 * 加急操作
 * @author steven allen <[<email address>]>
 * @date 2016.10.24
 */
class SetHurryAction extends CAction
{
	public function run()
	{
		$staff = $this->controller->staff;
		$fid = Yii::app()->request->getQuery('fid');
		$type = Yii::app()->request->getQuery('type',0);
		if(!$fid || !$type)
			$this->controller->returnError('参数错误');
		elseif($staff->getCanHurryNum() <= 0 || (is_array($fid) && count($fid) > $staff->getCanHurryNum()))
			$this->controller->returnError('加急配额不足，还可以加急'.$staff->getCanHurryNum().'条');
		else
		{
			if(strstr($fid,','))
			{
				$fid = explode(',', $fid);
				$success = $error = 0;
				foreach (array_filter($fid) as $key => $v) {
					$model = $type==1 ? ResoldEsfExt::model()->findByPk($v) : ResoldZfExt::model()->findByPk($v);
					if(time() - $model->hurry >= SM::resoldConfig()->resoldHurryTime->value*3600)
					{
						$success += 1;
						$model->hurry = time();
						$model->save();
					}
					else
					{
						$error += 1;
					}
				}
				$this->controller->frame['msg'] = '加急成功'.$success.'条，失败'.$error.'条';
			}
			else
			{
				$model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
				if(time() - $model->hurry < SM::resoldConfig()->resoldHurryTime->value*3600)
					$this->controller->returnError('房源在加急中，请稍后操作');
				else
				{
					$model->hurry = time();
					$model->save();
					$this->controller->frame['msg'] = '保存成功';
				}
			}
		}
	}
}