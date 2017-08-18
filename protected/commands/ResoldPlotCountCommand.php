<?php
/**
 * 二手房定时统计脚本
 * 半小时统计一次
 * 每次运行时删除当天先前的数据，不然量太大
 * @author steven.allen <[<email address>]>
 * @date 2016.09.20
 */
class ResoldPlotCountCommand extends CConsoleCommand
{
	public function actionIndex()
	{
        // 删当天的
        $year = date('Y',time());
        $month = date('m',time());
        $day = date('d',time());
        PlotResoldDailyExt::model()->deleteAllByAttributes(['year'=>$year,'month'=>$month,'day'=>$day]);
        // 插入
        $offset = $count = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => 50,
            'offset' => $offset++ * 50,
            'order' => 'id asc',
        ));
        $plots = PlotExt::model()->normal()->findAll($criteria);
        
        if($plots)
        {
            foreach ($plots as $key => $value) {
               $hids[] = $value->id;
            }
            $where = ' where deleted=0 and sale_status=1 and expire_time>'.time().' and status=1 and hid in('.implode(',',$hids).')';
            $group = ' group by category,hid';
            $esfPriceSql = 'select avg(ave_price) as image_count,hid,category from resold_esf'.$where.' and price>0' . $group;
            $zfPriceSql = 'select avg(price) as image_count,hid,category from resold_zf'.$where. 'and price>0'. $group;
            $esfNumSql = 'select count(id) as num,hid,category from resold_esf'.$where.$group;
            $zfNumSql = 'select count(id) as num,hid,category from resold_zf'.$where.$group;
            
            $esfPriceRes = Yii::app()->db->createCommand($esfPriceSql)->queryAll();
            $zfPriceRes = Yii::app()->db->createCommand($zfPriceSql)->queryAll();
            $esfNumRes = Yii::app()->db->createCommand($esfNumSql)->queryAll();
            $zfNumRes = Yii::app()->db->createCommand($zfNumSql)->queryAll();
            
            // 组成 [hid][category] 二维数组
            if($esfPriceRes)
                foreach ($esfPriceRes as $key => $value)
                    $esfPrice[$value['hid']][$value['category']] = $value['image_count'];
            if($zfPriceRes)
                foreach ($zfPriceRes as $key => $value)
                    $zfPrice[$value['hid']][$value['category']] = $value['image_count'];
            if($esfNumRes)
                foreach ($esfNumRes as $key => $value)
                    $esfNum[$value['hid']][$value['category']] = $value['num'];
            if($zfNumRes)
                foreach ($zfNumRes as $key => $value)
                    $zfNum[$value['hid']][$value['category']] = $value['num'];

            foreach($plots as $plot) {
                foreach ([1,2,3] as $key => $value) { // 对分类进行遍历

                    $daily = new PlotResoldDailyExt;
                    $daily->esf_price = $daily->zf_price = $daily->esf_num = $daily->zf_num = 0;
                    $daily->date = time();
                    $daily->category = $value;
                    $daily->hid = $plot->id;

                    if(isset($esfPrice[$plot->id]) && isset($esfPrice[$plot->id][$value]))
                        $daily->esf_price = $esfPrice[$plot->id][$value];
                    if(isset($zfPrice[$plot->id]) && isset($zfPrice[$plot->id][$value]))
                        $daily->zf_price = $zfPrice[$plot->id][$value];
                    if(isset($esfNum[$plot->id]) && isset($esfNum[$plot->id][$value]))
                        $daily->esf_num = $esfNum[$plot->id][$value];
                    if(isset($zfNum[$plot->id]) && isset($zfNum[$plot->id][$value]))
                        $daily->zf_num = $zfNum[$plot->id][$value];

                    $daily->save();
                }
                $count++;
                $plot->save();
            }
            echo "处理完成".$count."条\n";
            goto begin;
        }
        
        // 更新数据
        foreach (SchoolExt::model()->findAll() as $key => $value) {
            $value->save();
        }

        // 你懂得
        // $sql = "update resold_esf set hits=hits+floor(rand()*2) where source=2 and sale_status=1";
        // Yii::app()->db->createCommand($sql)->execute();
        // $sql = "update resold_zf set hits=hits+floor(rand()*2) where source=2 and sale_status=1";
        // Yii::app()->db->createCommand($sql)->execute();
        echo "完成\n";
	}
}