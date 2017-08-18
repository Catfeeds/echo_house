<?php
/**
 * 二手房发布
 * User: jt
 * Date: 2016/10/8 13:51
 */

class PublishAction extends CAction{

    public function run(){
        $post_data = Yii::app()->request->getPost('data');
        $post_image = Yii::app()->request->getPost('images');
        if($post_image){
            $post_data['image_count'] = count($post_image);
        }
        isset($post_data['hid']) && $plot = PlotExt::model()->findByPk($post_data['hid']);
        if(!isset($plot) || !$plot){
            return $this->controller->returnError('楼盘为空或不存在');
        }
        $post_data['plot_name'] = $plot->title ;
        $post_data['status'] = $this->controller->status;  //审核中
        $post_data['source'] = $this->controller->source; //个人
        isset($post_data['tags']) && $post_data['tags'] &&
        $post_data['data_conf'] = CJSON::encode(['tags'=>$post_data['tags']]);

        $category = Yii::app()->params['categoryPinyin'][$post_data['category']];
        if($category === false)
            return $this->controller->returnError('房屋类型异常');
        $t = Yii::app()->db->beginTransaction();
        if(isset($post_data['id']) && !empty($post_data['id'])){
            $resold_esf =  $this->getController()->findResoldById('ResoldEsfExt',$post_data['id']);
            if(!$resold_esf){
                return $this->getController()->returnError('找不到二手房');
            }
            $resold_esf->setScenario($category);
        }else{
            $resold_esf = new ResoldEsfExt($category);
        }
        // 验证码逻辑
        if(isset($post_data['code']))
        {
            $check_phone = $this->getController()->checkPhone($post_data['phone'], $post_data['code'],$resold_esf->getIsNewRecord()?0:$resold_esf->phone);
            if ($check_phone !== true) {
                return $check_phone;
            }
        }
            
        if(!$resold_esf->getPersonalSalingNum()){
            return $this->getController()->returnError('个人发布配额已满');
        }
        $resold_esf->attributes = $post_data;
        if(isset($post_data['tag_esfspkjyxm']) && $post_data['tag_esfspkjyxm'])
        {
            $resold_esf->esfspkjyxm = $post_data['tag_esfspkjyxm'];
            unset($post_data['tag_esfspkjyxm']);
        }
        $imgs = [];
        if($post_image)
            foreach ($post_image as $key => $value) {
                $imgs[] = $value['pic'];
            }
        if($imgs && !in_array($resold_esf->image, $imgs))
            $resold_esf->image = $imgs[0];
        elseif(!$imgs)
            $resold_esf->image = '';
        $resold_esf->uid = $this->controller->uid;
        try{
            if(!$resold_esf->save()){
                $errors = $resold_esf->getErrors();
                throw new CException(current($errors)[0]);
            }
            if(!$resold_esf->getIsNewRecord()){
                ResoldImageExt::model()->deleteAll('fid=:fid and type=:type',array(':fid'=>$resold_esf->id,':type'=>ResoldImageExt::ESF_TYPE));
            }
            if($post_image){
                foreach ($post_image as $item){
                    $resold_image = new ResoldImageExt();
                    $resold_image->fid = $resold_esf->id ;
                    $resold_image->url = $item['pic'];
                    $resold_image->source = $this->controller->source;
                    $resold_image->type = ResoldImageExt::ESF_TYPE;
                    if(!$resold_image->save()){
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