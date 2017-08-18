<?php
/**
 * 房源上下架操作
 * @author steven allen <[<email address>]>
 * 2016.10.27 房源到期时间取消
 * @date 2016.10.24
 */
class ChangeSaleStatusAction extends CAction
{
	public function run()
	{
		$staff = $this->controller->staff;
		$fid = Yii::app()->request->getQuery('fid','');
		$type = Yii::app()->request->getQuery('type',0);
		$sale = Yii::app()->request->getQuery('sale',0);
		$fid = array_filter(explode(',', $fid));

		if(!$fid || !$type || !$sale)
			$this->controller->returnError('参数错误');
		elseif($sale==1 && ($staff->getCanSaleNum() <= 0 || (is_array($fid) && count($fid) > $staff->getCanSaleNum())))
			$sale==1 && $this->controller->returnError('可上架配额不足，您可能有上架房源已到期，您还可以上架'.$staff->getCanSaleNum().'条');
		else
		{
			if(is_array($fid))
			{
				foreach (array_filter($fid) as $key => $v) {
					$model = $type==1 ? ResoldEsfExt::model()->findByPk($v) : ResoldZfExt::model()->findByPk($v);
					if($model->status == 1){
	                    $model->sale_status = $sale;
	                    $sale==2 and $model->hurry = 0;
                        // $model->sale_time = $model->refresh_time = time();
                        $sale ==  1 && $model->expire_time = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
                        $model->save();$model->save();
                        // sleep(0.2);
	                }
				}
			}
			else
			{
				$model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
				if($model->status == 1){
                    $model->sale_status = $sale;
                    $sale==2 and $model->hurry = 0;
                    // $model->sale_time = $model->refresh_time = time();
                    $sale ==  1 && $model->expire_time = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
                    $model->save();$model->save();
                }
			}
			// 下架干掉预约
			if($sale == 2)
			{
				ResoldAppointExt::model()->deleteAllByAttributes(['fid'=>$fid,'uid'=>$staff->uid,'type'=>$type,'status'=>0]);

			}
		}
	}
}
