<?php
/**
 *
 * User: jt
 * Date: 2016/10/11 11:46
 */
class DeleteAction extends CAction{

    public $model ;

    public function run(){
        $id = Yii::app()->request->getPost('id');
        $model = $this->model;
        $resold = $model::model()->findByPk($id);
        if(!$resold){
            return $this->getController()->returnError('找不到房源');
        }
        $sale_status_arr = array_flip(Yii::app()->params['saleStatus']);
        if(isset($resold->sale_status))
        {
            $recover = $sale_status_arr['回收']; 
            $resold->sale_status = $recover ;
            $resold->status = 0;
        }   
        else
        {
            $resold->status = 0;
            $resold->deleted = 1;
        }
        if($resold->save()){
            return $this->getController()->frame['msg'] = '删除成功';
        }else{
            $errors = $resold->getErrors();
            return $this->getController()->returnError(current($errors)[0]);
        }
    }

}