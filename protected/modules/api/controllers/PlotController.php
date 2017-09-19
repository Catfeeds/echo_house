<?php
class PlotController extends ApiController{
	public function actionList()
	{
		$info_no_pic = SiteExt::getAttr('qjpz','info_no_pic');
		$area = (int)Yii::app()->request->getQuery('area',0);
		$street = (int)Yii::app()->request->getQuery('street',0);
		$aveprice = (int)Yii::app()->request->getQuery('aveprice',0);
		$sfprice = (int)Yii::app()->request->getQuery('sfprice',0);
		$sort = (int)Yii::app()->request->getQuery('sort',0);
		$wylx = (int)Yii::app()->request->getQuery('wylx',0);
		$zxzt = (int)Yii::app()->request->getQuery('zxzt',0);
		$limit = (int)Yii::app()->request->getQuery('limit',20);
		$toptag = (int)Yii::app()->request->getQuery('toptag',0);
		$company = (int)Yii::app()->request->getQuery('company',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		$init = 0;
		if($area+$street+$aveprice+$sfprice+$sort+$wylx+$zxzt+$toptag+$company==0&&$page==1&&!$kw) {
			$init = 1;
		}
		$criteria = new CDbCriteria;
		if($kw) {
			$criteria1 = new CDbCriteria;
			$criteria1->addSearchCondition('name',$kw);
			$compas = CompanyExt::model()->find($criteria1);
			// var_dump($compas);exit;
			// $compas && $company = $compas['id'];
			if($compas) {
				$company = $compas['id'];
			}else
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
		foreach (['sfprice','wylx','toptag','zxzt'] as $key => $value) {
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
			// $idarr = Yii::app()->db->createCommand("select hid from plot_company where cid=$company")->queryAll();
			// // var_dump($idarr);exit;
			// if($idarr) {
			// 	foreach ($idarr as $hid) {
			// 		$companyids[] = $hid['hid'];
			// 	}
			// }
			// if($ids) {
			// 	$ids = array_intersect($ids,$companyids);
			// } else {
			// 	$ids = $companyids;
			// }
			$criteria->addCondition('company_id=:comid');
			$criteria->params[':comid'] = $company;
		}
		// var_dump($ids);exit;
		// $ids = array_intersect($ids,$companyids);
		if($sfprice>0||$wylx>0||$toptag>0) {
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
			if($sort == 3 && isset($_COOKIE['house_lng']) && isset($_COOKIE['house_lat'])) {
				// var_dump(1);exit;
				$city_lat = $_COOKIE['house_lat'];
				$city_lng = $_COOKIE['house_lng'];
				$criteria->order = 'ACOS(SIN(('.$city_lat.' * 3.1415) / 180 ) *SIN((map_lat * 3.1415) / 180 ) +COS(('.$city_lat.' * 3.1415) / 180 ) * COS((map_lat * 3.1415) / 180 ) *COS(('.$city_lng.' * 3.1415) / 180 - (map_lng * 3.1415) / 180 ) ) * 6380  asc';
			}
		} else {	
			$criteria->order = 'sort desc,updated desc';
		}
		// 走缓存拿初始数据
		if($init) {
			$dats = PlotExt::setPlotCache();
			if(isset($dats['list']) && $dats['list']) {
				foreach ($dats['list'] as $key => $value) {
					// var_dump($value);exit;
					$dats['list'][$key]['distance'] = round($this->getDistance($value['distance']),2);
				}
			}
			$this->frame['data'] = $dats;
		} else {
			// var_dump($criteria);exit;
			$plots = PlotExt::model()->normal()->getList($criteria,$limit);
			$lists = [];
			// if($company) {
			// 	$companydes = Yii::app()->db->createCommand("select id,name from company where id=$company")->queryRow();
			// }
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
					// if(!$company) {
					$companydes = ['id'=>$value->company_id,'name'=>$value->company_name];
					// }
					$wyw = '';
					$wylx = $value->wylx;
					if($wylx) {
						if(!is_array($wylx)) 
							$wylx = [$wylx];
						foreach ($wylx as $w) {
							$t = TagExt::model()->findByPk($w)->name;
							$t && $wyw .= $t.' ';
						}
						$wyw = trim($wyw);
					}
					
					
					// var_dump(Yii::app()->user->getIsGuest());exit;
					// if(Yii::app()->user->getIsGuest()) {
					// 	$pay = '';
					// } elseif($pays = $value->pays) {
					// 	$pay = $pays[0]['price'].(count($pays)>1?'('.count($pays).'个方案)':'');
					// } else {
					// 	$pay = '';
					// }
					$lists[] = [
						'id'=>$value->id,
						'title'=>Tools::u8_title_substr($value->title,18),
						'price'=>$value->price,
						'unit'=>PlotExt::$unit[$value->unit],
						'area'=>$areaName,
						'street'=>$streetName,
						'image'=>ImageTools::fixImage($value->image?$value->image:$info_no_pic),
						'wylx'=>$wyw,
						'zd_company'=>$companydes,
						'pay'=>$value->first_pay,
						'sort'=>$value->sort,
						'distance'=>round($this->getDistance($value),2),
					];
				}
				$pager = $plots->pagination;
				$this->frame['data'] = ['list'=>$lists,'page'=>$page,'num'=>$pager->itemCount,'page_count'=>$pager->pageCount,];
			}
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

	public function actionInfo($id='',$phone='')
	{
		if($id && strstr($id,'_')) {
			list($id,$phone) = explode('_', $id);
		}
		if(!$id || !($info = PlotExt::model()->findByPk($id))) {
			return $this->returnError('参数错误');
		}
		// $info->views += 1;
		// $info->save();
		Yii::app()->redis->getClient()->hIncrBy('plot_views',$info->id,1);
		$info_no_pic = ImageTools::fixImage(SiteExt::getAttr('qjpz','info_no_pic'));
		$images = $info->images;
		if($images) {
			foreach ($images as $key => $value) {
				$images[$key]['type'] = Yii::app()->params['imageTag'][$value['type']];
				$value['url'] && $images[$key]['url'] = ImageTools::fixImage($value['url']);
			}
		}
		$fm = ['id'=>0,'type'=>'封面图','url'=>ImageTools::fixImage($info->image)];
		array_unshift($images, $fm);

		if($area = $info->areaInfo)
			$areaName = $area->name;
		else
			$areaName = '';
		if($street = $info->streetInfo)
			$streetName = $street->name;
		else
			$streetName = '';
		// if($companydes = $info->getItsCompany()) {
		// 	// var_dump($companydes);exit;
		// 	$companyArr = [];
		// 	foreach ($companydes as $key => $value) {
		// 		$value && $companyArr[] = $value['name'];
		// 	}
		// } else {
		// 	$companyArr = [];
		// }
		if(Yii::app()->user->getIsGuest()) {
			$pay = [];
		} elseif($pays = $info->pays) {
			$pay[] = ['title'=>$pays[0]['name'],'content'=>$pays[0]['content'],'num'=>count($pays)];
		} else {
			$pay = [];
		}
		
		if($news = $info->news) {
			$news_time = date('Y-m-d H:i:s',$news[0]['updated']);
			$news = $news[0]['content'];
		} else {
			$news_time = $news = '';
		}
		$hxarr = [];
		if($hxs = $info->hxs) {
			foreach ($hxs as $key => $value) {
				$tmp = $value->attributes;
				$tmp['image'] = $tmp['image']?ImageTools::fixImage($tmp['image']):$info_no_pic;
				$hxarr[] = $tmp;
			}
		}
		$phones = array_filter(explode(' ', $info->market_users));
		$info->market_user && array_unshift($phones, $info->market_user);

		$phones = array_keys(array_flip($phones));

		$phonesnum = [];
		if($phones) {
			foreach ($phones as $key => $value) {
				preg_match('/[0-9]+/', $value,$tmp);
				$phonesnum = array_merge($phonesnum,$tmp);
			}
		}
		$major_phone = '';
		if($info->market_user) {
			preg_match('/[0-9]+/', $info->market_user,$major_phone);
			$major_phone = $major_phone[0];
		}

		// $companys = $info->getItsCompany();
		$is_show_add = 0;
		$cids = [];
		// var_dump($companys);exit;
		// $share_phone = '';
		if(!Yii::app()->user->getIsGuest()) {
			// if($companys) {
			// 	foreach ($companys as $key => $value) {
			// 		$cids[] = $value['id'];
			// 	}
			// }
			// var_dump($companys,$cids);exit;
			// if($companys && in_array($this->staff->cid, $cids)) {
			// 	$is_show_add = 1;
			// }
			if($this->staff->cid==$info->company_id) {
				$is_show_add = 1;
			}
			// if(in_array($this->staff->phone, $phonesnum)) {
			// 	$share_phone = $phone;
			// }
		}
		// var_dump($phone ,$phones);exit;
		$is_contact_only = 0;
		// 分享出去 总代或者分销加电话咨询，否则提示下载
		if($phone && $phones) {
			foreach ($phones as $key => $value) {
				if(strstr($value,$phone)) {
					$is_contact_only = 1;
					$phone = $value;
					break;
				}
			}
			!$is_contact_only && $is_contact_only = 2;
		}
		if(!is_array($info->wylx)) 
			$info->wylx = [$info->wylx];
		if(!is_array($info->zxzt)) 
			$info->zxzt = [$info->zxzt];
		$tags = array_filter(array_merge($info->wylx,$info->zxzt));
		// var_dump($info->wylx,$info->zxzt);exit;
		$tagName = [];
		if($tags) {
			foreach ($tags as $key => $value) {
				$tagName[] = TagExt::model()->findByPk($value)->name;
			}
		}
		$info->dllx && array_unshift($tagName, Yii::app()->params['dllx'][$info->dllx]);
		$data = [
			'id'=>$id,
			'title'=>$info->title,
			'area'=>$areaName,	
			'street'=>$streetName,
			'address'=>Tools::u8_title_substr($areaName.$streetName.$info->address,36),
			'price'=>$info->price,
			'unit'=>PlotExt::$unit[$info->unit],
			'map_lat'=>$info->map_lat?$info->map_lat:SiteExt::getAttr('qjpz','map_lat'),
			'map_lng'=>$info->map_lng?$info->map_lng:SiteExt::getAttr('qjpz','map_lng'),
			'map_zoom'=>$info->map_zoom?$info->map_zoom:SiteExt::getAttr('qjpz','map_zoom'),
			'pay'=>$pay,
			'news'=>$news,
			'news_time'=>$news_time,
			'sell_point'=>$info->peripheral.$info->surround_peripheral,
			'hx'=>$hxarr,
			'phones'=>$phone?[$phone]:($this->staff?$phones:[]),
			'phone'=>$phone?$phone:($this->staff?$major_phone:''),
			'images'=>$images,
			'dk_rule'=>$info->dk_rule,
			'is_login'=>$this->staff?'1':'0',
			'wx_share_title'=>$info->wx_share_title?$info->wx_share_title:$info->title,
			'is_show_add'=>$is_show_add,
			'phonesnum'=>$phonesnum,
			'zd_company'=>['id'=>$info->company_id,'name'=>$info->company_name],
			'tags'=>$tagName,
			'is_contact_only'=>$is_contact_only,
			'mzsm'=>SiteExt::getAttr('qjpz','mzsm'),
			'areaid'=>$info->area,
			// 'share_phone'=>$share_phone,
		];
		
		$data['can_edit'] = $this->staff && strstr($info->market_user,$this->staff->phone)?1:0;
		$this->frame['data'] = $data;
	}

	public function actionMoreInfo($id='')
	{
		if(!$id || !($info = PlotExt::model()->findByPk($id))) {
			return $this->returnError('参数错误');
		}
		$fields = [
			'open_time','is_new','delivery_time','developer','brand','manage_company','sale_tel','size','capacity','green','household_num','carport','price','manage_fee','property_years','dk_rule'
		];
		$data = [];
		foreach ($fields as $key => $value) {
			$data[$value] = $info->$value;
		}
		$jzlb = [];
		if($jzlbs = $info->jzlb) {
			if(!is_array($jzlbs))
				$jzlbs = [$jzlbs];
			foreach ($jzlbs as $key => $value) {
				$tmp = TagExt::model()->findByPk($value);
				$tmp && $jzlb[] = $tmp->name;
			}
		}
		$zxzt = [];
		if($zxzts = $info->zxzt) {
			if(!is_array($zxzts))
				$zxzts = [$zxzts];
			foreach ($zxzts as $key => $value) {
				$tmp = TagExt::model()->findByPk($value);
				$tmp && $zxzt[] = $tmp->name;
			}
		}
		$data['open_time'] && $data['open_time'] = date('Y-m-d',$data['open_time']);
		if($data['delivery_time'] && $data['delivery_time']>time()) {
			$data['delivery_time'] = date('Y-m-d',$data['delivery_time']);
		} else {
			$data['delivery_time'] = '现房';
		}
		$data['zxzt'] = $zxzt;
		$data['jzlb'] = $jzlb;
		$this->frame['data'] = $data;
	}

	public function actionPlotNews($id='')
	{
		if(!$id || !($info = PlotExt::model()->findByPk($id))) {
			return $this->returnError('参数错误');
		}
		if($news = $info->news) {
			foreach ($news as $key => $value) {
				$news[$key]['updated'] = date('Y-m-d',$value['updated']);
			}
		}
		$this->frame['data'] = $news;
	}

	public function actionPlotPays($id='')
	{
		if(!$id || !($info = PlotExt::model()->findByPk($id))) {
			return $this->returnError('参数错误');
		}

		$this->frame['data'] = ['list'=>$info->pays,'jy_rule'=>$info->jy_rule,'kfs_rule'=>$info->kfs_rule];
	}

	public function actionAjaxSearch($kw='')
	{
		$data = [];
		if($kw) {
			$criteria = new CDbCriteria;
			if(preg_match ("/^[a-z]/i", $kw) ) {
				// var_dump(1);exit;
				$criteria->addSearchCondition('pinyin',$kw);
			}
			else
				$criteria->addSearchCondition('title',$kw);
			$res = PlotExt::model()->normal()->findAll($criteria);
			if($res) {
				foreach ($res as $key => $value) {
					$data[] = ['id'=>$value->id,'title'=>$value->title,'area'=>$value->areaInfo->name,'street'=>$value->streetInfo->name];
				}
			}
			$this->frame['data'] = $data;
		}
	}

	public function actionSetCoo()
	{
		if(Yii::app()->request->getIsPostRequest()){
			$house_lng = $_POST['lng'];
			$house_lat = $_POST['lat'];
			// var_dump($house_lat);exit;
			setCookie('house_lng',$house_lng);
			setCookie('house_lat',$house_lat);
		}
	}

	public function actionGetHasCoo()
	{
		if(empty($_COOKIE['house_lng'])) {
			$this->returnError('无');
		} else {
			$this->returnSuccess('有');
		}
	}

	public function actionSubmit()
	{
		if(Yii::app()->request->getIsPostRequest()){
			if(!Yii::app()->user->getIsGuest()) {
				$hid = $_POST['hid'];
				$content = $_POST['content'];
				$user = $this->staff;
				$model = $_POST['model'];
				if($model == 'PlotExt') {
					$obj = PlotExt::model()->findByPk($hid);
				} else {
					$obj = new $model;
					$obj->hid = $hid;
				}
				if(isset($obj->author) && isset($user->name)) {
					$obj->author = $user->name;
				}
				if($model == 'PlotExt') {
					$obj->dk_rule = $content;
				} else {
					$obj->content = $content;
				}
				// var_dump($obj->attributes);exit;
				if(!$obj->save())
					$this->returnError(current(current($obj->getErrors())));
			}
		}
	}

	public function actionSearch()
	{
		$kw=$this->cleanXss($_POST['kw']);
		if($kw) {
			$kwarr = [];
			if(empty($_COOKIE['search_kw'])) {
				$kwarr[] = $kw;
			} else {
				$kwarr = json_decode($_COOKIE['search_kw'],true);
				array_unshift($kwarr, $kw);
				$kwarr = array_slice(array_unique($kwarr), 0,5);
			}
			setcookie('search_kw',json_encode($kwarr));
			$this->redirect('/subwap/list.html?kw='.$kw);
		}
	}

	public function actionGetSearchCoo()
	{
		if(empty($_COOKIE['search_kw'])) {
			$this->frame['data'] = [];
		} else
			$this->frame['data'] = json_decode($_COOKIE['search_kw'],true);
	}

	public function actionDelSearchCoo()
	{
		setcookie('search_kw','');
	}

	public function actionAddMakert()
	{
		if(!Yii::app()->user->getIsGuest() && Yii::app()->request->getIsPostRequest()) {
			if($hid = $this->cleanXss($_POST['hid'])) {
				$title = $this->cleanXss($_POST['title']);
				$num = $this->cleanXss($_POST['num']);
				if(strstr($title, '1')) {
					$time = 30*86400;
				} elseif (strstr($title, '3')) {
					$time = 30*86400*3;
				}
				$uid = $this->staff->id;
				// var_dump($uid,$hid);exit;
				if(!Yii::app()->db->createCommand("select id from plot_makert_user where uid=$uid and hid=$hid and deleted=0 and expire>".time())->queryRow()) {
					$obj = new PlotMarketUserExt;
					$obj->status = 1;
					$obj->uid = $uid;
					$obj->hid = $hid;
					if($obj->expire<time()) {
						$obj->expire = time()+$time*$num;
					} else {
						$obj->expire = $obj->expire+$time*$num;
					}
					
					if(!$obj->save())
						$this->returnError(current(current($obj->getErrors())));
				} else {
					$this->returnError('您已经提交申请，请勿重复提交');
				}
			}
		} else{
			$this->returnError('操作失败');
		}
	}

	public function actionAddSub()
	{
		if(!Yii::app()->user->getIsGuest() && Yii::app()->request->getIsPostRequest()) {
			if(($tmp['hid'] = $this->cleanXss($_POST['hid'])) && ($plot = PlotExt::model()->findByPk($_POST['hid'])) && ($tmp['phone'] = $this->cleanXss($_POST['phone']))) {
				$tmp['name'] = $this->cleanXss($_POST['name']);
				$tmp['time'] = strtotime($this->cleanXss($_POST['time']));
				$tmp['sex'] = $this->cleanXss($_POST['sex']);
				$tmp['note'] = $this->cleanXss(Yii::app()->request->getPost('note',''));
				$tmp['visit_way'] = $this->cleanXss($_POST['visit_way']);
				$tmp['is_only_sub'] = $this->cleanXss($_POST['is_only_sub']);
				$notice = $this->cleanXss($_POST['notice']);
				$tmp['uid'] = $this->staff->id;

				if($this->staff->type<=1) {
					return $this->returnError('您的账户类型为总代公司，不支持快速报备');
				} 

				if(Yii::app()->db->createCommand("select id from sub where uid=".$tmp['uid']." and hid=".$tmp['hid']." and deleted=0 and phone='".$tmp['phone']."' and created<=".TimeTools::getDayEndTime()." and created>=".TimeTools::getDayBeginTime())->queryScalar()) {
					return $this->returnError("同一组客户每天最多报备一次，请勿重复操作");
				}
				$obj = new SubExt;
				$obj->attributes = $tmp;
				$obj->status = 0;
				// 新增6位客户码 不重复
				$code = 700000+rand(0,99999);
				// var_dump($code);exit;
				while (SubExt::model()->find('code='.$code)) {
					$obj->code = $code = 700000+rand(0,99999);
				}
				if($obj->save()) {
					$pro = new SubProExt;
					$pro->sid = $obj->id;
					$pro->uid = $this->staff->id;
					$pro->note = '新增客户报备';
					$pro->save();
					SmsExt::sendMsg('客户通知',$this->staff->phone,['pro'=>$plot->title,'pho'=>substr($tmp['phone'], -4,4),'code'=>$code]);

					$stphones = explode(' ',SiteExt::getAttr('qjpz','bussiness_tel'));
					if($notice) {
						$stphones[] = $notice;
					}
					foreach ($stphones as $key => $value) {
								// $note = '【经纪人】'.$this->staff->name.'('.$this->staff->phone.')快速报备【客户】'.$tmp['name'].'('.$tmp['phone'].'),楼盘为'.$plot->title;
								// var_dump($note);exit;
						SmsExt::sendMsg('报备',$value,['staff'=>($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,'user'=>$tmp['name'].$tmp['phone'],'time'=>$_POST['time'],'project'=>$plot->title,'type'=>($obj->visit_way==1?'自驾':'班车').($obj->is_only_sub==1?'仅报备':'')]);
						// Yii::app()->mns->run((string)$value,$tmp['phone'].'新增报备');
					}
					// if($this->staff->type==3) {
					// 	if($stphones = explode(' ',SiteExt::getAttr('qjpz','bussiness_tel'))) {
					// 		foreach ($stphones as $key => $value) {
					// 			// $note = '【经纪人】'.$this->staff->name.'('.$this->staff->phone.')快速报备【客户】'.$tmp['name'].'('.$tmp['phone'].'),楼盘为'.$plot->title;
					// 			// var_dump($note);exit;
					// 			SmsExt::sendMsg('报备',$value,['staff'=>($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,'user'=>$tmp['name'].$tmp['phone'],'time'=>$_POST['time'],'project'=>$plot->title,'type'=>($obj->visit_way==1?'自驾':'班车').($obj->is_only_sub==1?'仅报备':'')]);
					// 			// Yii::app()->mns->run((string)$value,$tmp['phone'].'新增报备');
					// 		}
					// 	}
					// } elseif ($this->staff->type==2) {
					// 	if($plot->market_user) {
					// 		preg_match('/[0-9]+/', $plot->market_user,$phone);
					// 		SmsExt::sendMsg('报备',$phone,['staff'=>($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,'user'=>$tmp['name'].$tmp['phone'],'time'=>$_POST['time'],'project'=>$plot->title,'type'=>($obj->visit_way==1?'自驾':'班车').($obj->is_only_sub==1?'仅报备':'')]);
					// 	}
						
					// }
						
					
				} else {
					$this->returnError(current(current($obj->getErrors())));
				}
				// }
			}
		} else {
			$this->returnError('操作失败');
		}
	}

	public function actionAddCo()
	{
		if(!Yii::app()->user->getIsGuest() && Yii::app()->request->getIsPostRequest()) {
			if($tmp['hid'] = $this->cleanXss($_POST['hid'])) {
				$plot = PlotExt::model()->findByPk($tmp['hid']);
				$tmp['com_phone'] = $this->cleanXss($_POST['com_phone']);
				$tmp['uid'] = $this->staff->id;
// var_dump($plot);exit;
				if($this->staff->type>1 && $plot && !Yii::app()->db->createCommand("select id from cooperate where deleted=0 and uid=".$tmp['uid']." and hid=".$tmp['hid'])->queryScalar()) {
					if($this->staff->cid) {
						$company = Yii::app()->db->createCommand('select name from company where id='.$this->staff->cid)->queryScalar();
					} else {
						$company = '';
					}
					
					$obj = new CooperateExt;
					$obj->attributes = $tmp;
					$obj->status = 0;
					if($obj->save()) {
						SmsExt::sendMsg('分销',$tmp['com_phone'],['staff'=>$company.$this->staff->name.$this->staff->phone,'plot'=>$plot->title]);
					}
				} elseif($this->staff->type<=1) {
					$this->returnError('您的账户类型为总代公司，不支持申请分销签约');
				} else {
					$this->returnError('您已经提交申请，请勿重复提交');
				}
			}
		}
	}
	public function actionDo()
    {
    	// var_dump(Yii::app()->msg);exit;
        // var_dump(SmsExt::addOne('13861242596','1111'));
        // $infos = PlotExt::model()->normal()->findAll();
    // foreach ($infos as $key => $value) {
        //     if(!$value->first_pay && $value->pays) {
        //         $value->first_pay = $value->pays[0]['price'];
        //     }
        //     $value->save();
        // }
        // echo "ok";
        // phpinfo();
        var_dump(Yii::app()->redis->getClient()->hGetAll('test'));
        // Yii::app()->redis->getClient()->hSet('test','id','222');
        exit;
    }
    public function actionSubCompany()
    {
    	if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('CompanyExt',[]);
			$obj = new CompanyExt;
			$obj->attributes = $values;
			$obj->status = 0;
			$obj->save();
		}
    }
    public function actionGetPhones($hid='')
    {
    	if($hid) {
    		$info = PlotExt::model()->findByPk($hid);
    		if($info) {
    			$phones = $tmp = [];
    			if($info->market_users) {
    				$phones = explode(' ', $info->market_users);
    			}
    			if($info->market_user) {
    				array_unshift($phones, $info->market_user);
    			}
    			$phones = array_flip(array_flip($phones));
    			if($phones) {
    				foreach ($phones as $key => $value) {
    					preg_match('/[0-9]+/', $value,$k);
    					// var_dump($value,$k);exit;
    					$tmp[] = ['key'=>$k[0],'value'=>$value];
    				}
    			}
    			$this->frame['data'] = $tmp;
    		}
    	}
    }

    public function actionCheckMarket($hid='')
    {
    	$uid = !Yii::app()->user->getIsGuest()?$this->staff->id:0;
    	if(!$uid || !$hid) {
    		return $this->returnError('参数错误');;
    	}
    	if(Yii::app()->db->createCommand("select id from plot_makert_user where uid=$uid and hid=$hid and deleted=0")->queryRow()) {
			$this->returnError('您已经提交申请，请勿重复提交');
		} else {
			$this->returnSuccess('bingo');
		}
    }

    public function actionAddReport()
    {
    	if(!Yii::app()->user->getIsGuest() && Yii::app()->request->getIsPostRequest()) {
			if($tmp['hid'] = $this->cleanXss($_POST['hid'])) {
				$plot = PlotExt::model()->findByPk($tmp['hid']);
				if(!$plot) {
					return $this->returnError('操作失败');
				}
				$tmp['reason'] = $this->cleanXss($_POST['reason']);
				$tmp['uid'] = $this->staff->id;
// var_dump($plot);exit;
				if(!Yii::app()->db->createCommand("select id from report where deleted=0 and uid=".$tmp['uid']." and hid=".$tmp['hid'])->queryScalar()) {
					
					$obj = new ReportExt;
					$obj->attributes = $tmp;
					$obj->status = 0;
					if($obj->save()) {
						return $this->returnSuccess('操作成功');
					}
				} else {
					return $this->returnError('您已经提交申请，请勿重复提交');
				}
			}
		}
		return $this->returnError('操作失败');
    }

    public function actionCheckIsZc()
    {
    	if(Yii::app()->user->getIsGuest() || !($plot = PlotExt::model()->normal()->find('place_user='.$this->staff->id))) {
    		$this->returnError('暂无权限查看');
    	} else {
    		$subs = $plot->subs;
    		$data = [];
    		if($subs) {
    			foreach ($subs as $key => $value) {
    				$itsstaff = $value->user;
    				$tmp['user_name'] = $value->name;
    				$tmp['user_phone'] = $value->phone;
    				$tmp['staff_name'] = $itsstaff->name;
    				$tmp['staff_phone'] = $itsstaff->phone;
    				$tmp['staff_company'] = Yii::app()->db->createCommand("select name from company where id=".$itsstaff->cid)->queryScalar();
    				$data[] = $tmp;
    			}
    		}
    		$this->frame['data'] = $data;
    	}
    }
}