<?php
/*
* 小区找房
* @author liyu
* @created 2016年10月28日14:19:193
*/
class PlotAction extends CAction{
    public function run($streetid=0,$type=1){
        //1 二手房  2 租房
        $type = Yii::app()->request->getQuery('type',1);//二手房
        $data = array();
        $kw = Yii::app()->request->getQuery('kw','');
        $street = Yii::app()->request->getQuery('street',0);
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
        $street and $xs->addRange('street',$street,$street);
        $xs->setFacets('hid', true)->search();
        $nums = $xs->getFacets('hid');
        $plot = PlotExt::model()->normal()->findAll(array('index'=>'id'));
        $data['total'] = array_sum($nums);
        $tmp = array();
        $data['lists'] = array();
        foreach($nums as $k=>$v)
        {
            if(!isset($plot[$k])) continue;
            $plotResold = PlotResoldDailyExt::getLastInfoByHid($plot[$k]->id);
            $tmp = array('id'=>$plot[$k]->id,'name'=>$plot[$k]->title, 'lng'=>$plot[$k]->map_lng, 'lat'=>$plot[$k]->map_lat, 'num'=>$v, 'esf_price'=>$plotResold?$plotResold->esf_price:0, 'zf_price'=>$plotResold?$plotResold->zf_price:0);
            $data['lists'][] = $tmp;
        }
        $data['parent'] = $street;
        $this->controller->frame['data'] = $data;
    }
}
