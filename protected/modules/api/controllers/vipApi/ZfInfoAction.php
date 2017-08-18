<?php
/**
 * 租房详情
 * @author steven allen <[<email address>]>
 * @date 2016.10.24
 */
class ZfInfoAction extends CAction
{
	public function run($id)
	{
		$images = [];
		$resold_zf = ResoldZfExt::model()->undeleted()->findByPk($id);
		if(!$resold_zf){
		    return $this->getController()->returnError('找不到出租信息');
		}
		$resold_zf->data_conf = json_decode($resold_zf->data_conf,true);
		$response =  $resold_zf->getAPIAttributes(array('hid','category','rent_type','title','bedroom','livingroom',
		   'bathroom','size','floor','total_floor','towards','price','price','title','content','username','phone','decoration',
		   'data_conf','image','id','sale_status','wuye_fee'),array(
		   'images'=>array('url'),
		   'plot'=>array('title')
		));
		if($resold_zf->data_conf)
		    foreach ($resold_zf->data_conf as $key => $tag) {
		        if(!is_array($tag))
		        {
		            $tagCate = $key;
		            if($transtag = $this->controller->getApiCate($tagCate))
	                    if($transtag == 'tag_zfspkjyxm')
	                        $response[$transtag][] = $tag;
	                    else
	                        $response[$transtag] = $tag;
	                else
	                    $response['tagext'][] = $tag;
		        }
		        else
		        {
		        	foreach ($tag as $k => $value) {
		        		if($transtag = $this->controller->getApiCate($key))
		                    if($transtag == 'tag_zfspkjyxm')
		                        $response[$transtag][] = $value;
		                    else
		                        $response[$transtag] = $value;
		                else
		                    $response['tagext'][] = $value;
			        	}
		        }
		    }
	    if($response['images'])
            foreach ($response['images'] as $key => $value) {
                $images[] = ['url'=>ImageTools::fixImage($value['url']),'pic'=>$value['url']];
            }
         unset($response['images']);
         if($response['rent_type']){
         	$tagName = TagExt::getNameByTag($response['rent_type']);
            $response['rent_type'] = $tagName == '整租' ? 1 : ($tagName == '合租' ? 2 : 3);
        }
		return $this->controller->frame['data'] = ['data'=>$response,'images'=>$images];
	}
}