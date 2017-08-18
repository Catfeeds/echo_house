<?php
class DataController extends HomeController{
	public function actionIndex($lid='',$type='',$land='')
	{
		$t = SiteExt::getAttr('seo','home_data_index_title');
        $k = SiteExt::getAttr('seo','home_data_index_keyword');
        $d = SiteExt::getAttr('seo','home_data_index_desc');
        $t && $this->pageTitle = $t;
        $k && $this->keyword = $k;
        $d && $this->description = $d;
		$liarr = ['p'=>'积分榜','s'=>'射手榜','a'=>'助攻榜','t'=>'联赛赛制'];
		$criteria = new CDbCriteria;
		if($land) {
			$criteria->addCondition('land=:land');
			$criteria->params[':land'] = $land;
		}
		$leas = LeagueExt::model()->normal()->findAll($criteria);
		!$lid && $lid = $leas[0]['id'];
		$criteria = new CDbCriteria;
		$criteria->limit = 20;
		
		$type = $type?$type:array_keys($liarr)[0];
		switch ($type) {
			case 'p':
				if($lid) {
					$criteria->addCondition('lid=:lid');
					$criteria->params[':lid'] = $lid;
				}
				$criteria->order = 'points desc';
				$points = PointsExt::model()->normal()->findAll($criteria);
				break;
			case 's':
				if($lid) {
					$criteria->addCondition('t.lid=:lid');
					$criteria->params[':lid'] = $lid;
				}
				$criteria->order = 't.score desc';
				$points = PlayerDataExt::model()->with('player')->normal()->findAll($criteria);
				break;
			case 'a':
				if($lid) {
					$criteria->addCondition('t.lid=:lid');
					$criteria->params[':lid'] = $lid;
				}
				$criteria->order = 't.assist desc';
				$points = PlayerDataExt::model()->with('player')->normal()->findAll($criteria);
				break;
			case 't':
				$points = LeagueExt::model()->findByPk($lid);
				break;
			
			default:
				# code...
				break;
		}
		if($this->iswap==0)
			$this->render('index',['leas'=>$leas,'lid'=>$lid,'points'=>$points,'type'=>$type?$type:array_keys($liarr)[0],'liarr'=>$liarr,'land'=>$land]);
		else
			$this->render('wapindex',['leas'=>$leas,'lid'=>$lid,'points'=>$points,'type'=>$type?$type:array_keys($liarr)[0],'liarr'=>$liarr,'land'=>$land]);
	}
}