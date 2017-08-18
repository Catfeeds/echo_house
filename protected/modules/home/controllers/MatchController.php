<?php 
class MatchController extends HomeController{
	public function actionIndex($time='',$lid='')
	{
		$t = SiteExt::getAttr('seo','home_match_index_title');
        $k = SiteExt::getAttr('seo','home_match_index_keyword');
        $d = SiteExt::getAttr('seo','home_match_index_desc');
        $t && $this->pageTitle = $t;
        $k && $this->keyword = $k;
        $d && $this->description = $d;
		$criteria = new CDbCriteria;
		if($time) {
			$time = strtotime($time);
			$criteria->addCondition('time>=:t1 and time<:t2');
			$criteria->params[':t1'] = TimeTools::getDayBeginTime($time);
			$criteria->params[':t2'] = TimeTools::getDayEndTime($time);
		} else {
			$criteria->addCondition('time>=:t1');
			$criteria->params[':t1'] = TimeTools::getDayBeginTime(time()-86400);
		}
		if($lid) {
			$criteria->addCondition('lid=:lid');
			$criteria->params[':lid'] = $lid;
		}
		$matchs = MatchExt::model()->normal()->findAll($criteria);
		if($this->iswap)
			$this->render('wapindex',['matchs'=>$matchs,'time'=>$time,'lid'=>$lid]);
		else
			$this->render('index',['matchs'=>$matchs,'time'=>$time,'lid'=>$lid]);
	}
}