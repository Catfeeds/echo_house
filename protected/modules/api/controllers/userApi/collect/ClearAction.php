<?php
/**
 *
 * User: jt
 * Date: 2016/10/12 10:43
 */

class ClearAction extends CAction{

    public function run(){
        $house_type = Yii::app()->request->getPost('house_type');
        if(!ResoldUserCollectionExt::$house_type_relations[$house_type]){
            return $this->getController()->returnError('参数异常');
        }
        $res = ResoldUserCollectionExt::model()->deleteAll('uid=:uid and house_type=:house_type',array(':uid'=>$this->getController()->uid,':house_type'=>$house_type));
        if($res){
            return $this->getController()->frame['msg'] = '清空成功';
        }else{
            return $this->getController()->returnError('清空失败');
        }

    }

}