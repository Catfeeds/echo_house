<?php
/**
 * 根据坐标获取楼盘/街道
 * 为了速度，获取街道时，走迅搜查某个区域内的楼盘，再把楼盘按照街道汇总
 * @author steven allen <[<email address>]>
 * @date(2016.11.1)
 */
class NowLocationAction extends CAction
{
	public function run()
	{
		$tmp = array();
		$type = Yii::app()->request->getQuery('type',0);
        $kw = Yii::app()->request->getQuery('kw','');
        $lng = Yii::app()->request->getQuery('lng',0);
        $lat = Yii::app()->request->getQuery('lat',0);
        $level = Yii::app()->request->getQuery('level',0);
        $distance = Yii::app()->request->getQuery('distance',0);
        $points = Yii::app()->request->getQuery('points',[]);
        if(!$type || !$level)
        	return $this->controller->returnError('参数错误');
        $criteria = new CDbCriteria(array(
            'limit' => 100
        ));
        // if($points) {
        //     $criteria->addBetweenCondition('map_lat', $points['right_top']['lat'], $points['left_top']['lat']);
        //     $criteria->addBetweenCondition('map_lng', $points['left_bottom']['lng'], $points['right_bottom']['lng']);
        // }else {
        //     $mapArea = $this->getNearDistance( $lat, $lng, $distance / 2 );
        //     $criteria->addBetweenCondition('map_lat', $mapArea['lat2'], $mapArea['lat1']);
        //     $criteria->addBetweenCondition('map_lng', $mapArea['lng2'], $mapArea['lng1']);
        // }
        
        // if($kw)
        //     $criteria->addSearchCondition('title',$kw);
        // if($level==1)
        // {
        // 	$model = AreaExt::model();
        // 	$criteria->addCondition('parent!=0');
        //     $plots = $model->findAll($criteria);
        // }
        // else
        // {
            $xs = Yii::app()->search->house_plot;
            $xs->setQuery($kw);
            $xs->addRange('deleted',0,0);
            $xs->addRange('status',1,1);
            $type==1 && $xs->addRange('esf_num',1,null);
            $type==2 && $xs->addRange('zf_num',1,null);
            if($points) {
                $xs->addRange('map_lat', $points['right_top']['lat'], $points['left_top']['lat']);
                $xs->addRange('map_lng', $points['left_bottom']['lng'], $points['right_bottom']['lng']);
            } else {
                $mapArea = $this->getNearDistance( $lat, $lng, $distance / 2 );
                $xs->addRange('map_lat', $mapArea['lat2'], $mapArea['lat1']);
                $xs->addRange('map_lng', $mapArea['lng2'], $mapArea['lng1']);
            }
            $xs->setLimit(100);
            $plots = $xs->search();

        	// $model = PlotExt::model()->normal();
        // }
        
        $count = 0;
        $ave_price = 0;
        $areaPlot = [];
        foreach($plots as $v)
        {
            $data = array();
        	$esfNum = 0;
        	if($level==1) {
                if($type==1)
                    $areaPlot[$v->street] = isset($areaPlot[$v->street]) && $areaPlot[$v->street] ? ($areaPlot[$v->street] + $v->esf_num) : $v->esf_num;
                else
                    $areaPlot[$v->street] = isset($areaPlot[$v->street]) && $areaPlot[$v->street] ? ($areaPlot[$v->street] + $v->zf_num) : $v->zf_num;

        		// $esfNum = $type==1?Yii::app()->db->createCommand("select count(id) from resold_esf where sale_status=1 and category=1 and expire_time>".time()." and street=".$v->id)->queryScalar():Yii::app()->db->createCommand("select count(id) from resold_zf where sale_status=1 and category=1 and expire_time>".time()." and street=".$v->id)->queryScalar();
            }
        	elseif($level==2)
            {
                // $criteria = new CDbCriteria;

                // $criteria->addCondition('hid=:hid and category=1');
                // $criteria->params[':hid'] = $v->id;
                // $plotResold = PlotResoldDailyExt::getLastInfoByHid($v->id,1);
        		// $esfNum = $type==1?Yii::app()->db->createCommand("select count(id) from resold_esf where sale_status=1 and category=1 and expire_time>".time()." and hid=".$v->id)->queryScalar():Yii::app()->db->createCommand("select count(id) from resold_zf where sale_status=1 and category=1 and expire_time>".time()." and hid=".$v->id)->queryScalar();
                $ave_price = $type==1?$v->esf_price:($type==2?$v->zf_price:0);
            }
            $esfNum = $type==1?$v->esf_num:($type==2?$v->zf_num:0);
            $count += $esfNum;
            // 楼盘数据
            if($level == 2) {
                if($esfNum && $v) {
                    $data = array(
                        'id' => $v->id,
                        'name' => $v->title,
                        'lng' => $v->map_lng,
                        'lat' => $v->map_lat,
                        'num'=>$esfNum,
                        'ave_price'=>$ave_price
                    );
                }
                $data && $tmp['areas'][] = $data;
            } 
        }
        // 街道数据
        if($level == 1 && $areaPlot) {
            $criteria = new CDbCriteria;
            $criteria->addCondition('status=1');
            $criteria->addInCondition('id',array_keys($areaPlot));
            $areas = AreaExt::model()->findAll($criteria);
            if($areas)
                foreach ($areas as $v) {
                    $data = [
                        'id' => $v->id,
                        'name' => $v->name,
                        'lng' => $v->map_lng,
                        'lat' => $v->map_lat,
                        'num'=>$areaPlot[$v->id],
                        'ave_price'=>0
                    ];
                    $tmp['areas'][] = $data;
                }
        }
        $tmp['count'] = $count;
        $this->controller->frame['data'] = $tmp;
	}

	/**
     * 根据地图某点获取附近的范围
     * @param string $lat
     * @param string $lng
     * @param number $distance
     * @return multitype:string
     */
    private function getNearDistance($lat = '', $lng = '' , $distance = 0) {
        $earth_radius = 6378137;

        //$distance = $distance / 10;
        $dlng = 2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance/$earth_radius;
        $dlat = rad2deg($dlat);

        return array(
            'lat1'=>sprintf('%.7f', ($lat+$dlat)),
            'lat2'=>sprintf('%.7f', ($lat-$dlat)),
            'lng1'=>sprintf('%.7f', ($lng+$dlng)),
            'lng2'=>sprintf('%.7f', ($lng-$dlng)),
        );
    }
}