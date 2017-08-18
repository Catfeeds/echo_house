<?php
/**
 * 恢复
 * User: jt
 * Date: 2016/10/17 15:17
 */

class RestoreAction extends CAction
{
    public $model;

    public function run()
    {
        $id = Yii::app()->request->getPost('id');
        $model = $this->model;
        $resold = $model::model()->findByPk($id);
        if(!$resold){
            return $this->getController()->returnError('找不到房源信息');
        }
        $sale_status_arr = array_flip(Yii::app()->params['saleStatus']);
        $off = $sale_status_arr['下架'];
        isset($resold->sale_status) && $resold->sale_status = $off;
        $resold->deleted = 0;
        $resold->status = 0;
        $resold->save();
        return $this->getController()->frame['msg'] = '恢复成功';

    }

}