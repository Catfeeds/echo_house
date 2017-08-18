<?php
/**
 * 我要出租
 * User: jt
 * Date: 2016/10/10 14:53
 */
class EditAction extends CAction{

    public function run(){
        $data = Yii::app()->request->getPost('data');
        $images = Yii::app()->request->getPost('images');
        $data['image_count'] = $images ? count($images) : 0 ;
        $data['status'] = $this->controller->status;  //审核中
        $data['source'] = $this->controller->source; //个人
        $t = Yii::app()->db->beginTransaction();
        $category = Yii::app()->params['categoryPinyin'][$data['category']];
        if(isset($data['id']) && !empty($data['id'])){
            $resold_zf = $this->getController()->findResoldById('ResoldZfExt',$data['id']);
            if(!$resold_zf){
                return $this->getController()->returnError('找不到出租信息');
            }
            $resold_zf->setScenario($category);
        }else{
            $resold_zf =  new ResoldZfExt($category);
        }
        if(isset($data['code']))
        {
             $check_phone = $this->getController()->checkPhone($data['phone'],$data['code'],$resold_zf->getIsNewRecord()?0:$resold_zf->phone);
            if($check_phone !== true){
                return $check_phone;
            }
        }
        unset($data['data_conf']);
        $resold_zf->attributes = $data;
        if(isset($data['tags']) && $data['tags'])
        {
            foreach ($data['tags'] as $key => $tag) {
                if($tag)
                {
                    $cate = TagExt::getCateByTag($tag);
                    if(!is_array($resold_zf->$cate))
                        $data_conf[$cate] = $tag;
                    else
                        $data_conf[$cate][] = $tag;
                    $resold_zf->$cate = $data_conf[$cate];
                }
            }
            unset($data['tags']);
        }
        if(isset($data['tag_zfspkjyxm']) && $data['tag_zfspkjyxm'])
        {
            $resold_zf->zfspkjyxm = $data['tag_zfspkjyxm'];
            unset($data['tag_zfspkjyxm']);
        }
        // exit;
            
        //个人发布配额限制
        if(!$resold_zf->getPersonalSalingNum()){
            return $this->getController()->retrunError('个人发布配额已满');
        }
        $imgs = [];
        if($images)
            foreach ($images as $key => $value) {
                $imgs[] = $value['pic'];
            }
        if($imgs && !in_array($resold_zf->image, $imgs))
            $resold_zf->image = $imgs[0];
        elseif(!$imgs)
            $resold_zf->image = '';

        $resold_zf->uid = $this->controller->uid;
        if(isset($data['rent_type']) && $data['rent_type']) {
            $resold_zf->rent_type = $data['rent_type'] == 1 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'整租']])->id : ($data['rent_type'] == 2 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'合租']])->id : TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'不限']])->id);
        }
        try{
            if(!$resold_zf->save())
            {
                $errors = $resold_zf->getErrors();
                throw new CException(current($errors)[0]);
            }
            if(!$resold_zf->getIsNewRecord()){
                ResoldImageExt::model()->deleteAll('fid=:fid and type=:type',array(':fid'=>$resold_zf->id,':type'=>ResoldImageExt::ZF_TYPE));
            }
            if($images){
                foreach ($images as $item){ 
                    $resold_image = new ResoldImageExt();
                    $resold_image->fid = $resold_zf->id ;
                    $resold_image->url = $item['pic'];
                    $resold_image->source = $this->controller->source;
                    $resold_image->type = ResoldImageExt::ZF_TYPE;
                    if(!$resold_image->save())
                    {
                        $errors = $resold_image->getErrors();
                        throw new CException(current($errors)[0]);
                    }
                }
            }
            $t->commit();
        }catch (CException $e){
            $t->rollback();
            return $this->controller->returnError($e->getMessage());
        }
        return $this->getController()->frame['msg'] = '发布成功';
    }

}