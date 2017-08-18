<?php
/**
 *
 * User: jt
 * Date: 2016/10/11 15:26
 */
class ListAction extends CAction{

    public function run($limit=20){
        $criteria = new CDbCriteria(array(
            'condition'=>'t.uid=:uid',
            'params'=>array(':uid'=>$this->controller->uid),
            'with'=>array('areaInfo'),
            'order'=>'t.refresh_time asc , t.created desc',
        ));
        $dataProvider = ResoldQzExt::model()->undeleted()->getList($criteria,$limit);
        $response = array();
        if($dataProvider->data){
            foreach ($dataProvider->data as $resold_qg){
                $resold_qg->created = date('Y.m.d',$resold_qg->created);
                $attributes = $resold_qg->getAPIAttributes(array('id','status','size','refresh_time','created','title','price','livingroom','cookroom','bedroom','bathroom','deleted','category'), array('areaInfo'=>array('name'),'streetInfo'=>array('name')));
                $attributes['is_refresh'] = $resold_qg->isRefresh;
                $response[] = $attributes;
            }
        }
        return $this->controller->frame['data'] = ['data'=>$response,'page_count'=>$dataProvider->pagination->getPageCount()];
    }

}