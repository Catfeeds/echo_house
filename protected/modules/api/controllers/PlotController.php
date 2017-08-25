<?php
class PlotController extends ApiController{
	public function actionList()
	{
		$area = (int)Yii::app()->request->getQuery('area',0);
		$street = (int)Yii::app()->request->getQuery('street',0);
		$aveprice = (int)Yii::app()->request->getQuery('aveprice',0);
		$sfprice = (int)Yii::app()->request->getQuery('sfprice',0);
		$sort = (int)Yii::app()->request->getQuery('sort',0);
		$wylx = (int)Yii::app()->request->getQuery('wylx',0);
		$toptag = (int)Yii::app()->request->getQuery('toptag',0);
		$company = (int)Yii::app()->request->getQuery('company',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		$criteria = new CDbCriteria;
		if($kw) {
			$criteria->addSearchCondition('title',$kw);
		}
		if($area) {
			$criteria->addCondition('area=:area');
			$criteria->params[':area'] = $area;
		}
		if($street) {
			$criteria->addCondition('street=:street');
			$criteria->params[':street'] = $street;
		}
		$ids = $companyids = [];
		// var_dump($toptag,$sfprice,$wylx);exit;
		foreach (['sfprice','wylx','toptag'] as $key => $value) {
			if($$value) {
				$idarr = Yii::app()->db->createCommand("select hid from plot_tag where tid=".$$value)->queryAll();
				if($idarr) {
					$tmp = [];
					foreach ($idarr as $hid) {
						$tmp[] = $hid['hid'];
					}
					if($ids) {
						$ids = array_intersect($ids,$tmp);
					} else {
						$ids = $tmp;
					}
				}
				
			}
		}
		// $ids = array_intersect($ids,$tagids);
		
		if($company) {
			$idarr = Yii::app()->db->createCommand("select hid from plot_company where cid=$company")->queryAll();
			if($idarr) {
				foreach ($idarr as $hid) {
					$companyids[] = $hid['hid'];
				}
			}
			if($ids) {
				$ids = array_intersect($ids,$companyids);
			} else {
				$ids = $companyids;
			}
		}
		// var_dump($ids);exit;
		// $ids = array_intersect($ids,$companyids);
		if($sfprice>0||$wylx>0||$toptag>0||$company>0) {
			$criteria->addInCondition('id',$ids);
		}
		if($aveprice) {
			if($tag = TagExt::model()->findByPk($aveprice)) {
				$criteria->addCondition('price<=:max and price>=:min');
				$criteria->params[':max'] = $tag->max;
				$criteria->params[':min'] = $tag->min;
			}
		}
		if($sort) {
			switch ($sort) {
				case '1':
					$criteria->order = 'price desc';
					break;
				case '2':
					$criteria->order = 'price asc';
					break;
				default:
					# code...
					break;
			}
		} else {	
			if(isset($_COOKIE['house_lng']) && isset($_COOKIE['house_lat'])) {
				$city_lat = $_COOKIE['house_lat'];
				$city_lng = $_COOKIE['house_lng'];
				$criteria->order = 'ACOS(SIN(('.$city_lat.' * 3.1415) / 180 ) *SIN((map_lat * 3.1415) / 180 ) +COS(('.$city_lat.' * 3.1415) / 180 ) * COS((map_lat * 3.1415) / 180 ) *COS(('.$city_lng.' * 3.1415) / 180 - (map_lng * 3.1415) / 180 ) ) * 6380  asc';
			} else {
				$criteria->order = 'sort desc,updated desc';
			}
		}

		$plots = PlotExt::model()->normal()->getList($criteria);
		$lists = [];
		if($datares = $plots->data) {
			foreach ($datares as $key => $value) {
				if($area = $value->areaInfo)
					$areaName = $area->name;
				else
					$areaName = '';
				if($street = $value->streetInfo)
					$streetName = $street->name;
				else
					$streetName = '';
				if($company) {
					// unset($company);
					$companydes = Yii::app()->db->createCommand("select id,name from company where id=$company")->queryRow();
				} else {
					if($companydes = $value->getItsCompany()) {
						$companydes = $companydes[0];
					} else {
						$companydes = [];
					}
				}
					
				// var_dump(Yii::app()->user->getIsGuest());exit;
				if(Yii::app()->user->getIsGuest()) {
					$pay = '';
				} elseif($pays = $value->pays) {
					$pay = $pays[0]['name'].'('.count($pays).'个方案)';
				} else {
					$pay = '';
				}
				$lists[] = [
					'id'=>$value->id,
					'title'=>$value->title,
					'price'=>$value->price,
					'area'=>$areaName,
					'street'=>$streetName,
					'image'=>ImageTools::fixImage($value->image),
					'zd_company'=>$companydes,
					'pay'=>$pay,
					'distance'=>$this->getDistance($value),
				];
			}
			$pager = $plots->pagination;
			$this->frame['data'] = ['list'=>$lists,'page'=>$page,'num'=>$pager->itemCount,'page_count'=>$pager->pageCount,];
		}
	}

	public function getDistance($obj)
	{
		if(isset($_COOKIE['house_lng']) && isset($_COOKIE['house_lat'])) {
			$lat = $_COOKIE['house_lat'];
			$lng = $_COOKIE['house_lng'];
			$house_lng = $obj->map_lng?$obj->map_lng:SiteExt::getAttr('qjpz','map_lng');
			$house_lat = $obj->map_lat?$obj->map_lat:SiteExt::getAttr('qjpz','map_lat');
			return $this->countDistance($lng,$lat,$house_lng,$house_lat);
		} else {
			return 0;
		}
	}

	public function countDistance($lng1,$lat1,$lng2,$lat2)
	{
		$radLat1=deg2rad($lat1);
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;//两纬度之差,纬度<90
        $b=$radLng1-$radLng2;//两经度之差纬度<180
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
        return $s;
	}
}