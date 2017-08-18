<?php
/**
 * 获取单个租房信息
 * User: jt
 * Date: 2016/10/11 10:14
 */

class ItemAction extends CAction{

    public function run($id){
       $images = [];
       $resold_zf = $this->getController()->findResoldById('ResoldZfExt',$id);
       if(!$resold_zf){
            return $this->getController()->returnError('找不到出租信息');
       }
       $resold_zf->data_conf = json_decode($resold_zf->data_conf,true);
       $response =  $resold_zf->getAPIAttributes(array('id','hid','category','rent_type','title','bedroom','livingroom',
           'bathroom','size','floor','total_floor','towards','price','price','title','content','username','phone','image','wuye_fee',
           'data_conf','decoration'),array(
           'images'=>array('url'),
           'plot'=>array('title')
       ));
       if($resold_zf->data_conf)
            foreach ($resold_zf->data_conf as $key => $tag) {
                if(!is_array($tag))
                {
                    if($transtag = $this->controller->getApiCate($key))
                        $response[$transtag] = $tag;
                    else
                        $response['tagext'][] = $tag;
                }
                else
                {
                    foreach ($tag as $value) {
                        if($transtag = $this->controller->getApiCate($key))
                            $response[$transtag][] = $value;
                        else
                            $response['tagext'][] = $value;
                    }
                }
            }
        if($response['images']) {
            foreach ($response['images'] as $key => $value) {
                $images[] = ['url' => ImageTools::fixImage($value['url']), 'pic' => $value['url']];
            }
        }
        if($response['rent_type']){
            $tagName = TagExt::getNameByTag($response['rent_type']);
            $response['rent_type'] = $tagName == '整租' ? 1 : ($tagName == '合租' ? 2 : 3);
        }
        unset($response['images']);
        return $this->controller->frame['data'] = ['data'=>$response,'images'=>$images];
    }

}