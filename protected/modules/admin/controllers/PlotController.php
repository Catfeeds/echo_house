<?php
/**
 * 楼盘控制器
 * @author tivon <[<email address>]>
 * @date(2017.03.17)
 */
class PlotController extends AdminController{
	public function init()
	{
		parent::init();
		
			
	}

	public function filters()
	{
		return ['staff+newsList,imageList,priceList'];
	}

	public function filterStaff($chain)
	{
		// if(Yii::app()->user->id>1) {
		// 	$id = (int)Yii::app()->request->getQuery('id',0);
		// 	!$id && $id = (int)Yii::app()->request->getQuery('hid',0);
		// 	if($id) {
		// 		$hids = Yii::app()->db->createCommand("select hid from plot_company where cid=".Yii::app()->user->cid)->queryAll();
		// 		$ids = [];
		// 		if($hids) {
		// 			foreach ($hids as $key => $value) {
		// 				$ids[] = $value['hid'];
		// 			}

		// 		}
		// 		if(!$ids || !in_array($id, $ids)) {
		// 			$this->redirect('list');
		// 		} else {
		// 			$chain->run();
		// 		}
		// 	}
		// } else {
		// 	$chain->run();
		// }
		$chain->run();
	}
	public $controllerName = '';
	/**
	 * [actionList 楼盘列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$company='',$is_uid='',$sort='',$city='',$area='',$street='')
	{
		$modelName = 'PlotExt';
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
                $criteria->addSearchCondition('title', $value);
            } 
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
        // if(Yii::app()->user->id>1) {
        // 	$company=Yii::app()->user->cid;
        // }
        if($company) {
        	$criteria->addCondition('company_id=:comid');
        	$criteria->params[':comid'] = $company;
        }
        if($area) {
        	$criteria->addCondition('area=:area');
        	$criteria->params[':area'] = $area;
        }
        if($city) {
        	$criteria->addCondition('city=:city');
        	$criteria->params[':city'] = $city;
        }
        if(is_numeric($status)) {
        	$criteria->addCondition('status=:status');
        	$criteria->params[':status'] = $status;
        }
         if(is_numeric($is_uid)) {
         	if($is_uid)
        		$criteria->addCondition('uid>0');
        	else
        		$criteria->addCondition('uid=0');
        }
		$this->controllerName = '楼盘';
		$criteria->order = 'is_unshow asc,sort desc,refresh_time desc';
		if($sort) {
			$criteria->order = $sort.' desc';
		}
		$infos = PlotExt::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'status'=>$status,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'is_uid'=>$is_uid,'city'=>$city,'area'=>$area,'street'=>$street]);
	}

	/**
	 * [actionList 户型列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionHxlist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotHxExt::model()->undeleted()->getList($criteria,20);
		$this->render('hxlist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 动态列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionNewslist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotNewsExt::model()->undeleted()->getList($criteria,20);
		$this->render('newslist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}
	/**
	 * [actionList 动态列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionNewslistnew($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$company='',$is_uid='',$sort='')
	{
		$modelName = 'PlotExt';
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$cre = new CDbCriteria;
                $cre->addSearchCondition('title', $value);
                $ids = [];
                if($ress = PlotExt::model()->findAll($cre)) {
                	// var_dump($ress);
                	foreach ($ress as $res) {
                		$ids[] = $res['id'];
                	}
                }
                // var_dump($ids);
                $criteria->addInCondition('hid',$ids);
            } elseif($type=='author') {
            	$criteria->addSearchCondition('author',$value);
            }
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
        if(Yii::app()->user->id>1) {
        	$company=Yii::app()->user->cid;
        }
        if($company) {
        	$criteria->addCondition('company_id=:comid');
        	$criteria->params[':comid'] = $company;
        }
        if(is_numeric($status)) {
        	$criteria->addCondition('status=:status');
        	$criteria->params[':status'] = $status;
        }
         if(is_numeric($is_uid)) {
         	if($is_uid)
        		$criteria->addCondition('uid>0');
        	else
        		$criteria->addCondition('uid=0');
        }
		$this->controllerName = '项目动态';
		$criteria->order = 'sort desc,created desc';
		if($sort) {
			$criteria->order = $sort.' desc';
		}
		$infos = PlotNewsExt::model()->undeleted()->getList($criteria,20);
		$this->render('newslistnew',['cate'=>$cate,'status'=>$status,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'is_uid'=>$is_uid]);
	}
	/**
	 * [actionList 动态列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionCalllist($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$company='',$is_uid='',$sort='')
	{
		$modelName = 'PlotExt';
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$cre = new CDbCriteria;
                $cre->addSearchCondition('title', $value);
                $ids = [];
                if($ress = PlotExt::model()->findAll($cre)) {
                	// var_dump($ress);
                	foreach ($ress as $res) {
                		$ids[] = $res['id'];
                	}
                }
                // var_dump($ids);
                $criteria->addInCondition('hid',$ids);
            } elseif($type=='calla') {
            	$criteria->addSearchCondition('calla',$value);
            } elseif($type=='callb') {
            	$criteria->addSearchCondition('callb',$value);
            }
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
        if(Yii::app()->user->id>1) {
        	$company=Yii::app()->user->cid;
        }
        if($company) {
        	$criteria->addCondition('company_id=:comid');
        	$criteria->params[':comid'] = $company;
        }
        if(is_numeric($status)) {
        	$criteria->addCondition('status=:status');
        	$criteria->params[':status'] = $status;
        }
         if(is_numeric($is_uid)) {
         	if($is_uid)
        		$criteria->addCondition('uid>0');
        	else
        		$criteria->addCondition('uid=0');
        }
		$this->controllerName = '项目呼叫';
		$criteria->order = 'created desc';
		// if($sort) {
		// 	$criteria->order = $sort.' desc';
		// }
		$infos = PlotCallExt::model()->getList($criteria,20);
		$this->render('calllist',['cate'=>$cate,'status'=>$status,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'is_uid'=>$is_uid]);
	}

	/**
	 * [actionList 问答列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionWdslist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('pid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotWdExt::model()->undeleted()->getList($criteria,20);
		$this->render('wdlist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 问答列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionDplist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotDpExt::model()->getList($criteria,20);
		$this->render('dplist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 问答列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionEditlist($type='title',$value='',$time_type='created',$time='',$cate='',$status='',$company='',$is_uid='',$sort='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$modelName = 'PlotEditLogExt';
		$criteria = new CDbCriteria;
		if($value = trim($value))
            if ($type=='title') {
            	$plocriteria = new CDbCriteria;
                $plocriteria->addSearchCondition('title', $value);
                $ids = [];
                if($resss = PlotExt::model()->findAll($plocriteria)) {
                	foreach ($resss as $res) {
                		$ids[] = $res->id;
                	}
                }
                $criteria->addInCondition('hid',$ids);
            } 
        //添加时间、刷新时间筛选
        if($time_type!='' && $time!='')
        {
            list($beginTime, $endTime) = explode('-', $time);
            $beginTime = (int)strtotime(trim($beginTime));
            $endTime = (int)strtotime(trim($endTime));
            $criteria->addCondition("{$time_type}>=:beginTime");
            $criteria->addCondition("{$time_type}<:endTime");
            $criteria->params[':beginTime'] = TimeTools::getDayBeginTime($beginTime);
            $criteria->params[':endTime'] = TimeTools::getDayEndTime($endTime);

        }
        if($company) {
        	$criteria->addCondition('company_id=:comid');
        	$criteria->params[':comid'] = $company;
        }
        if(is_numeric($status)) {
        	$criteria->addCondition('status=:status');
        	$criteria->params[':status'] = $status;
        }
         if(is_numeric($is_uid)) {
         	if($is_uid)
        		$criteria->addCondition('uid>0');
        	else
        		$criteria->addCondition('uid=0');
        }
		$this->controllerName = '楼盘';
		$criteria->order = 'updated desc';
		if($sort) {
			$criteria->order = $sort.' desc';
		}
		$infos = PlotEditLogExt::model()->getList($criteria,20);
		$this->render('editlist',['cate'=>$cate,'status'=>$status,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,'is_uid'=>$is_uid]);
	}

	/**
	 * [actionList 问答列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionAsklist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotAskExt::model()->getList($criteria,20);
		$this->render('asklist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 问答列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionAnswerlist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotAnswerExt::model()->getList($criteria,20);
		$this->render('answerlist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 相册列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionImagelist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		if(Yii::app()->request->getIsPostRequest()) {
			PlotImageExt::model()->deleteAllByAttributes(['hid'=>$house->id]);
			$values = Yii::app()->request->getPost("TkExt",[]);
			$urls = $values['album'];
			$type = $values['type'];
			$sort = $values['sort'];
			if($urls) {
				foreach ($urls as $key => $value) {
					$model =  new PlotImageExt;
					$model->hid = $house->id;
					$model->url = $value;
					$model->sort = $sort[$key];
					$model->type = $type[$key];
					$model->save();
				}
			}
			$this->redirect('list');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotImageExt::model()->undeleted()->getList($criteria,20);
		// var_dump($houses->dat);exit;
		// $this->render('imagelist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
		$this->render('images',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}
	/**
	 * [actionList 相册列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionPricelist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/admin');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotPayExt::model()->undeleted()->getList($criteria,20);
		// var_dump($houses->dat);exit;
		$this->render('pricelist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	public function actionAjaxDel($id='')
	{
		if($id) {
			// $plot = PlotExt::model()->findByPk($id);
			PlotExt::model()->deleteAllByAttributes(['id'=>$id]);
			$this->setMessage('操作成功','success');
		}
	}

	/**
	 * [actionEdit 楼盘编辑页]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function actionEdit($id='')
	{
		$change = 0;
		$house = $id ? PlotExt::model()->findByPk($id) : new PlotExt;
		if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('PlotExt',[]);
			if($values['status']==1&&$house->status==0) {
				$change = 1;	
			}
			$house->attributes = $values;
			if(strpos($house->open_time,'-')) {
				$house->open_time = strtotime($house->open_time);
			}
			if(strpos($house->top_time,'-')) {
				$house->top_time = strtotime($house->top_time);
			}
			if(strpos($house->qjtop_time,'-')) {
				$house->qjtop_time = strtotime($house->qjtop_time);
			}
			if(strpos($house->delivery_time,'-')) {
				$house->delivery_time = strtotime($house->delivery_time);
			}
			// $company_id = '';
			// if(Yii::app()->user->id==1) {
			// 	$company_id = $house->company_id;
				
			// } else {
			// 	if($house->getIsNewRecord() && !$company_id) 
			// 		$company_id = Yii::app()->user->cid;
			// }
			// if(!is_array($zd_company) && $zd_company) {
			// 	$zd_company = [$zd_company];
			// }
			// var_dump($zd_company);exit;
			if($house->company_id) {
				$house->company_name = CompanyExt::model()->findByPk($house->company_id)->name;
			}
				
			$tagArray = [];
			foreach (PlotExt::$tagArr as $tagKey) {
				if(isset($values[$tagKey])&&$values[$tagKey]) {
					if(!is_array($house->$tagKey))
						$tmp = [$house->$tagKey];
					else
						$tmp = $house->$tagKey;
					$tagArray = array_merge($tagArray,$tmp);
				}
			}
			// 坑爹的composer暂时解决不了
			// if($house->getIsNewRecord()) {
			// 	if($house->company_id && $house->market_users) {
	  //               $mks = explode(' ', $house->market_users);
	  //               foreach ($mks as $key => $value) {
	  //                   preg_match_all('/[0-9]+/', $value,$num);
	  //                   if(isset($num[0][0])) {
	  //                       $num = $num[0][0];

	  //                       if(!Yii::app()->db->createCommand("select id from user where phone='".$num."'")->queryScalar()){
	  //                       	$time = time();
	  //                           // $obj = new UserExt;
	  //                           // $obj->phone = $num;
	  //                           // $obj->status = $obj->type = 1;
	  //                           // $obj->cid = $house->company_id;
	  //                           // $obj->name = str_replace($num, '', $value);
	  //                           // $obj->save();
	  //                           $sql = "insert into user(phone,status,cid,name,type,created,updated) values('$num',1,".$house->company_id.",'".str_replace($num, '', $value)."',1,$time,$time)";
	  //                           Yii::app()->db->createCommand($sql)->execute();
	  //                           // SmsExt::sendMsg('新用户注册',$obj->phone,['name'=>$obj->name,'num'=>PlotExt::model()->normal()->count()+800]);
	  //                           SmsExt::sendMsg('新用户注册','13861242596',['name'=>'zt','num'=>PlotExt::model()->normal()->count()+800]);
	  //                       }
	  //                   }
	  //               }
	  //           }		
			// }
			// exit;
			// var_dump($tagArray);exit;
			if($house->getIsNewRecord()) {
				$house->staff_id = Yii::app()->user->id;
			}
			if($house->save()) {
				if($change) {
					$house->changeS();
				}
				// if($zd_company) {
				// 	PlotCompanyExt::model()->deleteAllByAttributes(['hid'=>$house->id]);
				// 	foreach ($zd_company as $cid) {
				// 		$obj = new PlotCompanyExt;
				// 		$obj->hid = $house->id;
				// 		$obj->cid = $cid;
				// 		$obj->save();
				// 	}
				// }
				PlotTagExt::model()->deleteAllByAttributes(['hid'=>$house->id]);
				if($tagArray)
					foreach ($tagArray as $tid) {
						$obj = new PlotTagExt;
						$obj->hid = $house->id;
						$obj->tid = $tid;
						$obj->save();
					}
				$this->setMessage('保存成功','success');
				$this->redirect('/admin/plot/list');
			} else {
				$this->setMessage(current(current($house->getErrors())),'error');
			}
		}
		$this->render('edit',['plot'=>$house]);
	}

	public function actionDealimage($hid='')
	{
		$value = PlotExt::model()->findByPk($hid);
		$hxs = $value->hxs;
		$imgs = $value->images;
		if($hxs){
			if(!strstr($hxs[0]['image'],'http')) {
				$this->setMessage('已处理','success');
				$this->redirect('/admin/plot/list');
			}
				
		}elseif($imgs){
			if(!strstr($imgs[0]['url'],'http')) {
				$this->setMessage('已处理','success');
				$this->redirect('/admin/plot/list');
			}
				
		}
		// $value->image = $this->sfimage($value->image,$value->image);
  //       $value->save();
        if($hxs){
            foreach ($hxs as $hx) {
                $hx->image = $this->sfimage($hx->image,$hx->image);
                $hx->save();
            }
        }
        if($imgs){
            foreach ($imgs as $img) {
                $img->url = $this->sfimage($img->url,$img->url);
                $img->save();
            }
        }
        $this->setMessage('处理完毕','success');
        $this->redirect('/admin/plot/list');
	}

	public function actionDelNews($id='')
	{
		$news = PlotNewsExt::model()->findByPk($id);
		$news->deleted = 1;
		$news->save();
		$this->setMessage('操作成功','success');
	}

	public function actionDelPrices($id='')
	{
		$news = PlotPriceExt::model()->findByPk($id);
		$news->deleted = 1;
		$news->save();
		$this->setMessage('操作成功','success');
	}

	public function actionDelWds($id='')
	{
		$news = PlotWdExt::model()->findByPk($id);
		$news->deleted = 1;
		$news->save();
		$this->setMessage('操作成功','success');
	}

	public function actionEditImage()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotImageExt';
		$this->controllerName = '楼盘相册';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			if($info->save()) {
				$this->setMessage('操作成功','success',['imagelist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('imageedit',['article'=>$info,'hid'=>$hid]);
	}

	public function actionEditHx()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotHxExt';
		$this->controllerName = '楼盘户型';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);

			if($info->save()) {
				$this->setMessage('操作成功','success',['hxlist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('hxedit',['article'=>$info,'hid'=>$hid]);
	}

	public function actionNewseditnew()
	{
		$id = Yii::app()->request->getQuery('id','');
		$modelName = 'PlotNewsExt';
		$this->controllerName = '楼盘动态';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if($info->getIsNewRecord()) {
			$info->staff_id = Yii::app()->user->id;
		}
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);

			if($info->save()) {
				$this->setMessage('操作成功','success',['newslistnew']);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('newseditnew',['article'=>$info]);
	}

	public function actionEditNews()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotNewsExt';
		$this->controllerName = '楼盘动态';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if($info->getIsNewRecord()) {
			$info->staff_id = Yii::app()->user->id;
		}
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->status = 1;
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['newslist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('newsedit',['article'=>$info,'hid'=>$hid]);
	}

	public function actionEditdp()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotDpExt';
		$this->controllerName = '楼盘点评';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$userphone = Yii::app()->request->getPost('userphone');
			$uid = '';
			if($userphone) {
				$user = UserExt::model()->find("phone='$userphone'");
				$uid = $user->id;
			}
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->uid = $uid;
			$info->status = 1;
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['dplist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('dpedit',['article'=>$info,'hid'=>$hid,'userphone'=>isset($info->user->phone)?$info->user->phone:'']);
	}
	public function actionEditask()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotAskExt';
		$this->controllerName = '楼盘提问';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$userphone = Yii::app()->request->getPost('userphone');
			$uid = '';
			if($userphone) {
				$user = UserExt::model()->find("phone='$userphone'");
				$uid = $user->id;
			}
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->uid = $uid;
			$info->status = 1;
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['asklist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('askedit',['article'=>$info,'hid'=>$hid,'userphone'=>isset($info->user->phone)?$info->user->phone:'']);
	}

	public function actionEditanswer()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotAnswerExt';
		$this->controllerName = '楼盘回答';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$userphone = Yii::app()->request->getPost('userphone');
			$uid = '';
			if($userphone) {
				$user = UserExt::model()->find("phone='$userphone'");
				$uid = $user->id;
			}
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->uid = $uid;
			$info->status = 1;
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['answerlist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('answeredit',['article'=>$info,'hid'=>$hid,'userphone'=>isset($info->user->phone)?$info->user->phone:'']);
	}

	public function actionEditPrice()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotPayExt';
		$this->controllerName = '楼盘佣金';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);

			if($info->save()) {
				$this->setMessage('操作成功','success',['pricelist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('priceedit',['article'=>$info,'hid'=>$hid]);
	}

	public function actionCleanPublisher($id='')
	{
		if($id) {
			$obj = PlotExt::model()->findByPk($id);
			PlotMarketUserExt::model()->deleteAllByAttributes(['uid'=>$obj->uid,'hid'=>$id]);
			$obj->uid = 0;
			$obj->save();
			$this->setMessage('操作成功','success');
		}
	}

	public function actionRefresh($id='')
	{
		if($id) {
			$obj = PlotExt::model()->findByPk($id);
			$obj->refresh_time = time();
			$obj->save();
			$this->setMessage('操作成功','success');
		}
	}

	public function actionFindInfo()
	{
		$this->render('findInfo');
	}

	public function actionFindU($kw)
	{
		if($kw) {
			$criteria = new CDbCriteria;
			if(is_numeric($kw)) {
				$criteria->addSearchCondition('phone',$kw);
			} else {
				$criteria->addSearchCondition('name',$kw);
			}
			if($userres = UserExt::model()->findAll($criteria)) {
				if($userres) {
					$tmp['s'] = 'success';
					foreach ($userres as $key => $user) {
						$cinfo = $user->companyinfo;
						$tmp['list'][] = ['id'=>$user->id,'name'=>$user->name,'phone'=>$user->phone,'company'=>$cinfo?$cinfo->name:'','type'=>UserExt::$ids[$user->type]];
					}
				}
				// $cinfo = $user->companyinfo;
				// echo json_encode(['s'=>'success','id'=>$user->id,'name'=>$user->name,'phone'=>$user->phone,'company'=>$cinfo?$cinfo->name:'','type'=>UserExt::$ids[$user->type]]);
				echo json_encode($tmp);
			} else {
				echo json_encode(['s'=>'error']);
			}

		}
			
	}

	public function actionFindC($kw)
	{
		if($kw) {
			$criteria = new CDbCriteria;
			if(is_numeric($kw)) {
				$criteria->addSearchCondition('code',$kw);
			} else {
				$criteria->addSearchCondition('name',$kw);
			}
			if($companyrees = CompanyExt::model()->findAll($criteria)) {
				$tmp['s'] = 'success';
				foreach ($companyrees as $key => $company) {
					$tmp['list'][] = ['id'=>$company->id,'code'=>$company->code,'name'=>$company->name,'type'=>UserExt::$ids[$company->type]];
				}
				// $cinfo = $user->companyinfo;
				echo json_encode($tmp);
			} else {
				echo json_encode(['s'=>'error']);
			}
		}
	}

}