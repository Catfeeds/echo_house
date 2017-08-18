<?php
/**
 * 管理出售房源
 * User: jt
 * Date: 2016/10/9 10:08
 */

class ListAction extends CAction{

    public function run($limit=20){
        $criteria = new CDbCriteria(array(
            'order'=>'t.refresh_time asc , t.created desc',
            'condition'=>'t.uid=:uid',
            'with'=>array('areaInfo'),
            'params'=>array(':uid'=>$this->controller->uid),
        ));
        $dataProvider = ResoldEsfExt::model()->normal()->getList($criteria,$limit);
        $response = array();
        if($dataProvider->totalItemCount > 0 ){
            foreach ($dataProvider->data as $resold_esf){
                $resold_esf->created = date('Y.m.d',$resold_esf->created);
                $info = $resold_esf->getAPIAttributes(array('id','bathroom','bedroom','livingroom','price','image','sale_status','created','size','status','title','deleted','category','refresh_time','expire_time'),array(
                    'areaInfo'=>array('name'),
                    'streetInfo'=>array('name')
                ));
                $info['image'] = ImageTools::fixImage($info['image'],88,66);
                $info['is_refresh'] = $resold_esf->isRefresh;
                $info['is_expire'] = (time()-$info['expire_time'])>0?1:0;
                // 审核中不显示已过期
                $info['status']%2==0 && $info['is_expire'] = 0;
                $response[] = $info;
                // $response['image'] and $response['image'] = ImageTools::fixImage($response['image']);
            }
        }
        return $this->controller->frame['data'] = ['data'=>$response,'page_count'=>$dataProvider->pagination->getPageCount()];
    }

}