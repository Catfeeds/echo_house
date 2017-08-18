<?php
/**
 * 发布买房信息
 * User: jt
 * Date: 2016/10/10 9:49
 */
class EditAction extends CAction{

    public function run(){
        $data = Yii::app()->request->getPost('data');
        $category = Yii::app()->params['categoryPinyin'][$data['category']];
        if(isset($data['id']) && !empty($data['id'])){
            $resold_qg = $this->getController()->findResoldById('ResoldQgExt',$data['id']);
            if(!$resold_qg){
                return $this->getController()->returnError('找不到求购信息');
            }
            $resold_qg->setScenario($category);
        }else{
            $resold_qg = new ResoldQgExt();
            $resold_qg->setScenario($category);
        }
        if(isset($data['code']))
        {
            $check_phone = $this->getController()->checkPhone($data['phone'],$data['code'],$resold_qg->getIsNewRecord()?0:$resold_qg->phone);
            if($check_phone !== true){
                return $check_phone;
            }
        }

        $resold_qg->attributes = $data;
        $resold_qg->status = $this->controller->status;
        $tagArr = [];
        if(isset($data['tags']) && !empty($data['tags']))
        {
            foreach ($data['tags'] as $key => $value) {
                $tagArr[] = $value;
            }
        }
        if(isset($data['hid']) && $data['hid'])
            $resold_qg->hid = json_encode($data['hid']);
        else
            $resold_qg->hid = '';
        $esfspkjyxm = isset($data['tag_esfspkjyxm']) ? $data['tag_esfspkjyxm'] : '';
        if(is_array($esfspkjyxm)){
            $tagArr = array_merge($tagArr,$esfspkjyxm);
        }
        $resold_qg->data_conf = json_encode(array('tags'=>$tagArr));
        $resold_qg->uid = $this->controller->uid;
        if($resold_qg->save())
            return $this->controller->frame['msg'] = '发布成功';
        $errors = $resold_qg->getErrors();
        return $this->getController()->returnError(current($errors)[0]);
    }

}