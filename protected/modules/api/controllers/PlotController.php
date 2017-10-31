<?php
class PlotController extends ApiController{
	public function actionList()
	{

		$info_no_pic = SiteExt::getAttr('qjpz','info_no_pic');
		$areaslist = AreaExt::getALl();
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
		$uid = (int)Yii::app()->request->getQuery('uid',0);
		$status = Yii::app()->request->getQuery('status','');
		$minprice = (int)Yii::app()->request->getQuery('minprice',0);
		$maxprice = (int)Yii::app()->request->getQuery('maxprice',0);
		$page = (int)Yii::app()->request->getQuery('page',1);
		$save = (int)Yii::app()->request->getQuery('save',0);
		$kw = $this->cleanXss(Yii::app()->request->getQuery('kw',''));
		$this->frame['data'] = ['list'=>[],'page'=>$page,'num'=>0,'page_count'=>0,];
		$init = $areainit = 0 ;
		if($area+$street+$aveprice+$sfprice+$sort+$wylx+$zxzt+$toptag+$company+$save+$maxprice+$minprice==0&&$page==1&&!$kw) {
			$init = 1;
		}
		if($area&&$street+$aveprice+$sfprice+$sort+$wylx+$zxzt+$toptag+$company+$save+$maxprice+$minprice==0&&$page==1&&!$kw) {
			$areainit = 1;
		}
		$criteria = new CDbCriteria;
		if($uid>0) {
			if($this->staff && $this->staff->type==1 && $this->staff->companyinfo) {
				$init = 0;
				$criteria->addCondition('uid=:uid');
				$criteria->params[':uid'] = $this->staff->id;
				if(is_numeric($status)) {
					$criteria->addCondition('status=:status');
					$criteria->params[':status'] = $status;
				}
			} else {
				return $this->returnError('用户类型错误，只支持总代公司发布房源');
			}
			
		} else {
			$criteria->addCondition('status=1');
		}
		if($save>0&&$this->staff) {
			$savehidsarr = [];
			$savehids = Yii::app()->db->createCommand("select hid from save where uid=".$this->staff->id)->queryAll();
			if($savehids) {
				foreach ($savehids as $savehid) {
					$savehidsarr[] = $savehid['hid'];
				}
			}
			$criteria->addInCondition('id',$savehidsarr);
		}
		if($kw) {
			// $criteria1 = new CDbCriteria;
			// $criteria1->addSearchCondition('name',$kw);
			// $compas = CompanyExt::model()->normal()->find($criteria1);
			// // var_dump($compas);exit;
			// // $compas && $company = $compas['id'];
			// if($compas) {
			// 	$company = $compas['id'];
			// }else
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

		if($minprice) {
			$criteria->addCondition('price>=:minprice');
			$criteria->params[':minprice'] = $minprice;
		}

		if($maxprice) {
			$criteria->addCondition('price<=:maxprice');
			$criteria->params[':maxprice'] = $maxprice;
		}

		$ids = $companyids = [];
		// var_dump($toptag,$sfprice,$wylx);exit;
		foreach (['sfprice','wylx','toptag','zxzt'] as $key => $value) {
			if($$value) {
				$idarr = Yii::app()->db->createCommand("select hid from plot_tag where tid=".$$value)->queryAll();
				// var_dump($idarr);exit;
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
		if($sfprice>0||$wylx>0||$toptag>0||$zxzt>0) {
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
				case '4':
					$criteria->order = 'created desc';
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
			$criteria->order = 'updated desc';
		}
		if($areainit) {
			$dats = PlotExt::getFirstListFromArea();
			if(isset($dats[$area])&& isset($dats[$area]['list']) && $dats[$area]['list']) {
				foreach ($dats[$area]['list'] as $key => $value) {
					// var_dump($value);exit;
					$dats[$area]['list'][$key]['distance'] = round($this->getDistance($value['distance']),2);
				}
			}
			$this->frame['data'] = $dats[$area];
		}
		// 走缓存拿初始数据
		elseif($init) {
			$dats = PlotExt::setPlotCache();
			if(isset($dats['list']) && $dats['list']) {
				foreach ($dats['list'] as $key => $value) {
					// var_dump($value);exit;
					$dats['list'][$key]['distance'] = round($this->getDistance($value['distance']),2);
				}
			}
			$this->frame['data'] = $dats;
		} else {
			$plots = PlotExt::model()->undeleted()->getList($criteria,$limit);
			$lists = [];
			// if($company) {
			// 	$companydes = Yii::app()->db->createCommand("select id,name from company where id=$company")->queryRow();
			// }
			if($datares = $plots->data) {
				foreach ($datares as $key => $value) {
					if(isset($areaslist[$value->area]))
						$areaName = $areaslist[$value->area];
					else
						$areaName = '';
					if(isset($areaslist[$value->street]))
						$streetName = $areaslist[$value->street];
					else
						$streetName = '';
					// if(!$company) {
					$companydes = ['id'=>$value->company_id,'name'=>$value->company_name];
					// }
					$wyw = '';
					$wylx1 = $value->wylx;
					if($wylx1) {
						if(!is_array($wylx1)) 
							$wylx1 = [$wylx1];
						foreach ($wylx1 as $w) {
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
					$expire = '您尚未成为对接人';
					// // var_dump($uid);exit;
					if($uid) {
						$expiret = Yii::app()->db->createCommand('select expire from plot_makert_user where uid='.$this->staff->id.' and hid='.$value->id)->queryScalar();
						if(!$expiret) {
							$expire = '等待付款';
						}elseif($expiret>0 && $expiret<time()) {
							$expire = '已到期';
						} elseif($expiret>0) {
							if($value->status) {
								$expire = '已上线';
							} else {
								$expire = '等待审核';
							}
						}
					}
					$lists[] = [
						'id'=>$value->id,
						'title'=>Tools::u8_title_substr($value->title,18),
						'price'=>$value->price,
						'unit'=>PlotExt::$unit[$value->unit],
						'area'=>$areaName,
						'street'=>$streetName,
						'image'=>ImageTools::fixImage($value->image?$value->image:$info_no_pic,220,164),
						'wylx'=>$wyw,
						// 'status'=>$value->status?'已上线':'审核中',
						'zd_company'=>$companydes,
						'pay'=>$value->first_pay,
						'sort'=>$value->sort,
						'expire'=>$expire,
						'distance'=>round($this->getDistance($value),2),
					];
				}
				$pager = $plots->pagination;
				$this->frame['data'] = ['list'=>$lists,'page'=>$page,'num'=>$pager->itemCount,'page_count'=>$pager->pageCount,];
			}
		}
		if($area+$street+$aveprice+$sfprice+$wylx+$zxzt+$toptag+$company+$uid+$save==0&&!$kw) {
			$this->frame['data']['num'] += 800;
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
				is_numeric($value['type']) && $images[$key]['type'] = Yii::app()->params['imageTag'][$value['type']];
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
		if($pays = $info->pays) {
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
		$hxarr = $phones = [];
		if($hxs = $info->hxs) {
			foreach ($hxs as $key => $value) {
				$tmp = $value->attributes;
				$tmp['image'] = $tmp['image']?ImageTools::fixImage($tmp['image']):$info_no_pic;
				$hxarr[] = $tmp;
			}
		}
		if($sfs = $info->sfMarkets) {
			foreach ($sfs as $key => $value) {
				$thisstaff = UserExt::model()->findByPk($value->uid);
				$thisstaff && $phones[] = $thisstaff->name.$thisstaff->phone;
			}
			// $phones = [];
		} else {
			$phones = array_filter(explode(' ', $info->market_users));
		}
		$info->market_user && array_unshift($phones, $info->market_user);

		$phones && $phones = array_keys(array_flip($phones));

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

		$cids = [];

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
		$ffphones=[];
		if($ffs = $info->sfMarkets) {
			foreach ($ffs as $key => $value) {
				$value->user&&$ffphones[] = $value->user->phone;
			}
		}
		$is_alert = 0;
		if($info->uid && !Yii::app()->db->createCommand("select id from plot_makert_user where uid=".$info->uid." and hid=".$info->id)->queryScalar()) {
			$is_alert = 1;
		}
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
			'phones'=>$phone?[$phone]:$phones,
			'phone'=>$phone?$phone:($this->staff?$major_phone:''),
			'images'=>$images,
			'dk_rule'=>$info->dk_rule,
			'is_login'=>$this->staff?'1':'0',
			'wx_share_title'=>$info->wx_share_title?$info->wx_share_title:$info->title,
			'phonesnum'=>$phonesnum,
			'zd_company'=>['id'=>$info->company_id,'name'=>$info->company_name],
			'tags'=>$tagName,
			'is_contact_only'=>$is_contact_only,
			'mzsm'=>SiteExt::getAttr('qjpz','mzsm'),
			'areaid'=>$info->area,
			'streetid'=>$info->street,
			'owner_phone'=>$info->owner?$info->owner->phone:'',
			'ff_phones'=>$ffphones,
			'is_alert'=>$is_alert,
			'is_save'=>$this->staff&&Yii::app()->db->createCommand('select id from save where uid='.$this->staff->id.' and hid='.$info->id)->queryScalar()?1:0,
			// 'share_phone'=>$share_phone,
		];
		if($this->staff) {
			if(($data['owner_phone']==$this->staff->phone&&Yii::app()->db->createCommand("select id from plot_makert_user where is_manager=1 and status=1 and deleted=0 and expire>".time()." and uid=".$this->staff->id." and hid=".$info->id)->queryScalar())||strstr($info->market_user,$this->staff->phone)) {
				$data['can_edit'] = 1;
			} else {
				$data['can_edit'] = 0;
			}
		}else {
				$data['can_edit'] = 0;
			}
		// $data['can_edit'] = $this->staff && strstr($info->market_user,$this->staff->phone)?1:0;
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
					$data[] = ['id'=>$value->id,'title'=>$value->title,'area'=>$value->areaInfo?$value->areaInfo->name:'','street'=>$value->streetInfo?$value->streetInfo->name:''];
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
				$plot = PlotExt::model()->findByPk($hid);
				$title = $this->cleanXss($_POST['title']);
				$num = $this->cleanXss($_POST['num']);
				if(strstr($title, '1')) {
					$time = 30*86400;
				} elseif (strstr($title, '3')) {
					$time = 30*86400*3;
				}
				$uid = $this->staff->id;
				// var_dump($uid,$hid);exit;
				$criteria = new CDbCriteria;
				$criteria->addCondition("uid=$uid and hid=$hid and deleted=0 and expire>".time());
				$obj = PlotMarketUserExt::model()->normal()->find($criteria);
				if(!$obj)
					$obj = new PlotMarketUserExt;
				// if(!Yii::app()->db->createCommand("select id from plot_makert_user where uid=$uid and hid=$hid and deleted=0 and expire>".time())->queryRow()) {
					// $obj = new PlotMarketUserExt;
					if($plot->uid&&$plot->uid==$uid) {
						$obj->is_manager = 1;
					}
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
				// } else {
				// 	$this->returnError('您已经提交申请，请勿重复提交');
				// }
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
				$tmp['notice'] = $notice = $this->cleanXss($_POST['notice']);
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
				if($tmp['uid']) {
					$companyname = Yii::app()->db->createCommand("select c.name from company c left join user u on u.cid=c.id where u.id=".$tmp['uid'])->queryScalar();
					$obj->company_name = $companyname;
				}
				// 新增6位客户码 不重复
				$code = 700000+rand(0,99999);
				// var_dump($code);exit;
				while (SubExt::model()->find('code='.$code)) {
					$code = 700000+rand(0,99999);
				}
				$obj->code = $code;
				if($obj->save()) {
					$pro = new SubProExt;
					$pro->sid = $obj->id;
					$pro->uid = $this->staff->id;
					$pro->note = '新增客户报备';
					$pro->save();
					SmsExt::sendMsg('客户通知',$this->staff->phone,['pro'=>$plot->title,'pho'=>substr($tmp['phone'], -4,4),'code'=>$code]);
					
					$this->staff->qf_uid && Yii::app()->controller->sendNotice('您好，你对'.$plot->title.'的报备已经成功，客户的尾号是'.substr($tmp['phone'], -4,4).'，客户码为'.$code.'，请牢记您的客户码。',$this->staff->qf_uid);

					if($notice) {
						$noticename = Yii::app()->db->createCommand("select name from user where phone='$notice'")->queryScalar();
						SmsExt::sendMsg('报备',$notice,['staff'=>($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,'user'=>$tmp['name'].$tmp['phone'],'time'=>$_POST['time'],'project'=>$plot->title,'type'=>($obj->visit_way==1?'自驾':'班车')]);

						$noticeuid = Yii::app()->db->createCommand("select qf_uid from user where phone='$notice'")->queryScalar();
						// $noticeuid && $this->staff->qf_uid && Yii::app()->controller->sendNotice('项目名称：'.$plot->title.'；客户：'.$tmp['name'].$tmp['phone'].'；来访时间：'.$_POST['time'].'；来访方式：'.($obj->visit_way==1?'自驾':'班车').'；业务员：'.($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,$noticeuid);
						$noticeuid && $this->staff->qf_uid && Yii::app()->controller->sendNotice(
							'报备项目：'.$plot->title.'
客户姓名：'.$tmp['name'].'
客户电话： '.$tmp['phone'].'
公司门店：'.($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').'
业务员姓名：'.$this->staff->name.'
业务员电话：'.$this->staff->phone.'
市场对接人：'.$noticename.'
对接人电话：'.$notice.'
带看时间：'.$_POST['time'].'
来访方式：'.($obj->visit_way==1?'自驾':'班车'),$noticeuid);

					}
						
					
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
						$noticeuid = Yii::app()->db->createCommand("select qf_uid from user where phone='".$tmp['com_phone']."'")->queryScalar();
						$noticeuid && Yii::app()->controller->sendNotice('分销合同签约申请：'.$company.$this->staff->name.$this->staff->phone.'，正在经纪圈APP中申请合作（'.$plot->title.'）项目，请尽快联系哦！',$noticeuid);
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
    	Yii::app()->cache->flush();  
        // var_dump(Yii::app()->controller->sendNotice('有新的独立经纪人注册，请登陆后台审核','',1));
        // Yii::app()->redis->getClient()->hSet('test','id','222');
        exit;
    }
    public function actionSubCompany()
    {
    	if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('CompanyExt',[]);
			if(CompanyExt::model()->undeleted()->find("name='".$values['name']."'")) {
				return $this->returnError('公司名已存在');
			}
			$area = $street = 0;
			if($values['area']) {
				$streetobj = AreaExt::model()->findByPk($values['area']);
				if($streetobj) {
					$area = $streetobj->parent;
					$street = $streetobj->id;
				}
			}
			$obj = new CompanyExt;
			$obj->attributes = $values;
			$obj->area = $area;
			$obj->street = $street;
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
    			if($sfs = $info->sfMarkets) {
					foreach ($sfs as $key => $value) {
						$thisstaff = UserExt::model()->findByPk($value->uid);
						$thisstaff && $phones[] = $thisstaff->name.$thisstaff->phone;
					}
					// $phones = [];
				} else {
					$phones = array_filter(explode(' ', $info->market_users));
				}
				$info->market_user && array_unshift($phones, $info->market_user);

				$phones && $phones = array_keys(array_flip($phones));

				$phonesnum = [];
				if($phones) {
					foreach ($phones as $k => $value) {
						preg_match('/[0-9]+/', $value,$k);
						$tmp[] = ['key'=>$k[0],'value'=>$value];
					}
				}
    			// if($info->market_users) {
    			// 	$phones = explode(' ', $info->market_users);
    			// }
    			// if($info->market_user) {
    			// 	array_unshift($phones, $info->market_user);
    			// }
    			// $phones = array_flip(array_flip($phones));
    			// if($phones) {
    			// 	foreach ($phones as $key => $value) {
    			// 		preg_match('/[0-9]+/', $value,$k);
    			// 		// var_dump($value,$k);exit;
    			// 		$tmp[] = ['key'=>$k[0],'value'=>$value];
    			// 	}
    			// }
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
    	if(Yii::app()->user->getIsGuest()) {
    		return $this->returnError('暂无权限查看');
    	} else {
    		$hid = Yii::app()->db->createCommand("select hid from plot_place where uid=".Yii::app()->user->id)->queryScalar();
    		if(!$hid) {
    			return $this->returnError('暂无权限查看');
    		} else {
    			$plot = PlotExt::model()->findByPk($hid);
    		}
    		// var_dump($hid);exit;
    		// $subs = $plot->subs;
    		$criteria = new CDbCriteria;
    		$criteria->addCondition('hid='.$hid);
    		$kw = Yii::app()->request->getQuery('kw','');
    		$status = Yii::app()->request->getQuery('status','');
    		if($kw) {
    			if(is_numeric($kw)) {
    				$criteria->addSearchCondition('phone',$kw);
    			} else {
    				$criteria->addSearchCondition('name',$kw);
    			}
    		}
    		if(is_numeric($status)) {
    			$criteria->addCondition('status=:status');
    			$criteria->params[':status'] = $status;
    		}
    		$criteria->order = 'created desc';
    		$subs = SubExt::model()->undeleted()->getList($criteria);

    		$data = $data['list'] = [];
    		if($subs->data) {

    			foreach ($subs->data as $key => $value) {
    				
    				$itsstaff = $value->user;
    				if(!$itsstaff) {
    					continue;
    				}
    				$cname = Yii::app()->db->createCommand("select name from company where id=".$itsstaff->cid)->queryScalar();
    				$tmp['id'] = $value->id;
    				$tmp['user_name'] = $value->name;
    				$tmp['user_phone'] = $value->phone;
    				$tmp['staff_name'] = $itsstaff->name;
    				$tmp['staff_phone'] = $itsstaff->phone;
    				$tmp['time'] = date('m-d H:i',$value->updated);
    				$tmp['status'] = SubExt::$status[$value->status];
    				$tmp['staff_company'] = $cname?$cname:'独立经纪人';
    				$data['list'][] = $tmp;
    			}
    		}
    		$data['num'] = $subs->pagination->itemCount;
    		$this->frame['data'] = $data;
    	}
    }
    public function actionCheckIsSale()
    {
    	if(Yii::app()->user->getIsGuest()) {
    		return $this->returnError('暂无权限查看');
    	} else {
    		$hid = Yii::app()->db->createCommand("select hid from plot_sale where uid=".Yii::app()->user->id)->queryScalar();
    		if(!$hid) {
    			return $this->returnError('暂无权限查看');
    		} else {
    			$plot = PlotExt::model()->findByPk($hid);
    		}
    		// var_dump($hid);exit;
    		// $subs = $plot->subs;
    		$criteria = new CDbCriteria;
    		$criteria->addCondition('hid='.$hid);
    		$criteria->addCondition('sale_uid='.$this->staff->id);
    		$kw = Yii::app()->request->getQuery('kw','');
    		$status = Yii::app()->request->getQuery('status','');
    		if($kw) {
    			if(is_numeric($kw)) {
    				$criteria->addSearchCondition('phone',$kw);
    			} else {
    				$criteria->addSearchCondition('name',$kw);
    			}
    		}
    		if(is_numeric($status)) {
    			$criteria->addCondition('status=:status');
    			$criteria->params[':status'] = $status;
    		}
    		$criteria->order = 'created desc';
    		$subs = SubExt::model()->undeleted()->getList($criteria);

    		$data = $data['list'] = [];
    		if($subs->data) {

    			foreach ($subs->data as $key => $value) {
    				
    				$itsstaff = $value->user;
    				$cname = Yii::app()->db->createCommand("select name from company where id=".$itsstaff->cid)->queryScalar();
    				$tmp['id'] = $value->id;
    				$tmp['user_name'] = $value->name;
    				$tmp['user_phone'] = $value->phone;
    				$tmp['staff_name'] = $itsstaff->name;
    				$tmp['staff_phone'] = $itsstaff->phone;
    				$tmp['time'] = date('m-d H:i',$value->updated);
    				$tmp['status'] = SubProExt::$status[$value->status];
    				$tmp['staff_company'] = $cname?$cname:'独立经纪人';
    				$data['list'][] = $tmp;
    			}
    		}
    		$data['num'] = $subs->pagination->itemCount;
    		$this->frame['data'] = $data;
    	}
    }

    public function actionCheckSub($code='',$acxs='')
    {
    	if(!$code) {
    		return $this->returnError('客户码不能为空');
    	}
    	$hid = Yii::app()->db->createCommand("select hid from plot_place where uid=".Yii::app()->user->id)->queryScalar();
    	$hisplot = PlotExt::model()->normal()->findByPk($hid);

    	if($hisplot) {
    		$obj = SubExt::model()->undeleted()->find("is_check=0 and code='$code' and hid=".$hisplot->id);

    		if($acxs) {
    			$obj->sale_uid = $acxs;
    		}
    		if(!$obj)
    			$this->returnError('客户码错误或已添加');
    		else {
    			$obj->is_check = 1;
    			$obj->status = 1;
    			$pro = new SubProExt;
    			$pro->note = '客户已到访';
    			$pro->sid = $obj->id;
    			$pro->status = 1;
    			$pro->uid = $this->staff->id;
    			$pro->save();
    			$obj->save();
    			$this->frame['data'] = $obj->id;
    			if($acxs && $sale = UserExt::model()->findByPk($acxs)) {
    				$sale->qf_uid && $this->sendNotice('您好，'.$hisplot->title.'有新的客户来访，请登录案场销售后台查看。',$sale->qf_uid);
    			}
    		}
    	} else {
    		$this->returnError('项目不存在');
    	}
    	
    }

    public function actionGetSubInfo($id='')
    {
    	if(!$id || (!$sub = SubExt::model()->findByPk($id))) {
    		return $this->returnError('参数错误');
    	}
    	$pros = [];
    	if($ls = $sub->pros) {
    		foreach ($ls as $key => $value) {
    			$pros[] = ['note'=>$value->note,'status'=>SubProExt::$status[$value->status],'time'=>date('m-d H:i',$value->created)];
    		}
    	}
    	$sale_user = $sub->sale_user;
    	$data = [
    		'name'=>$sub->name,
    		'phone'=>$sub->phone,
    		'dk_time'=>date('Y-m-d H:i:s',$sub->time),
    		'plot_name'=>$sub->plot->title,
    		'zj_name'=>$sub->user->name,
    		'zj_phone'=>$sub->user->phone,
    		'company'=>$sub->user->companyinfo?$sub->user->companyinfo->name:'暂无',
    		'note'=>$sub->note,
    		'status'=>SubExt::$status[$sub->status],
    		'is_del'=>SubExt::$status[$sub->status]=='失效'?1:0,
    		'list'=>$pros,
    		'can_edit'=>$sub->is_check,
    		'sale'=>$sale_user?($sale_user->name.$sale_user->phone):'',
    	];
    	$this->frame['data'] = $data;
    }

    public function actionAddSubPro()
    {
    	// var_dump($_POST);exit;
    	if(Yii::app()->request->getIsPostRequest() && !Yii::app()->user->getIsGuest()) {
    		$note = $this->cleanXss(Yii::app()->request->getPost('note',''));
    		$status = $this->cleanXss(Yii::app()->request->getPost('status',''));
    		$sid = $this->cleanXss(Yii::app()->request->getPost('sid',''));  
    		$sub = SubExt::model()->findByPk($sid);
    		// 防止离职后操作
    		if($this->staff->cid!=$sub->plot->company_id) {
    			return $this->returnError('您已从该公司离职，暂无权限操作');
    		}
    		if($sub && $status) {
    			if($status!=7) {
    				$sub->status = $status;
	    			$sub->save();
    			}
    			$obj = new SubProExt;
    			$obj->note = $note;
    			$obj->sid = $sid;
    			$obj->status = $status;
    			$obj->uid = $this->staff->id;
    			if(!$obj->save()){
    				return $this->returnError('操作失败');
    			}
    		}
    	}
    }
    /**
     * 新项目 会员免费发布 其余免费发布一条
     */
    public function actionAddPlot()
    {
    	if(Yii::app()->request->getIsPostRequest()) {
    		if($this->staff && $this->staff->type!=1) {
    			return $this->returnError('用户类型错误，只支持总代公司发布房源');
    		}
    		// if(!($company = $this->staff->companyinfo)) {
    		// 	return $this->returnError('尚未绑定公司');
    		// }
    		$post = $_POST;
    		// if($post&&is_array($post) ){
    		// 	foreach ($post as $key => $value) {
    		// 		$post[$key] = $this->cleanXss($value);
    		// 	}
    		// }
    		// $mak = $post['market_name'].$post['market_phone'];
    		// unset($post['market_name']);
    		// unset($post['market_phone']);
    		$img = '';
    		$imgs = $post['image'];
    		unset($post['image']);
    		if(isset($post['fm']) && $post['fm']) {
    			$img = $post['fm'];
    		} else {
    			$img = $imgs[0];
    		}
    		unset($post['fm']);
    		if($comname = $post['pcompany']) {
    			$criteria = new CDbCriteria;
    			$criteria->addSearchCondition('name',$comname);
    			$company = CompanyExt::model()->find($criteria);
    			if(!$company) {
    				$company = new CompanyExt;
    				$company->name = $comname;
    				$company->phone = $post['pphone'];
    				$company->status = 0;
    				$company->save();
    			}
    		}
    		unset($post['pcompany']);
    		$pphone = $post['pphone'];
    		if(!($user = UserExt::model()->find("phone='$pphone'"))) {
    			$user = new UserExt;
    			$user->name = $post['pname'];
    			$user->type = 1;
    			$user->cid = $company->id;
    			$user->phone = $pphone;
    			$user->status = 0;
    			$user->save();
    		}
    		$obj = new PlotExt;
    		$obj->attributes = $post;
    		$obj->pinyin = Pinyin::get($obj->title);
    		$obj->fcode = substr($obj->pinyin, 0,1);
    		$obj->status = 0;
    		$obj->image = $img;
    		// $obj->market_user = $mak;
    		$obj->uid = $user->id;
    		// $company = $this->staff->companyinfo;
    		$obj->company_id = $company->id;
    		$obj->company_name = $company->name;
    		// var_dump($obj->attributes);exit;
    		if(!$obj->save()) {
    			return $this->returnError(current(current($obj->getErrors())));
    		} else {
    			// 对接人
    			$mak = new PlotMarketUserExt;
    			$mak->uid = $user->id;
    			$mak->hid = $obj->id;
    			$mak->is_manager = 1;
    			$mak->status = 1;
    			$mak->expire = $user->vip_expire>time()?$user->vip_expire:30*86400;
    			$mak->save();
    			if($imgs && count($imgs)>1) {
    				unset($imgs[0]);
    				foreach ($imgs as $k) {
    					$im = new PlotImageExt;
    					$im->url = $k;
    					$im->hid = $obj->id;
    					$im->status = 1;
    					$im->save();
    				}
    			}

    			$user->qf_uid && $res = Yii::app()->controller->sendNotice('您好，'.$obj->title.'已成功提交至新房通后台，编辑会在2小时内（工作时间）完善项目资料后上线。如有其它疑问可致电：400-6677-021',$user->qf_uid);
    			Yii::app()->controller->sendNotice('有新的房源录入，房源名为'.$obj->title.'，请登录后台查看','',1);
    			$this->frame['data'] = $obj->id;
    		}

    	}
    }

    public function actionCheckName($name='') {
    	if($name) {
    		if($id = Yii::app()->db->createCommand("select id from plot where deleted=0 and title='$name'")->queryScalar()) {
    			$this->frame['data'] = $id;
    			$this->returnError('该项目已经发布，如果您是该项目的对接人，请点击项目详情页底部电话添加您的号码。');
    		}
    	}
    }

    public function actionCheckCanSub($phone='')
    {
    	$staff = UserExt::model()->find("phone='$phone'");
    	// 不是会员只能发一条
    	if($this->staff && $this->staff->type!=1) {
    		return $this->returnError('用户类型错误，只支持总代公司发布房源');
    	}
    	if($staff && $staff->vip_expire<time()) {
    		if($staff->plots)
    			return $this->returnError('您的免费项目配额已满，请至经纪圈成为会员后操作');
    	}
    	// if(!$this->staff || $this->staff->type!=1) {
    	// 	return $this->returnError('用户类型错误，只支持总代公司发布房源');
    	// } elseif($this->staff->vip_expire<time()) {
    	// 	return $this->returnError('您尚未成为会员，成为会员后即可享受无限次发布项目、无限次成为对接人等特权');
    	// }
    }

    public function actionCheckCompanyName($name='') {
    	if($name) {
    		if(Yii::app()->db->createCommand("select id from company where deleted=0 and name='$name'")->queryScalar()) {
    			$this->returnError('该公司已注册，请联系客服获取门店码！');
    		}
    	}
    }

    public function actionCheckIsMarket($hid='')
    {
    	if(!Yii::app()->user->getIsGuest()&&$hid) {
    		$plot = PlotExt::model()->findByPk($hid);
    		if($this->staff->type==3) {
    			return $this->returnError('您的账户为独立经纪人，如果您是'.$plot->company_name.'的员工，请联系客服修改账户归属。');
    		}
    		
    		if($plot&&$plot->company_id==$this->staff->cid) {
    			$this->returnSuccess('bingo');
    		} else {
    			$this->returnError('您的账户不属于'.$plot->company_name.'，不可以成为该项目的对接人哦！');
    		}
    	} else {
    		$this->returnError('账户或楼盘信息错误');
    	}
    }

    public function actionGetNameFromId($id='')
    {
    	if($id) {
    		$this->frame['data'] = PlotExt::model()->findByPk($id)->title;
    	}
    }

    public function actionGetAcsales()
    {
    	if(!Yii::app()->user->getIsGuest()) {
    		$hid = Yii::app()->db->createCommand("select hid from plot_place where uid=".$this->staff->id)->queryScalar();
    		if($hid) {
    			$ress = Yii::app()->db->createCommand("select u.id,u.name,u.phone from user u left join plot_sale s on u.id=s.uid where s.deleted=0 and s.hid=".$hid)->queryAll();
    			if($ress) {
    				foreach ($ress as $key => $value) {
    					$ress[$key]['name'] = $value['name'].$value['phone'];
    					unset($ress[$key]['phone']);
    				}
    			}
    			$this->frame['data'] = $ress;
    		}
    	} else {
    		$this->returnError('未知错误');
    	}
    }

    public function actionAddSale($sid='',$sale_uid='')
    {
    	if(!Yii::app()->user->getIsGuest()&&$sid&&$sale_uid) {
    		$sub = SubExt::model()->findByPk($sid);
    		$hisplot = $sub->plot;
    		$sub->sale_uid = $sale_uid;
    		$sub->save();
    		if($sale_uid && $sale = UserExt::model()->findByPk($sale_uid)) {
				$sale->qf_uid && $this->sendNotice('您好，'.$hisplot->title.'有新的客户来访，请登录案场销售后台查看。',$sale->qf_uid);
			}
    	} else {
    		$this->returnError('未知错误');
    	}
    }

    public function actionAddSave($hid='')
    {
    	if(!Yii::app()->user->getIsGuest()&&$hid) {
    		if($save = SaveExt::model()->find('hid='.(int)$hid.' and uid='.$this->staff->id)) {
    			SaveExt::model()->deleteAllByAttributes(['hid'=>$hid,'uid'=>$this->staff->id]);
    			$this->returnSuccess('取消收藏成功');
    		} else {
    			$save = new SaveExt;
    			$save->uid = $this->staff->id;
    			$save->hid = $hid;
    			$save->save();
    			$this->returnSuccess('收藏成功');
    		}
    	}else {
    		$this->returnError('请登录后操作');
    	}
    }

    public function actionUserList()
    {
    	if(!Yii::app()->user->getIsGuest()&&$this->staff->type>1) {
    		$criteria = new CDbCriteria;
    		$criteria->addCondition('uid='.$this->staff->id);
    		$kw = Yii::app()->request->getQuery('kw','');
    		$status = Yii::app()->request->getQuery('status','');
    		if($kw) {
    			if(is_numeric($kw)) {
    				$criteria->addSearchCondition('phone',$kw);
    			} else {
    				$criteria->addSearchCondition('name',$kw);
    			}
    		}
    		if(is_numeric($status)) {
    			$criteria->addCondition('status=:status');
    			$criteria->params[':status'] = $status;
    		}
    		$criteria->order = 'created desc';
    		$subs = SubExt::model()->undeleted()->getList($criteria);
    		$data = $data['list'] = [];
    		if($subs->data) {

    			foreach ($subs->data as $key => $value) {
    				
    				$itsstaff = $this->staff;
    				$tmp['id'] = $value->id;
    				$tmp['user_name'] = $value->name;
    				$tmp['user_phone'] = $value->phone;
    				$tmp['staff_name'] = Yii::app()->db->createCommand("select name from user where phone='".$value->notice."'")->queryScalar();
    				$tmp['staff_phone'] = $value->notice;
    				$tmp['time'] = date('m-d H:i',$value->updated);
    				$tmp['status'] = SubExt::$status[$value->status];
    				$tmp['staff_company'] = $value->plot?$value->plot->title:'';
    				$data['list'][] = $tmp;
    			}
    		}
    		$data['num'] = $subs->pagination->itemCount;
    		$this->frame['data'] = $data;
    	} else {
    		$this->returnError('用户类型错误，只支持分销或独立经纪人访问');
    	}
    }

    public function actionAddSubscribe()
    {
    	if(Yii::app()->request->getIsPostRequest()&&!Yii::app()->user->getIsGuest()) {
    		$params = Yii::app()->request->getPost('SubscribeExt',[]);
    		if($usu = UserSubscribeExt::model()->normal()->find('uid='.$this->staff->id)){
    			if($usu->num<=count($this->staff->subscribes)) {
    				return $this->returnError('您的订阅数已达上线');
    			}
    		} else {
    			return $this->returnError('您的订阅已到期，请先支付');
    		}
    		if($params) {
    			$criteria = new CDbCriteria;
    			$tmp = "uid=:uid and";
    			$criteria->params[':uid'] = $this->staff->id;
    			foreach ($params as $key => $value) {
    				$tmp .= " $key=:$key and";
    				$criteria->params[":$key"] = $value;
    			}
    			$tmp = trim($tmp,'and');
    			$criteria->addCondition($tmp);
    			// var_dump($criteria);exit;
    			if(SubscribeExt::model()->find($criteria)) {
    				$this->returnError('您已添加此类订阅，请勿重复添加');
    			} else {
    				$obj = new SubscribeExt;
    				$obj->attributes = $params;
    				$obj->uid = $this->staff->id;
    				if($obj->save()) {
    					$this->returnSuccess('添加成功');
    				} else {
    					$this->returnError(current(current($obj->getErrors())));
    				}
    			}
    		}
    	} else {
    		$this->returnError('请登录后操作');
    	}
    }

    public function actionGetSubscribeList()
    {
    	if(!Yii::app()->user->getIsGuest()) {
    		$subss = $this->staff->subscribes;
    		$data = [];
    		if($subss) {
    			foreach ($subss as $key => $value) {
    				$data[] = [
    				'id'=>$value->id,
    				'area'=>$value->area?$value->areainfo->name:'',
    				'area_id'=>$value->area,
    				'street'=>$value->street?$value->streetinfo->name:'',
    				'street_id'=>$value->street,
    				'minprice'=>$value->minprice,
    				'maxprice'=>$value->maxprice,
    				'wylx'=>$value->wylx?TagExt::model()->findByPk($value->wylx)->name:'',
    				'wylx_id'=>$value->wylx,
    				'zxzt'=>$value->zxzt?TagExt::model()->findByPk($value->zxzt)->name:'',
    				'zxzt_id'=>$value->zxzt,
    				];
    			}
    			$this->frame['data'] = $data;
    		}
    	}
    }

    public function actionDelSubscribe($id='')
    {
    	SubscribeExt::model()->deleteAllByAttributes(['id'=>$id]);
    	$this->returnSuccess('操作成功');
    }

    public function actionJoinCompany($code='')
    {
    	if($code&&!Yii::app()->user->getIsGuest()) {
    		if($com = CompanyExt::model()->undeleted()->find("code='$code'")){
    			if(substr($code, 0,1)=='6')
    				$this->staff->type = 2;
    			elseif(substr($code, 0,1)=='8')
    				$this->staff->type = 1;
    			$this->staff->cid = $com->id;
    			$this->staff->save();
    			$log = new UserLogExt;
    			$log->from = 0;
    			$log->to = $com->id;
    			$log->uid = $this->staff->id;
    			$log->save();
    			$this->returnSuccess('您已成功绑定到'.$com->name);
    		} else {
    			$this->returnError('该门店码不存在，请核实或联系客服');
    		}
    	} else {
    		$this->returnError('请登录后操作');
    	}
    }

    public function actionCheckCanSubscribe()
    {
    	if(!Yii::app()->user->getIsGuest()) {
    		$obj = UserSubscribeExt::model()->normal()->find('uid='.$this->staff->id);
    		if(!$obj) {
				$this->returnError('订阅上新房源仅需9.9元/月');
    		}
    	} else {
    		$this->returnError('请登录后操作');
    	}
    }

    public function actionAddSubscribePay()
    {
    	if(Yii::app()->request->getIsPostRequest()&&!Yii::app()->user->getIsGuest()) {
    		$num = $_POST['num'];
    		$title = $_POST['title'];
    		$time = $num*(strstr($title, '年')?(365*86400):(30*86400));
    		$obj = new UserSubscribeExt;
    		$obj->uid = $this->staff->id;
    		$obj->expire = time()+$time;
    		$obj->save();
    	}
    }
    public function actionLeave($id='')
    {
    	if($id && !Yii::app()->user->getIsGuest() && $this->staff->id == $id) {
    		$info = UserExt::model()->findByPk($id);
            if($info->cid) {
                UserExt::model()->updateAll(['parent'=>0],'parent=:pa',[':pa'=>$id]);
                $info->cid = 0;
                $info->is_jl = 0;
                $info->is_manage = 0;
                $info->parent = 0;
                if($info->save()) {
                    $log = new UserLogExt;
                    $log->from = $info->cid;
                    $log->uid = $id;
                    $log->to = 0;
                    $log->save();
                }
            }
    	} else {
    		$this->returnError('未知错误');
    	}
    }

    public function actionSetVip()
    {
    	if(!Yii::app()->user->getIsGuest() && Yii::app()->request->getIsPostRequest()) {
			if($title = $this->cleanXss($_POST['title'])) {
				$num = $this->cleanXss($_POST['num']);
				if(strstr($title, '1')) {
					$time = 365*86400*$num;
				} elseif (strstr($title, '2')) {
					$time = 365*86400*2*$num;
				}
				if($obj = $this->staff) {
					if($obj->vip_expire<time()) {
						$obj->vip_expire = time()+$time;
					} else {
						$obj->vip_expire = $obj->vip_expire+$time;
					}
					if(!$obj->save()) {
						$this->returnError(current(current($obj->getErrors())));
					}
				}
			}
		} else{
			$this->returnError('未知错误');
		}
    }

    public function actionCheckIsVip()
    {
    	if(!Yii::app()->user->getIsGuest()&& $staff = $this->staff) {
    		if($staff->type>1) {
    			return $this->returnError('系统只支持总代用户类型成为会员');
    		}
    		if($staff->vip_expire>time()) {
    			$this->returnSuccess('bingo');
    		} else {
    			$this->returnError('您尚未成为会员，成为会员后即可享受无限次发布项目、无限次成为对接人等特权');
    		}
    	} else {
    		$this->returnError('请先登录');
    	}
    }

    public function actionAddMakertNew()
    {
    	if(!Yii::app()->user->getIsGuest()&&Yii::app()->request->getIsPostRequest()&& $staff = $this->staff) {
    		if($staff->vip_expire>time()) {
    			if($hid = $this->cleanXss($_POST['hid'])) {
					$plot = PlotExt::model()->findByPk($hid);
					
					$uid = $this->staff->id;
					// var_dump($uid,$hid);exit;
					$criteria = new CDbCriteria;
					$criteria->addCondition("uid=$uid and hid=$hid and deleted=0");
					$obj = PlotMarketUserExt::model()->normal()->find($criteria);
					if(!$obj)
						$obj = new PlotMarketUserExt;
					if($obj->expire>time()) {
						return $this->returnError('您已是该项目对接人，请勿重复操作');
					}
					// if(!Yii::app()->db->createCommand("select id from plot_makert_user where uid=$uid and hid=$hid and deleted=0 and expire>".time())->queryRow()) {
						// $obj = new PlotMarketUserExt;
						if($plot->uid&&$plot->uid==$uid) {
							$obj->is_manager = 1;
						}
						$obj->status = 1;
						$obj->uid = $uid;
						$obj->hid = $hid;
						if($obj->expire<time()) {
							$obj->expire = $staff->vip_expire;
						} 
						
						if(!$obj->save())
							$this->returnError(current(current($obj->getErrors())));
					// } else {
					// 	$this->returnError('您已经提交申请，请勿重复提交');
					// }
				}
    		} else {
    			return $this->returnError('您尚未成为会员或已到期，成为会员后即可享受无限次发布项目、无限次成为对接人等特权');
    		}
    	} else {
    		$this->returnError('未知错误');
    	}
    }

    public function actionGetOldExpire()
    {
    	if(!Yii::app()->user->getIsGuest()&&$this->staff&&$this->staff->vip_expire==0) {
    		$num = 0;
    		$expire = PlotMarketUserExt::model()->findAll('uid='.$this->staff->id);
    		if($expire) {
    			foreach ($expire as $key => $value) {
    				if($value->expire>1513270861) {
    					$num += 268;
    				} elseif($value->expire>0) {
    					$num += 99;
    				}
    			}
    		}
    		$this->frame['data'] = $num;
    	} else {
    		$this->returnError('未知错误');
    	}
    }

}