<?php

/**
 * User: fanqi
 * Date: 2016/9/26
 * Time: 17:10
 */
class SaleZfAction extends CAction
{
    public function run(){
        $data = Yii::app()->request->getPost('data');
        if(empty($data))
            return $this->getController()->returnError('参数错误');
        foreach ($data as $key => $value) {
            $value=='NaN' && $data[$key] = 0;
        }
        if($this->controller->staff->getCanSaleNum() < 1)
            return $this->controller->returnError('套餐配额不足');
        $images = Yii::app()->request->getPost('images');
        $data['image_count'] = $images ? count($images) : 0 ;
        $data['content'] = Tools::filterEmoji($data['content']);
        $source_arr = array_flip(Yii::app()->params['source']);
        $data['source'] = $source_arr['中介'];
        $t = Yii::app()->db->beginTransaction();
        $resold_zf = (isset($data['id']) && !empty($data['id'])) ? $this->getController()->findResoldById('ResoldZfExt',$data['id']) : new ResoldZfExt(Yii::app()->params['categoryPinyin'][$data['category']]);
        if(!$resold_zf){
           return $this->getController()->returnError('找不到出租信息');
        }
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

        // 黑名单限制
        if(ResoldBlackExt::model()->count(['condition'=>'phone=:phone','params'=>[':phone'=>$resold_zf->phone]])){
            return $this->getController()->returnError('该号码为黑名单用户');
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

        try{
            $resold_zf->uid = $this->controller->staff->uid;
            if(!$resold_zf->status)
                $resold_zf->status = 1;
            if(!$resold_zf->sale_status)
                $resold_zf->sale_status = 1;
            if($resold_zf->sale_status == 1) {
                // 到期时间延续
                $resold_zf->expire_time = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
            }
            if(isset($data['rent_type']) && $data['rent_type']) {
                $resold_zf->rent_type = $data['rent_type'] == 1 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'整租']])->id : ($data['rent_type'] == 2 ? TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'合租']])->id : TagExt::model()->find(['condition'=>'name=:name and cate=:cate','params'=>[':cate'=>'zfmode',':name'=>'不限']])->id);
            }
            $resold_zf->sid = $this->controller->staff->sid;
            if($resold_zf->getIsNewRecord())
                $resold_zf->sale_time = $resold_zf->refresh_time = time();
            $resold_zf->phone = $this->controller->staff->phone;
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
                    $resold_image->source = $source_arr['个人'];
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