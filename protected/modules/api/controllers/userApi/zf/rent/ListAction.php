<?php
/**
 * 我的出租房源
 * User: jt
 * Date: 2016/10/10 14:54
 */

class ListAction extends CAction{

    public function run($limit=20){
        $criteria = new CDbCriteria(array(
            'condition'=>'t.uid=:uid',
            'params'=>array(':uid'=>$this->controller->uid),
            'with'=>array('areaInfo','streetInfo'),
            'order'=>'t.refresh_time asc , t.created desc',
        ));
        $dataProvider = ResoldZfExt::model()->normal()->getList($criteria,$limit);
        $response = array();
        if($dataProvider->data){
            foreach ($dataProvider->data as $resold_qg){
                $resold_qg->created = date('Y.m.d',$resold_qg->created);
                $info = $resold_qg->getAPIAttributes(array('expire_time','refresh_time','id','title','created','size','price','bedroom','livingroom','bathroom','status','sale_status','image','deleted','category'), array('areaInfo'=>array('name'),'streetInfo'=>array('name')));
                $info['is_refresh'] = $resold_qg->isRefresh;
                $info['is_expire'] = (time()-$info['expire_time'])>0?1:0;
                // 审核中不显示已过期
                $info['status']%2==0 && $info['is_expire'] = 0;
                $info['image'] = ImageTools::fixImage($info['image'],88,66);
                $response[] = $info;
            }
        }
        return $this->controller->frame['data'] = ['data'=>$response,'page_count'=>$dataProvider->pagination->getPageCount()];
    }

}