<?php
/**
 * 楼盘控制器
 * @author tivon <[<email address>]>
 * @date(2017.03.17)
 */
class PlotController extends VipController{
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
		if(Yii::app()->user->id>1) {
			$id = (int)Yii::app()->request->getQuery('id',0);
			!$id && $id = (int)Yii::app()->request->getQuery('hid',0);
			if($id) {
				$hids = Yii::app()->db->createCommand("select hid from plot_company where cid=".Yii::app()->user->cid)->queryAll();
				$ids = [];
				if($hids) {
					foreach ($hids as $key => $value) {
						$ids[] = $value['hid'];
					}

				}
				if(!$ids || !in_array($id, $ids)) {
					$this->redirect('list');
				} else {
					$chain->run();
				}
			}
		} else {
			$chain->run();
		}
	}
	public $controllerName = '';
	/**
	 * [actionList 楼盘列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionList($type='title',$value='',$time_type='created',$time='',$cate='',$cate1='',$company='')
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
        if(Yii::app()->user->id>1) {
        	$company=Yii::app()->user->cid;
        }
        if($company) {
        	// $ids = Yii::app()->db->createCommand("select hid from plot_company where cid=$company")->queryAll();
        	// $idArr = [];
        	// if($ids) {
        	// 	foreach ($ids as $id) {
        	// 		$idArr[] = $id['hid'];
        	// 	}
        	// }
        	// $criteria->addInCondition('id',$idArr);
        	$criteria->addCondition('company_id=:comid');
        	$criteria->params[':comid'] = $company;
        }
		$this->controllerName = '楼盘';
		$criteria->order = 'sort desc,updated desc,id desc';
		$infos = PlotExt::model()->undeleted()->getList($criteria,20);
		$this->render('list',['cate'=>$cate,'cate1'=>$cate1,'infos'=>$infos->data,'pager'=>$infos->pagination,'type' => $type,'value' => $value,'time' => $time,'time_type' => $time_type,]);
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
			$this->redirect('/vip');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotHxExt::model()->undeleted()->getList($criteria,20);
		$this->render('hxlist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
	}

	/**
	 * [actionList 户型列表]
	 * @param  string $title [description]
	 * @return [type]        [description]
	 */
	public function actionPlacelist($hid='')
	{
		// $_SERVER['HTTP_REFERER']='http://www.baidu.com';
		$house = PlotExt::model()->findByPk($hid);
		if(!$house){
			$this->redirect('/vip');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotPlaceExt::model()->undeleted()->getList($criteria,20);
		// var_dump($houses->data);exit;
		$this->render('placelist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
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
			$this->redirect('/vip');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotNewsExt::model()->undeleted()->getList($criteria,20);
		$this->render('newslist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
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
			$this->redirect('/vip');
		}
		$criteria = new CDbCriteria;
		$criteria->order = 'updated desc,id desc';
		$criteria->addCondition('pid=:hid');
		$criteria->params[':hid'] = $hid;
		$houses = PlotWdExt::model()->undeleted()->getList($criteria,20);
		$this->render('wdlist',['infos'=>$houses->data,'pager'=>$houses->pagination,'house'=>$house]);
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
			$this->redirect('/vip');
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
			$this->redirect('/vip');
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
			$plot = PlotExt::model()->findByPk($id);
			$plot->deleted=1;
			if($plot->save()) {
				$this->setMessage('操作成功','success');
			} else {
				$this->setMessage('操作失败','error');
			}
		}
	}

	/**
	 * [actionEdit 楼盘编辑页]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function actionEdit($id='')
	{
		$house = $id ? PlotExt::model()->findByPk($id) : new PlotExt;
		if(Yii::app()->request->getIsPostRequest()) {
			$values = Yii::app()->request->getPost('PlotExt',[]);
			$house->attributes = $values;
			if(strpos($house->open_time,'-')) {
				$house->open_time = strtotime($house->open_time);
			}
			if(strpos($house->delivery_time,'-')) {
				$house->delivery_time = strtotime($house->delivery_time);
			}
			$zd_company = [];
			if(Yii::app()->user->id==1) {
				$zd_company = $house->zd_company;
				
			} else {
				if($house->getIsNewRecord())
					$zd_company = [Yii::app()->user->cid];
			}
			if(!is_array($zd_company) && $zd_company) {
				$zd_company = [$zd_company];
			}
			// var_dump($zd_company);exit;
			if($zd_company) {
				$house->company_name = CompanyExt::model()->findByPk($zd_company[0])->name;
				$house->company_id = $zd_company[0];
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
			// var_dump($tagArray);exit;
			if($house->save()) {
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
				$this->redirect('/vip/plot/list');
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
				$this->redirect('/vip/plot/list');
			}
				
		}elseif($imgs){
			if(!strstr($imgs[0]['url'],'http')) {
				$this->setMessage('已处理','success');
				$this->redirect('/vip/plot/list');
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
        $this->redirect('/vip/plot/list');
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

	public function actionEditNews()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotNewsExt';
		$this->controllerName = '楼盘动态';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['newslist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('newsedit',['article'=>$info,'hid'=>$hid]);
	}

	public function actionEditPlace()
	{
		$id = Yii::app()->request->getQuery('id','');
		$hid = $_GET['hid'];
		$modelName = 'PlotPlaceExt';
		$this->controllerName = '案场助理';
		$info = $id ? $modelName::model()->findByPk($id) : new $modelName;
		$info->getIsNewRecord() && $info->status = 1;
		if(Yii::app()->request->getIsPostRequest()) {
			$info->attributes = Yii::app()->request->getPost($modelName,[]);
			$info->hid = $hid;
			// var_dump($info->attributes);exit;
			if($info->save()) {
				$this->setMessage('操作成功','success',['placelist?hid='.$hid]);
			} else {
				$this->setMessage(array_values($info->errors)[0][0],'error');
			}
		} 
		$this->render('placeedit',['article'=>$info,'hid'=>$hid]);
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

	public function actionChangeMarket($hid='',$kw='')
	{
		$plot = PlotExt::model()->findByPk($hid);
		if($plot){
			$plot->market_user = $kw;
			if($plot->save()) {
				$this->setMessage('操作成功');
			} else {
				$this->setMessage('操作失败','error');
			}
		}
	}

	public function actionChangePlace($hid='',$uid='')
	{
		if(Yii::app()->db->createCommand("select id from plot where status=1 and deleted=0 and place_user=".$uid)->queryScalar()) {
			return $this->setMessage('案场不能重复','error');;
		}
		$plot = PlotExt::model()->findByPk($hid);
		if($plot){
			$plot->place_user = $uid;
			if($plot->save()) {
				$this->setMessage('操作成功');
			} else {
				$this->setMessage('操作失败','error');
			}
		}
	}

}