<?php
/**
 * 预约刷新操作
 */
class SetAppointAction extends CAction
{
    public function run()
    {
    	$staff = $this->controller->staff;
    	$fid = Yii::app()->request->getQuery('fid');
    	$type = Yii::app()->request->getQuery('type',0);
    	$day = Yii::app()->request->getQuery('day',0);
    	$hour = Yii::app()->request->getQuery('hour',0);
    	$minute = Yii::app()->request->getQuery('minute',0);
        $refreshInterval = SM::resoldConfig()->resoldRefreshInterval() * 60;
        $fid = explode(',', $fid);
    	if($day > $staff->getCanAppointNum() || (is_array($fid) && count($fid) > $staff->getCanAppointNum()))
    		$this->controller->returnError('套餐配额不足，还可以预约'.$staff->getCanAppointNum().'条');
    	else
    	{
    		if(!$type || !$fid || !$day)
    			$this->controller->returnError('参数错误');
    		else
    		{
                
                    $success = $error = 0;
                    foreach (array_filter($fid) as $key => $v) {
                        $refresh_time = Yii::app()->db->createCommand('select refresh_time from resold_esf where id='.$v )->queryScalar();
                        $times = [];
                        for($i = 1; $i <= $day; $i++)
                        {
                            $times[] = TimeTools::getDayBeginTime() + ($i - 1)*86400 + $hour*3600 + $minute*60;
                        }
                        if(count($times)==1 && $times[0] < time())
                            return $this->controller->frame['msg'] = '请勿预约之前的时间';
                        if(count($times)==1 && count($fid)==1 && $times[0] - $refresh_time < $refreshInterval)
                            return $this->controller->frame['msg'] = '该时间点房源在刷新状态中';
                        foreach ($times as $key => $t) {
                            if($t <= time())
                            {
                                $error += 1;
                                unset($times[$key]);
                                continue ;
                            }
                            $criteria = new CDbCriteria;
                            $criteria->addCondition('fid=:fid and type=:type and uid=:uid and appoint_time<=:max and appoint_time>=:min and status=0');
                            $criteria->params = [':fid'=>$v,':uid'=>$staff->uid,':type'=>$type,':max'=>$t+$refreshInterval,':min'=>$t-$refreshInterval];
                            if(ResoldAppointExt::model()->count($criteria))
                            {
                                $error += 1;
                                unset($times[$key]);
                                continue ;
                            }
                            if($t - $refresh_time < $refreshInterval) {
                                $error += 1;
                                unset($times[$key]);
                                continue ;
                            }
                        }
                        if($times)
                        {
                            $model = $type==1 ? ResoldEsfExt::model()->findByPk($v) : ResoldZfExt::model()->findByPk($v);
                            $model->appoint_time = time();
                            if($model->save())
                            {
                                foreach ($times as $key => $time) {
                                    $staffAppoint = new ResoldAppointExt;
                                    $staffAppoint->appoint_time = $time;
                                    $staffAppoint->fid = $v;
                                    $staffAppoint->uid = $staff->uid;
                                    $staffAppoint->type = $type;
                                    $staffAppoint->save();
                                    $success += 1;
                                }
                            }
                        }
                        $this->controller->frame['msg'] = '预约成功'.$success.'条，失败'.$error.'条';
                    }
                // }
                // else
                // {
                //     $times = [];
                //     for($i = 1; $i <= $day; $i++)
                //     {
                //         $times[] = TimeTools::getDayBeginTime() + ($i - 1)*86400 + $hour*3600 + $minute*60;
                //     }
                //     foreach ($times as $key => $t) {
                //         if($t <= time())

                //         $criteria = new CDbCriteria;
                //         $criteria->addCondition('fid=:fid and type=:type and uid=:uid and appoint_time<:max and appoint_time>:min and status=0');
                //         $criteria->params = [':fid'=>$fid,':uid'=>$staff->uid,':type'=>$type,':max'=>$t+$refreshInterval,':min'=>$t-$refreshInterval];
                //         if(ResoldAppointExt::model()->count($criteria))
                //             return $this->controller->returnError('房源预约间隔太短，应大于'.SM::resoldConfig()->resoldRefreshInterval().'分钟');
                //     }
                //     $model = $type==1 ? ResoldEsfExt::model()->findByPk($fid) : ResoldZfExt::model()->findByPk($fid);
                //     $model->appoint_time = time();
                //     if($model->save())
                //         for($i = 1; $i <= $day; $i++)
                //         {
                //             $staffAppoint = new ResoldAppointExt;
                //             $staffAppoint->appoint_time = TimeTools::getDayBeginTime() + ($i - 1)*86400 + $hour*3600 + $minute*60;
                //             $staffAppoint->fid = $fid;
                //             $staffAppoint->uid = $staff->uid;
                //             $staffAppoint->type = $type;
                //             $staffAppoint->save();
                //         }
                //     else
                //         $this->controller->returnError('房源保存出错');
                // }
    			
    		}
    	}
    }
}