<?php
/**
 * 获取单个买房信息
 * User: jt
 * Date: 2016/10/10 10:02
 */

class ItemAction extends CAction{

    public function run($id){
        $resold_qg = $this->getController()->findResoldById('ResoldQgExt',$id);
        if(!$resold_qg){
            return $this->getController()->returnError('找不到求购信息');
        }
        $resold_qg->data_conf = json_decode($resold_qg->data_conf,true);
        $resold_qg->hid = json_decode($resold_qg->hid,true);
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id',$resold_qg->hid);
        $plot = PlotExt::model()->findAll($criteria);
        $response = $resold_qg->getAPIAttributes(array('id','category','area','street','hid',
            'bedroom','livingroom','bathroom','size','price','title','content','username','phone','towards','decoration'
            ));
        foreach ($plot as $item) {
             $response['plot'][] = $item->title;
        }
        if(isset($resold_qg->data_conf['tags']) && $resold_qg->data_conf['tags'])
            foreach ($resold_qg->data_conf['tags'] as $key => $tag) {
                $tagCate = TagExt::getCateByTag($tag);
                if($transtag = $this->controller->getApiCate($tagCate))
                    if($transtag == 'tag_esfspkjyxm')
                        $response[$transtag][] = $tag;
                    else
                        $response[$transtag] = $tag;
                else
                    $response['tagext'][] = $tag;
            }

        return $this->controller->frame['data'] = array('data'=>$response);

    }

}