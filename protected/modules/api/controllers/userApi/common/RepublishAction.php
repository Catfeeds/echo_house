<?php
/**
 * 重新发布
 * User: jt
 * Date: 2016/10/17 15:06
 */

class RepublishAction extends CAction
{
    public $model ;

    public function run()
    {
        $id = Yii::app()->request->getPost('id');
        $resold = $this->getController()->findResoldById($this->model,$id);
        if(!$resold){
            return $this->getController()->returnError('找不到房源');
        }
        // if($resold->status != 1){
        //     return $this->getController()->ReturnError('审核通过才能发布');
        // }
        if(!$resold->getPersonalSalingNum()){
            return $this->getController()->returnError('个人发布配额已满');
        }
        $sale_status_arr = array_flip(Yii::app()->params['saleStatus']);
        $shelves = $sale_status_arr['下架'];
        // $resold->sale_time = time();
        isset($resold->sale_status) && $resold->sale_status = $shelves;
        $resold->status = 0;
        if(!$resold->save()){
            return $this->getController()->returnError('发布失败');
        }
        return $this->getController()->frame['msg'] = '发布成功';
    }
}