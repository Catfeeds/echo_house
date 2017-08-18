<?php
/**
 * 定时任务脚本
 * @author tivon
 * @date 2015-12-04
 */
class CrontabCommand extends CConsoleCommand
{
    /**
     * 定时统计价格走势数据
     * 执行规则：每月1日0时执行，统计上一个月的数据
     */
    public function actionPriceTrend()
    {
        $page = 0;
        $areas = AreaExt::model()->parent()->findAll();
        $data = array();
        $criteria = new CDbCriteria(array(
            'select' => 'avg(price) as price,area',
            'condition' => 'price_mark=1 and unit=1 and price>0',
        ));
        $row = PlotExt::model()->normal()->isNew()->find($criteria);
        $data[0] = $row->price ? round($row->price, 2) : 0;
        $criteria->addCondition('area=:area');
        foreach ($areas as $v) {
            $criteria->params[':area'] = $v->id;
            $row = PlotExt::model()->normal()->isNew()->find($criteria);
            $data[$v->id] = $row->price ? round($row->price, 2) : 0;
        }
        $model = new PlotPricetrendExt;
        $model->formatTime = date('Y-m', strtotime('-1 month'));
        $model->data = $data;
        $model->save();
    }

    public function actionResoldPrice($month=0)
    {
        if($month)
            $date = strtotime(date('Y').'-'.(strlen($month)==1?('0'.$month):$month).'-01');
        else
            $date = strtotime('-1 month');

        /**
         * 二手房定时统计区域价格走势数据
         */
        $areas = AreaExt::model()->parent()->findAll();
        $esfRow = Yii::app()->db->createCommand("select avg(ave_price) as price,area from resold_esf where category=1 and sale_status=1 and deleted=0 and ave_price>0 and created>=".TimeTools::getMonthBeginTime($date).' and created<='.TimeTools::getMonthEndTime($date))->queryRow();
        $esfData[0] = $esfRow['price'] ? (int)($esfRow['price']) : 0;
        foreach ($areas as $v) {
            $esfCriteria->params[':area'] = $v->id;
            $esfRow = Yii::app()->db->createCommand("select avg(ave_price) as price,area from resold_esf where category=1 and sale_status=1 and deleted=0 and ave_price>0 and area=".$v->id." and created>=".TimeTools::getMonthBeginTime($date).' and created<='.TimeTools::getMonthEndTime($date))->queryRow();
            $esfData[$v->id] = $esfRow['price'] ? (int)($esfRow['price']) : 0;
        }
        $ResoldPriceTrend = new ResoldPricetrendExt();
        $ResoldPriceTrend->formatTime = date('Y-m', $date);
        $ResoldPriceTrend->data = $esfData;
        $ResoldPriceTrend->save();

        // 二手房定时统计楼盘价格走势数据
        // 
        $esfPlotSql = "SELECT avg(ave_price) as price,hid FROM `resold_esf` where sale_status=1 and deleted=0 and ave_price>0 and created<=".TimeTools::getMonthEndTime($date)." and created>=".TimeTools::getMonthBeginTime($date)." group by hid";
        $esfs = Yii::app()->db->createCommand($esfPlotSql)->queryAll();
        foreach ($esfs as $key => $value) {
            $ResoldPlotPrice = new ResoldPlotPriceExt();
            $ResoldPlotPrice->new_time = date('Y-m', $date);
            $ResoldPlotPrice->price = $value['price'] ? (int)($value['price']) : 0;
            $ResoldPlotPrice->hid = $value['hid'];
            $ResoldPlotPrice->save();
        }
    }

}
