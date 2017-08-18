<?php
/*
* 地图找房 街道
* @author  liyu
* @created 2016年10月19日17:00:53
*/
class StreetAction extends CAction{
    public function run(){
        $type = Yii::app()->request->getQuery('type',1);//二手房
        $data = array();
        $kw = Yii::app()->request->getQuery('kw','');
        $area = Yii::app()->request->getQuery('area',0);
        if($type==1){
            $xs = Yii::app()->search->house_esf;
        }else if($type==2){
            $xs = Yii::app()->search->house_zf;
        }else{
            $this->controller->frame['status'] = 'error';
            $this->controller->frame['msg'] = '不存在';
        }

        $xs->setQuery($kw);
        $xs->addRange('status',1,1);
        $xs->addRange('deleted',0,0);
        $xs->addRange('expire_time',time(),null);
        $area and $xs->addRange('area',$area,$area);
        $xs->setFacets('street', true)->search();
        $nums = $xs->getFacets('street');
        $areas = AreaExt::model()->normal()->findAll(array('index'=>'id'));
        $data['total'] = array_sum($nums);
        $tmp = array();
        $data['lists'] = array();
        foreach($nums as $k=>$v)
        {
            if(!isset($areas[$k])) continue;
            $tmp = array('id'=>$areas[$k]->id,'name'=>$areas[$k]->name, 'lng'=>$areas[$k]->map_lng, 'lat'=>$areas[$k]->map_lat, 'num'=>$v);
            $data['lists'][] = $tmp;
        }
        $data['parent'] = $area;
        $this->controller->frame['data'] = $data;
    }
}
