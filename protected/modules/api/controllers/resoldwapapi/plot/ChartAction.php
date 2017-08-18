<?php
/*
* 小区房价价格走势图
* @author liyu
* @created 2016年10月26日09:56:27
*/
class ChartAction extends CAction{
    public function run(){
        //房价走势
        $hid = Yii::app()->request->getQuery('hid',0);
        if(!$hid){
            $this->controller->frame['status'] = 'error';
            $this->controller->frame['msg'] = '请输入小区id';
        }
        $plot = PlotExt::model()->normal()->findByPk($hid);
        $priceTrend = new ResoldPlotPriceChart($plot);
        $cat = array();
        foreach ($priceTrend->date as $key => $value) {
            $cat[] = $value;
        }
        $lpj = array();//楼盘价格
        foreach ($priceTrend->plotPriceList as $key => $value) {
            $lpj[] = round($value/1000,2);
        }
        $qyj = array();//区域价格
        foreach ($priceTrend->areaPriceList as $key => $value) {
            $qyj[] = round($value/1000,2);
        }
        $ctj = array();//全市价格
        foreach ($priceTrend->cityPriceList as $key => $value) {
            $ctj[] = round($value/1000,2);
        }
        $data = [];
        $tmp['title'] = $plot->title;
        $tmp['data'] = $lpj;
        $data['lpj'] = $tmp;
        unset($tmp);
        $tmp['title'] = $plot->areaInfo->name.'二手房价';
        $tmp['data'] = $qyj;
        $data['qyj'] = $tmp;
        unset($tmp);
        $tmp['title'] = SM::urmConfig()->cityName->value.'二手房价';
        $tmp['data'] = $ctj;
        $data['ctj'] = $tmp;

        $rate = PlotExt::PlotRate($plot);
        //die(var_dump($rate));
        $datas = array(
            'price' => $plot->avg_esf?$plot->avg_esf->price:0,
            'lastMonthP'=>$rate['lastMouthP'],
            'lastYearP'=>$rate['lastYearP'],
            'categories' => $cat,
            'datas' => $data,
            );
        $this->controller->frame['data'] = $datas;
    }
}
