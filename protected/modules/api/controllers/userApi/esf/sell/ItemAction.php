<?php
/**
 * 获取用户的二手房数据
 * User: jt
 * Date: 2016/10/9 16:29
 */

class ItemAction extends CAction{

    public function run($id){
        $images = [];
        $resold_esf = $this->getController()->findResoldById('ResoldEsfExt',$id);
        if(!$resold_esf){
            return $this->getController()->returnError('找不到二手房');
        }
        $response =  $resold_esf->getAPIAttributes(array(
            'id','category','plot_name','hid','bedroom','livingroom','bathroom','cookroom','size','area','street','image','price',
            'age','floor','total_floor','towards','title','content','username','phone','wuye_fee','decoration'
        ),array(
            'images'=>array('url')
        ));
        $resold_esf->data_conf = json_decode($resold_esf->data_conf,true);
        // $type = TagExt::model()->getTagByCate('esfzfzztype')->normal()->findAll();
        // foreach ($type as $item){
        //     if(in_array($item->id,$resold_esf->data_conf['tags'])){
        //         $response['house_type'] = $item->id;
        //     }
        // }

        if($response['images'])
            foreach ($response['images'] as $key => $value) {
                $images[] = ['url'=>ImageTools::fixImage($value['url']),'pic'=>$value['url']];
            }
        
            
        if($resold_esf->data_conf['tags'])
            foreach ($resold_esf->data_conf['tags'] as $key => $tag) {
                $tagCate = TagExt::getCateByTag($tag);
                if($transtag = $this->controller->getApiCate($tagCate))
                    if($transtag == 'tag_esfspkjyxm')
                        $response[$transtag][] = $tag;
                    else
                        $response[$transtag] = $tag;
                else
                    $response['tagext'][] = $tag;
            }
        unset($response['images']);
        return $this->getController()->frame['data'] = ['data'=>$response,'images'=>$images];
    }

}