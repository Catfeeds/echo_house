<?php
/**
 * 刷新租房时间
 * User: jt
 * Date: 2016/10/11 10:14
 */

class RefreshAction extends CAction{

    public $model ;

    public function run(){
        $id = Yii::app()->request->getPost('id');
        $resold = $this->getController()->findResoldById($this->model,$id);
        if(!$resold){
            return $this->getController()->returnError('找不到房源信息');
        }
        $refresh_time  = $resold->refresh_time;
        $space = SM::resoldConfig()->resoldRefreshInterval->value * 60;
        if($refresh_time && ((time()- $refresh_time)  < $space)){
            return $this->getController()->returnError('休息'.ceil(($space - (time()-$refresh_time))/60).'分钟再来刷新吧');
        }
        $resold->refresh_time = time();
        if($resold->save()){
            return $this->getController()->frame['msg'] = '更新成功';
        }else{
            return $this->getController()->returnError('更新失败');
        }
    }

}