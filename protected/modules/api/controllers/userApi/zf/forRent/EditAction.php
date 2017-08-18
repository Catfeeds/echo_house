<?php
/**
 *
 * User: jt
 * Date: 2016/10/11 11:32
 */
class EditAction extends CAction{

    public function run(){
        $data = Yii::app()->request->getPost('data');
        $category = Yii::app()->params['categoryPinyin'][$data['category']];
        if(isset($data['id']) && !empty($data['id'])){
            $resold_qz = $this->getController()->findResoldById('ResoldQzExt',$data['id']);
            if(!$resold_qz){
                return $this->getController()->returnError('找不到求租信息');
            }
            $resold_qz->setScenario($category);
        }else{
            $resold_qz = new ResoldQzExt();
            $resold_qz->setScenario($category);
        }
        if(isset($data['code']))
        {
             $check_phone = $this->getController()->checkPhone($data['phone'],$data['code'],$resold_qz->getIsNewRecord()?0:$resold_qz->phone);
            if($check_phone !== true){
                return $check_phone;
            }
        }
        if(isset($data['tags']) && !empty($data['tags'])) {
            $tags = array_filter($data['tags']);
            $data_conf = [];
            foreach ($tags as $key => $tag) {
                if($tag)
                {
                    $cate = TagExt::getCateByTag($tag);
                    if(!is_array($resold_qz->$cate))
                        $data_conf[$cate] = $tag;
                    else
                        $data_conf[$cate][] = $tag;
                    $resold_qz->$cate = $data_conf[$cate];
                }
            }
            unset($data['tags']);
        }
        if(isset($data['tag_zfspkjyxm']) && $data['tag_zfspkjyxm'])
        {
            $resold_qz->zfspkjyxm = $data['tag_zfspkjyxm'];
            unset($data['tag_zfspkjyxm']);
        }
        $resold_qz->attributes = $data;
        if(isset($data['hid']) && $data['hid'])
            $resold_qz->hid = json_encode($data['hid']);
        else
            $resold_qz->hid = '';
        $resold_qz->status = $this->controller->status;
        if(isset($data['rent_type']) && $data['rent_type']){
            $resold_qz->rent_type = $data['rent_type'] == 1 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'整租']])->id : ($data['rent_type'] == 2 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'合租']])->id : TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'不限']])->id);
        }
        $resold_qz->uid = $this->controller->uid;
        $resold_qz->data_conf = CJSON::encode(array_filter(ResoldQzExt::getTagName()));
        if($resold_qz->save()){
            return $this->getController()->frame['msg'] = '发布成功';
        }
        else
        {
            $errors = $resold_qz->getErrors();
            return $this->getController()->returnError(current($errors)[0]);
        }
        
    }
}