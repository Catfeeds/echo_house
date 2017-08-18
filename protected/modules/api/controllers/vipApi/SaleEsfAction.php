<?php

/**
 * User: fanqi
 * Date: 2016/9/23
 * Time: 17:00
 * 我要卖房接口
 */
class SaleEsfAction extends CAction
{
    public function run(){
        $post_data = Yii::app()->request->getPost('data');
        $post_image = Yii::app()->request->getPost('images');
        if($this->controller->staff->getCanSaleNum() < 1)
            return $this->controller->returnError('套餐配额不足');
        if($post_image){
            $post_data['image_count'] = count($post_image);
        }
        isset($post_data['hid']) && $plot = PlotExt::model()->findByPk($post_data['hid']);
        if(!isset($plot) || !$plot){
            return $this->controller->returnError('楼盘为空或不存在');
        }
        // 空字段控制
        foreach ($post_data as $key => $value) {
            $value=='NaN' && $post_data[$key] = 0;
        }
        $post_data['content'] = Tools::filterEmoji($post_data['content']);
        $post_data['plot_name'] = $plot->title ;
        $check_arr = array_flip(Yii::app()->params['checkStatus']);
        $source_arr = array_flip(Yii::app()->params['source']);
        $source_person = $source_arr['中介'];
        $post_data['source'] = $source_person; 
        isset($post_data['tags']) && $post_data['tags'] &&
        $post_data['data_conf'] = CJSON::encode(['tags'=>$post_data['tags']]);
        $t = Yii::app()->db->beginTransaction();
        $resold_esf = (isset($post_data['id']) && !empty($post_data['id'])) ? ResoldEsfExt::model()->findByPk($post_data['id']) : new ResoldEsfExt();
        $resold_esf->scenario = Yii::app()->params['categoryPinyin'][$post_data['category']];
        if(!$resold_esf){
            return $this->getController()->returnError('找不到二手房');
        }
        // 黑名单限制
        if(ResoldBlackExt::model()->count(['condition'=>'phone=:phone','params'=>[':phone'=>$resold_esf->phone]])){
            return $this->getController()->returnError('该号码为黑名单用户');
        }
        unset($post_data['tags']);
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
        try{
            $resold_esf->uid = $this->controller->staff->uid;
            if(!$resold_esf->status)
                $resold_esf->status = 1;
            if(!$resold_esf->sale_status)
                $resold_esf->sale_status = 1;
            if($resold_esf->sale_status == 1) {
                // 到期时间延续
                $resold_esf->expire_time = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
            }
            $resold_esf->sid = $this->controller->staff->sid;
            if($resold_esf->getIsNewRecord())
                $resold_esf->sale_time = $resold_esf->refresh_time = time();
            // else
            $resold_esf->phone = $this->controller->staff->phone;
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
                    $resold_image->source = $source_person;
                    $resold_image->type = ResoldImageExt::ESF_TYPE;
                    if(!$resold_image->save())
                        throw new CException($resold_image->errors[0].'保存失败');
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