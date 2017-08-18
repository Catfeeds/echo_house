<?php
/*
* 按地区找房
* @author liyu
* @created 2016年10月19日14:53:36
*/
class AreaAction extends CAction{
    public function run(){
        $type = Yii::app()->request->getQuery('type',1);//二手房
        $data = array();
        $kw = Yii::app()->request->getQuery('kw','');

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
        $xs->addRange('category',1,1);
        $xs->addRange('expire_time',time(),null);
        $xs->addRange('sale_status',1,1);
        $xs->addRange('deleted',0,0);
        $xs->setFacets('area', true)->search();
        $nums = $xs->getFacets('area');
        $areas = AreaExt::model()->normal()->findAll(array('index'=>'id','condition'=>'parent=0'));
        $data['total'] = array_sum($nums);
        $tmp = array();
        $data['lists'] = array();
        foreach($nums as $k=>$v)
        {
            if(!isset($areas[$k])) continue;
            $tmp = array('id'=>$areas[$k]->id,'name'=>$areas[$k]->name, 'lng'=>$areas[$k]->map_lng, 'lat'=>$areas[$k]->map_lat, 'num'=>$v);
            $data['lists'][] = $tmp;
        }
        $this->controller->frame['data'] = $data;
    }
}
