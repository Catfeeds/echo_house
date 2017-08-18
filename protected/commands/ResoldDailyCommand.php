<?php
/**
 * 二手房日常数据统计脚本
 * @author steven.allen <[<email address>]>
 * @date 2016.09.20
 */
class ResoldDailyCommand extends CConsoleCommand
{
	public function actionAvePrice()
	{
        $daily = new ResoldDailyExt;
        $date = strtotime("-1 day");
        $where = " where deleted=0 and sale_status=1 and sale_time<=".TimeTools::getDayEndTime(strtotime("-1 day"))." and sale_time>=".TimeTools::getDayBeginTime(strtotime("-1 day")).' and expire_time>'.time();

        $esfPrice = ResoldEsfExt::model()->findBySql("select avg(ave_price) as price from resold_esf".$where." and ave_price>0");
        $esfSize  = ResoldEsfExt::model()->findBySql("select sum(size) as size from resold_esf".$where);
        $esfNum   = Yii::app()->db->createCommand("select count(*) as num from resold_esf".$where)->queryRow();

        $zfPrice = ResoldZfExt::model()->findBySql("select avg(price) as price from resold_zf".$where." and price>0");
        $zfSize = ResoldZfExt::model()->findBySql("select sum(size) as size from resold_zf".$where);
        $zfNum = Yii::app()->db->createCommand("select count(*) as num from resold_zf".$where)->queryRow();

        $daily->date = $date;
        $daily->esf_price = (int)$esfPrice['price'];
        $daily->esf_size =  (int)$esfSize['size'];
        $daily->esf_num =   (int)$esfNum['num'];

        $daily->zf_price =  (int)$zfPrice['price'];
        $daily->zf_size =   (int)$zfSize['size'];
        $daily->zf_num =    (int)$zfNum['num'];

        $areas = AreaExt::model()->findAll(['condition'=>'parent=0']);
        $areainfo = [];
        if($areas)
            foreach ($areas as $key => $value) {
                $where = " where area=".$value['id']." and deleted=0 and sale_status=1 and sale_time<=".TimeTools::getDayEndTime(strtotime("-1 day"))." and sale_time>=".TimeTools::getDayBeginTime(strtotime("-1 day")).' and expire_time>'.time();
                $esfPrice = ResoldEsfExt::model()->findBySql("select avg(ave_price) as price from resold_esf".$where." and ave_price>0");
                $esfSize  = ResoldEsfExt::model()->findBySql("select sum(size) as size from resold_esf".$where);
                $esfNum   = Yii::app()->db->createCommand("select count(*) as num from resold_esf".$where)->queryRow();

                $zfPrice = ResoldEsfExt::model()->findBySql("select avg(price) as price from resold_zf".$where." and price>0");
                $zfSize = ResoldEsfExt::model()->findBySql("select sum(size) as size from resold_zf".$where);
                $zfNum = Yii::app()->db->createCommand("select count(*) as num from resold_zf".$where)->queryRow();
                $ar = [
                'a'=>$value['id'],
                'ep'=>$esfPrice['price']?(int)$esfPrice['price']:0,
                'es'=>$esfSize['size']?(int)$esfSize['size']:0,
                'em'=>$esfNum['num']?(int)$esfNum['num']:0,
                'zp'=>$zfPrice['price']?(int)$zfPrice['price']:0,
                'zs'=>$zfSize['size']?(int)$zfSize['size']:0,
                'zm'=>$zfNum['num']?(int)$zfNum['num']:0,
                ];
                $areainfo[] = $ar;
            }
        $daily->areainfo = json_encode($areainfo);
        $daily->save();
            
	}
}