<?php
/**
 * 相册控制器
 */
class AlbumController extends HomeController{

	public $cates = [];

	public $rights = [];

	// public $kw = '';

	public function init()
	{

		parent::init();
		$this->cates = CHtml::listData(TkCateExt::model()->normal()->findAll(),'id','name');
		// 热门推荐
        $rmtjs = RecomExt::getObjFromCate(2,6);
        // 三个联赛
        $leas = LeagueExt::model()->normal()->findAll(['limit'=>3]);
        // 积分
        $points = [];
        if($leas) {
            foreach ($leas as $key => $value) {
                $criteria = new CDbCriteria;
                $criteria->addCondition('lid=:lid');
                $criteria->params[':lid'] = $value->id;
                $criteria->order = 'points desc';
                $criteria->limit = 10;
                $points[] = PointsExt::model()->findAll($criteria);

            }
        }
        // 十个评论
        $comms = CommentExt::model()->normal()->findAll(['limit'=>10,'order'=>'praise desc, created asc']);
        $this->rights = ['leas'=>$leas,'points'=>$points,'rmtjs'=>$rmtjs,'comms'=>$comms];
	}
	public function actionList($cid='',$kw='')
	{
		$criteria = new CDbCriteria;
		if($kw) {
			$criteria->addSearchCondition('title',$kw);
			$this->kw = $kw;
		}
		if($cid) {
			$cate = TkCateExt::model()->find(['condition'=>"pinyin='$cid'"]);
			$seo = json_decode($cate->seo,true);
			if(isset($seo['t']) && $seo['t'])
				$this->pageTitle = $seo['t'];
			if(isset($seo['d']) && $seo['d'])
				$this->description = $seo['d'];
			if(isset($seo['k']) && $seo['k'])
				$this->keyword = $seo['k'];
			$criteria->addCondition('cid=:cid');
			$criteria->params[':cid'] = $cate->id;
			$cid = $cate->id;
		} else {
			$t = SiteExt::getAttr('seo','home_album_list_title');
	        $k = SiteExt::getAttr('seo','home_album_list_keyword');
	        $d = SiteExt::getAttr('seo','home_album_list_desc');

	        foreach (['{site}'=>'球布斯'] as $key => $value) {
	        	$t && $this->pageTitle = str_replace($key, $value, $t);
		        $k && $this->keyword = str_replace($key, $value, $k);
		        $d && $this->description = str_replace($key, $value, $d);
	        }
		}
		$datas = TkExt::model()->normal()->getList($criteria,18);
		$infos = $datas->data;
		$pager = $datas->pagination;
		// if($infos) {
		// 	$ids = '';
		// 	foreach ($infos as $key => $value) {
		// 		$ids .= $value->id.',';
		// 	}
		// 	setCookie('album_list_ids',trim($ids));
		// }
		
        // var_dump($this->cates);exit;
        if($this->iswap)
			$this->render('alist',['infos'=>$infos,'pager'=>$pager,'cid'=>$cid,'cates'=>$this->cates,'rights'=>$this->rights]);
		else
			$this->render('list',['infos'=>$infos,'pager'=>$pager,'cid'=>$cid,'cates'=>$this->cates,'rights'=>$this->rights]);
	}

	public function actionInfo($id='')
	{
		$t = SiteExt::getAttr('seo','home_album_info_title');
        $k = SiteExt::getAttr('seo','home_album_info_keyword');
        $d = SiteExt::getAttr('seo','home_album_info_desc');
        $info = TkExt::model()->findByPk($id);
       foreach (['{site}'=>'球布斯','{cate}'=>$info->cate?$info->cate->name:'','{title}'=>$info->title] as $key => $value) {
	        	$t && $t = $this->pageTitle = str_replace($key, $value, $t);
		        $k && $k = $this->keyword = str_replace($key, $value, $k);
		        $d && $d = $this->description = str_replace($key, $value, $d);
	        }

		// var_dump($this->user);exit;
		$info->hits += 1;
		$info->save();
		$nextid = $preid = '';
		// isset($_COOKIE['album_list_ids']) && $lists = $_COOKIE['album_list_ids'];
		// if(isset($lists) && $lists) {
		// 	$lists = explode(',', $lists);
		// 	foreach ($lists as $key => $value) {
		// 		if($id==$value) {
		// 			isset($lists[$key+1]) && $nextid = $lists[$key+1];
		// 			isset($lists[$key-1]) && $preid = $lists[$key-1];
		// 		}
		// 	}
		// } else {
		// 	$nx = TkExt::model()->normal()->find('id>'.$id);
		// 	$nx && $nextid = $nx->id;
		// 	$be = TkExt::model()->normal()->find('id<'.$id);
		// 	$be && $preid = $be->id;
		// }
		$nx = TkExt::model()->normal()->find('id>'.$id);
		$nx && $nextid = $nx->id;
		$be = TkExt::model()->normal()->find('id<'.$id);
		$be && $preid = $be->id;
		// $this->render('info',['info'=>$info,'rights'=>$this->rights]);
		$this->render('imageinfo',['info'=>$info,'rights'=>$this->rights,'nextid'=>$nextid,'preid'=>$preid]);
	}

	public function actionSetPraise($id='')
	{
		if(!$this->user) {
			echo json_encode(['msg'=>'请登陆后操作','s'=>'error']);
		} else {
			$uid = $this->user->id;
			if(Yii::app()->db->createCommand("select id from praise where uid=$uid and cid=$id")->queryScalar()) {
				echo json_encode(['msg'=>'您已点过赞','s'=>'error']);
			} else {
				$praise = new PraiseExt;
				$praise->uid = $uid;
				$praise->cid = $id;
				$praise->save();
				$info = CommentExt::model()->findByPk($id);
				$info->praise += 1;
				$info->save();
				echo json_encode(['num'=>$info->praise,'s'=>'success']);
			}
		}
	}
}