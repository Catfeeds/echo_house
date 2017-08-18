<?php
/**
 * 二手房详情
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class EsfInfoAction extends CAction
{
	public function run($id){
        $images = [];
        $resold_esf = ResoldEsfExt::model()->undeleted()->findByPk($id);
        if(!$resold_esf){
            return $this->getController()->returnError('找不到二手房');
        }
        $response =  $resold_esf->getAPIAttributes(array(
            'id','category','plot_name','hid','bedroom','livingroom','bathroom','cookroom','size','area','street','image','price',
            'age','floor','total_floor','towards','title','content','username','phone','wuye_fee','sale_status','decoration'
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
        unset($response['images']);
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
        return $this->getController()->frame['data'] = ['data'=>$response,'images'=>$images];
    }
}