<?php
/**
 * 二手房预约刷新脚本
 * 五分钟一次
 * @author steven.allen <[<email address>]>
 * @date 2016.11.22
 */
class ResoldAppointCommand extends CConsoleCommand
{
	public function actionIndex()
	{
		$appoints = ResoldAppointExt::model()->findAll(['condition'=>'appoint_time<=:time and status=0','params'=>[':time'=>time()]]);
		if($appoints) {
			$transaction = Yii::app()->db->beginTransaction();
			$expireTime = SM::resoldConfig()->resoldExpireTime() * 86400;
			try{
				foreach ($appoints as $key => $value) {
					if($value->type==1) {
						Yii::app()->db->createCommand('update resold_esf set refresh_time='.time().',expire_time='.(time()+$expireTime).' where id='.$value->fid)->execute();
					} elseif($value->type==2) {
						Yii::app()->db->createCommand('update resold_zf set refresh_time='.time().',expire_time='.(time()+$expireTime).' where id='.$value->fid)->execute();
					}
					
					// $model = $value->type == 1 ? ResoldEsfExt::model()->findByPk($value->fid) : ResoldZfExt::model()->findByPk($value->fid);
					// $model->refresh_time = time();
					// $model->expire_time = time() + $expireTime;
					// if(!$model->save())
					// 	throw new CException('resoldesf or resoldzf save fail');
					$value->status = 1;
					if(!$value->save())
						throw new CException('resoldappoint save fail');
				}
				$transaction->commit();
				echo "\n操作成功\n";
			}catch (CException $e){
				$transaction->rollback();
				echo $e->getMessage();
			}
		}
	}
}