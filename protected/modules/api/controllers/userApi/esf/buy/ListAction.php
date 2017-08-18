<?php
/**
 *
 * User: jt
 * Date: 2016/10/10 9:56
 */

class ListAction extends CAction{

    public function run($limit=20){
        $criteria = new CDbCriteria(array(
            'condition'=>'t.uid=:uid',
            'params'=>array(':uid'=>$this->controller->uid),
            'order'=>'t.refresh_time asc , t.created desc',
            'with'=>array('areaInfo')
        ));
        $dataProvider = ResoldQgExt::model()->undeleted()->getList($criteria,$limit);
        $response = array();
        if($dataProvider->data){
            foreach ($dataProvider->data as $resold_qg){
                 $resold_qg->created = date('Y.m.d',$resold_qg->created);
                 $resold_qg->area = $resold_qg->areaInfo ? $resold_qg->areaInfo->name : '';
                 $resold_qg->street = $resold_qg->streetInfo ? $resold_qg->streetInfo->name : '';
                 $attributes = $resold_qg->attributes;
                 $attributes['is_refresh'] = $resold_qg->isRefresh;
                 $response[] = $attributes;
            }
        }
        return $this->controller->frame['data'] = ['data'=>$response,'page_count'=>$dataProvider->pagination->getPageCount()];
    }

}