<?php
/*
* 小区列表
* @author liyu
* @created 2016年10月21日14:42:20
*/
class ListAction extends CAction{
    public function run(){
        $params = [];
        $sort = '';
        $ids = [];
        /*
        * sort = [0=>'默认排序按地理位置',4=>'评估均价由低到高排序',3=>'评估均价由高到低排序',1=>'按二手房数量由高到低排序',2=>'按租房数量由低到高排序']
        */
        foreach(['area','limit','page','pricetag','miniprice','maxprice','kw','sort','fid','street','infoid'] as $v){
            $params[$v] = '';
            if($value = Yii::app()->request->getQuery("$v",0)){
                $params[$v] = $value;
            }
        }
        //设置默认值
        $params['limit'] = $params['limit']?$params['limit']:20;
        $params['page'] = $params['page']?$params['page']:1;
        $params['kw'] = $params['kw']?$params['kw']:'';
        //
        $xs = Yii::app()->search->house_plot;
        $xs->setQuery($params['kw']);
        $xs->setFacets(array('status'), true);
        //对area street pricetag 的传参进行讯搜操作
        if($params['pricetag']){
            $priceTag = TagExt::model()->findByPk($params['pricetag']);
            $xs->addRange('esf_price',$priceTag->min,$priceTag->max?$priceTag->max:null);
        }
        $params['miniprice'] and $xs->addRange('esf_price',$params['miniprice'],null);
        $params['maxprice'] and $xs->addRange('esf_price',null,$params['maxprice']);
        //判断area是area 还是街道
        // $areas = AreaExt::getAllarea();
        // if($params['area']){
        //     if(in_array($params['area'],array_keys($areas))){
        //         $xs->addRange('area',$params['area'],$params['area']);
        //     }else{
        //         $xs->addRange('street',$params['area'],$params['area']);
        //     }
        // }
        $params['area'] && $xs->addRange('area',$params['area'],$params['area']);
        $params['street'] && $xs->addRange('street',$params['street'],$params['street']);
        //其他讯搜条件
        $xs->addRange('status', 1, 1);
        $xs->addRange('deleted', 0, 0);
        //$xs->addRange('sale_status',1,1);
        $docs = $xs->search();
        if($params['sort']){
            switch($params['sort']){
                //估计  由低到高 false 倒序
                case 3:
                    $mysort = ['esf_price'=>false,'resold_sort'=>false, 'open_time'=>false];
                    //$sort = 'esf_price desc,sort desc,open_time desc';
                    $xs->setMultiSort($mysort);
                    break;
                case 4:
                    $mysort = ['esf_price'=>true,'resold_sort'=>false, 'open_time'=>false];
                    //$sort = 'esf_price asc,sort desc,open_time desc';
                    $xs->setMultiSort($mysort);
                    break;
                // case 3:
                //     //小区房龄高--低
                //     $mysort = ['open_time'=>false,'sort'=>false];
                //     $sort = 'open_time desc,sort desc';
                //     $xs->setMultiSort($mysort);
                //     break;
                // case 4:
                //     //小区房龄低--高
                //     $mysort = ['open_time'=>true,'sort'=>false];
                //     $sort = 'open_time asc,sort desc';
                //     $xs->setMultiSort($mysort);
                //     break;
                case 1:
                    $mysort = ['esf_num'=>false,'resold_sort'=>false];
                    $xs->setMultiSort($mysort);
                    break;
                case 2:
                    $mysort = ['esf_num'=>true,'resold_sort'=>false];
                    $xs->setMultiSort($mysort);
                    break;
                // case 7:
                //     $mysort = ['zf_num'=>false,'sort'=>false];
                //     $xs->setMultiSort($mysort);
                //     break;
                // case 8:
                //     $mysort = ['zf_num'=>true,'sort'=>false];
                //     $xs->setMultiSort($mysort);
                //     break;
            }
        }else{
            $lat = isset($_COOKIE['resold_plot_lat'])&&$_COOKIE['resold_plot_lat'] ? $_COOKIE['resold_plot_lat'] : '';
            $lng = isset($_COOKIE['resold_plot_lng'])&&$_COOKIE['resold_plot_lng'] ? $_COOKIE['resold_plot_lng'] : '';
            if($lat && $lng)
            {
                Yii::log('lat:'.$lat.',lng:'.$lng);
                $xs->setGeodistSort(['map_lng'=>$lng, 'map_lat'=>$lat]);
            }
            else
            {
                // $sort = 'sort desc,open_time desc';
                $xs->setMultiSort(array('resold_sort'=>false, 'esf_num'=>false, 'open_time'=>false));
            }
        }
        $count = 0;
        $count = array_sum($xs->getFacets('status'));


        $pager = new CPagination($count);
        $pager->pageSize = $params['limit'];

        $xs->setLimit($params['limit'], $params['limit'] * ($params['page'] - 1));//设置limit
        $docs = $xs->search();
        $plot = [];
        //die(var_dump($docs->zf_num));
        if($docs){
            foreach ($docs as $key => $value) {

                if($params['infoid'] && $params['infoid'] == $value->id) //bug waiting for change
                    continue;
                $ids[] = $value->id;
	        	$plotinfo = PlotExt::model()->with('avg_esf')->findByPk($value->id);
                $zfcount = $value->zf_num;
                $esfcount = $value->esf_num;
                $plot[] = ['id'=>$plotinfo->id,'title'=>$plotinfo->title,'sort'=>$plotinfo->sort,'price'=>$plotinfo->avg_esf?$plotinfo->avg_esf->price:0,'unit'=>'元/套','area'=>$plotinfo->area?$plotinfo->areaInfo->name:'','street'=>$plotinfo->street?$plotinfo->streetInfo->name:'','image'=>ImageTools::fixImage($plotinfo->image,88,66),'zfcount'=>$zfcount,'esfcount'=>$esfcount,'open_time'=>date('Y-m-d',$plotinfo->open_time)];
	        }
        }
        // sort remaining to improve
        // use krsort when commanding sort by esf_num
        // if($params['sort'] == 1)
        // {
        //     if($plot)
        //         foreach ($plot as $key => $value) {
        //             $plotResold = PlotResoldDailyExt::getLastInfoByHid($value->id)->esf_num;
        //             $transPlot[$plotResold][] = $value;
        //         }
        //     krsort($transPlot);
        //     $plot = [];
        //     if($transPlot)
        //         foreach ($transPlot as $key => $value) {
        //             $plot = array_merge($plot,$value);
        //         }
        // }
        //
        // $data = [];
        // if($plot){
        //     foreach($plot as $k=>$v){
        //         if(!$v)
        //             continue;
        //         $farea = $v->areaInfo?$v->areaInfo->name:'';
        //         $street = $v->streetInfo?$v->streetInfo->name:'';
        //         $criteria = new CDbCriteria;
        //
        //         $criteria->addCondition('hid=:hid and category=1 and expire_time>:time');
        //         $criteria->params[':hid'] = $v->id;
        //         $criteria->params[':time'] = time();
        //         // $plotResold = PlotResoldDailyExt::getLastInfoByHid($v->id);
        //         $image = $v->image?$v->image:SM::resoldImageConfig()->resoldNoPic();
        //         $data[] = ['id'=>$v->id,'title'=>$v->title,'sort'=>$v->sort,'price'=>$v->price,'area'=>$farea,'street'=>$street,'image'=>ImageTools::fixImage($image,88,66),'zfcount'=>ResoldZfExt::model()->saling()->count($criteria),'esfcount'=>ResoldEsfExt::model()->saling()->count($criteria),'open_time'=>date('Y-m-d',$v->open_time)];
        //     }
        // }
        $filters = [];
        if($params){
            foreach($params as $k=>$v)
                $filters['chosen_'.$k] = $v;
        }
        $trans = ['plot'=>$plot,'page_count'=>$pager->pageCount,'totalNum'=>$count,'page'=>$params['page']];
        $trans = array_merge($trans,['filters'=>$filters]);
        $this->controller->frame['data'] = $trans;

    }

}
