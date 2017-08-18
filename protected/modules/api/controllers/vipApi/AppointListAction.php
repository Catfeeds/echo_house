<?php

/**
 * User: fanqi
 * Date: 2016/9/26
 * Time: 15:22
 */
class AppointListAction extends CAction
{
    public function run()
    {
        $data = [];
    	$fid = Yii::app()->request->getQuery('fid',0);
    	$type = Yii::app()->request->getQuery('type',0);
    	$staff = $this->controller->staff;
    	if(!$fid || !$type)
			$this->controller->returnError('参数错误');
        $appoints = ResoldAppointExt::model()->findAll(['condition'=>'type=:type and fid=:fid and uid=:uid','params'=>[':type'=>$type,':fid'=>$fid,':uid'=>$staff->uid],'order'=>'appoint_time asc']);

        foreach ($appoints as $key => $value) {
            $data[] = ['id'=>$value->id,'fid'=>$value->fid,'date'=>date('Y.m.d',$value->appoint_time),'time'=>date('H:i',$value->appoint_time),'status'=>$value->status];
        }

        $this->getController()->frame['data'] = ['data'=>$data,'num'=>['canAppoint'=>$this->controller->staff->getCanAppointNum(),'appointNum'=>$this->controller->staff->getCanAppointNum()+count($this->controller->staff->appointEsfs)]];
    }
}