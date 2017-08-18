<?php
/**
 *
 * User: jt
 * Date: 2016/10/11 15:25
 */
class ItemAction extends CAction{

    public function run($id){
        $resold_qz = $this->getController()->findResoldById('ResoldQzExt',$id);
        if(!$resold_qz){
            return $this->getController()->returnError('找不到求租信息');
        }
        $resold_qz->hid = json_decode($resold_qz->hid,true);
        $resold_qz->data_conf = json_decode($resold_qz->data_conf,true);
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id',$resold_qz->hid);
        $plot = PlotExt::model()->findAll($criteria);
        $response = array();
        $response = $resold_qz->getAPIAttributes(array('id','category','area','street','hid','size','price','rent_type',
            'title','content','username','phone','towards','decoration'
        ));
        foreach ($plot as $item) {
            $response['plot'][] = $item->title;
        }

        if($resold_qz->data_conf) {
            foreach ($resold_qz->data_conf as $key => $tag) {
                if (!is_array($tag)) {
                    $tagCate = TagExt::getCateByTag($tag);
                    if ($this->controller->getApiCate($tagCate))
                        $response[$this->controller->getApiCate($tagCate)] = $tag;
                } else {
                    foreach ($tag as $value) {
                        $response[$this->controller->getApiCate($key)][] = $value;
                    }
                }
            }
        }
        if(!isset($response[$this->controller->getApiCate('resoldhuxing')]) || empty($response[$this->controller->getApiCate('resoldhuxing')])){
            $response[$this->controller->getApiCate('resoldhuxing')] = '';
        }
        if($response['rent_type']){
            $tagName = TagExt::getNameByTag($response['rent_type']);
            $response['rent_type'] = $tagName == '整租' ? 1 : ($tagName == '合租' ? 2 : 3);
        }
        return $this->controller->frame['data'] = array('data'=>$response);
    }

}