<?php
/**
 *
 * User: jt
 * Date: 2016/10/12 10:36
 */

class DeleteAction extends CAction{

    public function run(){
        $id = Yii::app()->request->getPost('id');
        $model = ResoldUserCollectionExt::model()->findByPk($id);
        if(!$model)
            return $this->getController()->returnError('未找到该收藏');
        $model->delete();
        return $this->getController()->frame['msg'] = '删除成功';
    }

}