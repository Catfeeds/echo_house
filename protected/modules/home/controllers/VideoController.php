<?php
class VideoController extends HomeController{

	public $cates = [];

	public $rights = [];

	public function init()
	{
		parent::init();
		$this->cates = CHtml::listData(ArticleCateExt::model()->normal()->findAll(),'id','name');
		// 热门推荐
        $rmtjs = RecomExt::getObjFromCate(2,6);
        // var_dump($rmtjs);exit();
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
        $comms = CommentExt::model()->normal()->findAll(['limit'=>10]);
        $this->rights = ['leas'=>$leas,'points'=>$points,'rmtjs'=>$rmtjs,'comms'=>$comms];
	}
	public function actionList($cid='')
	{
		$t = SiteExt::getAttr('seo','home_video_list_title');
        $k = SiteExt::getAttr('seo','home_video_list_keyword');
        $d = SiteExt::getAttr('seo','home_video_list_desc');
        $t && $this->pageTitle = $t;
        $k && $this->keyword = $k;
        $d && $this->description = $d;
		$criteria = new CDbCriteria;
		if($cid) {
			$criteria->addCondition('cid=:cid');
			$criteria->params[':cid'] = $cid;
		}
		$datas = ArticleExt::model()->isvideo()->getList($criteria,20);
		$infos = $datas->data;
		$pager = $datas->pagination;
		
        // var_dump($this->cates);exit;
		$this->render('list',['infos'=>$infos,'pager'=>$pager,'cid'=>$cid,'cates'=>$this->cates,'rights'=>$this->rights]);
	}

	public function actionInfo($id='')
	{
		$t = SiteExt::getAttr('seo','home_video_info_title');
        $k = SiteExt::getAttr('seo','home_video_info_keyword');
        $d = SiteExt::getAttr('seo','home_video_info_desc');
        $info = ArticleExt::model()->findByPk($id);
		$info->hits += 1;
        foreach (['{site}'=>'球布斯','{title}'=>$info->title] as $key => $value) {
	        	$t && $t = $this->pageTitle = str_replace($key, $value, $t);
		        $k && $k = $this->keyword = str_replace($key, $value, $k);
		        $d && $d = $this->description = str_replace($key, $value, $d);
	        }
		// var_dump($this->user);exit;
		
		$info->save();
		$this->render('info',['info'=>$info,'rights'=>$this->rights]);
	}
}