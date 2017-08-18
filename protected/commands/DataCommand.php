<?php
/**
 * 数据迁移脚本
 * @author tivon
 * @date 2015-10-09
 */
class DataCommand extends CConsoleCommand
{
	/**
	 * [actionLeague 联赛脚本]
	 * @param  integer $id   [description]
	 * @param  integer $page [description]
	 * @return [type]        [description]
	 */
	public function actionLeague($id=0,$page=0)
	{
		begin:

		$url = SiteExt::getAttr('qjpz','leagueApi');
		// if($id)
		$res = HttpHelper::get($url.'?id='.$id.'&page='.$page);
		if(isset($res['content']) && $data = json_decode($res['content'],true)) {
			if($data) {
				foreach ($data as $key => $value) {
					// var_dump($value);exit;
					$no = $value['no'];
					if(Yii::app()->db->createCommand("select id from league where old_id=$no")->queryScalar()) {
						continue;
					} 
					$league = new LeagueExt;
					$league->old_id = $value['no'];
					foreach (['name','country','image','land'] as $v) {
						$league->$v = $value[$v];
					}
					// $league->attributes = $value;
					if(!$league->save()) {
						echo current(current($league->getErrors()));
					}
				}
			}
			echo "finishing 100*$page=============\n";
			$page++;
			goto begin;
		} else {
			echo "finished==================\n";
		}
	}

	/**
	 * [actionNews 资讯]
	 * @param  integer $id   [description]
	 * @param  string  $date [description]
	 * @return [type]        [description]
	 */
	public function actionNews($id=0,$date='')
	{
		$page = 0;
		if(!$date) {
			$date = date('Ymd',time());
		}
		begin:

		$url = SiteExt::getAttr('qjpz','newsApi');
		// if($id)
		$res = HttpHelper::get($url.'?id='.$id.'&page='.$page.'&time='.$date);
		if(isset($res['content']) && $data = json_decode($res['content'],true)) {
			// $count = count($data);
			// for ($i=0; $i < $count/200 + 1; $i++) { 
				foreach ($data as $key => $value) {
					// var_dump($url.'?id='.$id.'&page='.$page.'&time='.date('Ymd',time()),$value);exit;
					// $name = $value['name'];
					if(!$value)
						continue;
					$cname = $value['league'];
					
					// $cid = Yii::app()->db->createCommand("select id from article_cate where name='$cname' and status=1 and deleted=0")->queryScalar();

					// if(!$cid && $cname) {
					// 	$catemodel = new ArticleCateExt;
					// 	$catemodel->name = $cname;
					// 	$catemodel->status = 1;
					// 	$catemodel->save();
					// 	$cid = $catemodel->id;
					// }

					$no =  $value['no'];
					if(Yii::app()->db->createCommand("select id from article where old_id=$no")->queryScalar())
						continue;
					if(Yii::app()->db->createCommand("select id from article where title='".$value['title']."'")->queryScalar())
						continue;
					$league = new ArticleExt;
					$league->old_id = $value['no'];
					foreach (['old_id'=>'no','image'=>'coverPhoto','content'=>'content','title'=>'title','created'=>'createtime','descpt'=>'intruduction'] as $k => $v) {
						$league->$k = $value[$v];
					}
					$league->created = strtotime($league->created);
					// $league->status = 1;
					// var_dump($cid);exit;
					// $cid && $league->cid = $cid;
					// var_dump($league->attributes);exit;
					// $league->attributes = $value;
					if(!$league->save()) {
						echo current(current($league->getErrors()));
					}
				}
				echo "finishing 100*$page=============";
				$page++;
				goto begin;
			// } 
		} else {
			echo "finished==================\n";
		}
		// $this->end();
	}

	/**
	 * [actionTeam 球队]
	 * @param  integer $id   [description]
	 * @param  integer $page [description]
	 * @return [type]        [description]
	 */
	public function actionTeam($id=0,$page=0)
	{
		begin:

		$url = SiteExt::getAttr('qjpz','teamApi');
		// if($id)
		$res = HttpHelper::get($url.'?id='.$id.'&page='.$page);
	        if($res['content'] === '"continue"') {
                        $page++;
                        goto begin;
                }
	        if($res['content'] === '"finish"') {
                        $page++;
                        exit;
		}
		if(isset($res['content']) && $data = json_decode($res['content'],true)) {
			if($data) {
				foreach ($data as $key => $value) {
					// var_dump($value);exit;
					$no = $value['no'];
					$criteria = new CDbCriteria;
					$criteria->addCondition("old_id=:no");
					$criteria->params[':no'] = $no;

					if(!($team = TeamExt::model()->find($criteria))) {
						$team = new TeamExt;
						$team->old_id = $value['no'];
						$team->status = 1;
					}
					foreach (['name','city','coach','image'] as $v) {
						$team->$v = $value[$v];
					}
					if($team->save()) {
						$oldlid = $value['league'];
						$lid = Yii::app()->db->createCommand("select id from league where old_id=$oldlid")->queryScalar();
		  				$tid = $team->id;
					        $criteria = new CDbCriteria;
        	                                $criteria->addCondition("old_id=:no");
	                                        $criteria->params[':no'] = $no;
        	                                $criteria->addCondition("lid=:lid");
	                                        $criteria->params[':lid'] = $lid;
        	                                $criteria->addCondition("year=:schedule");
	                                        $criteria->params[':schedule'] = $value['schedule'];
						if(!($points = PointsExt::model()->find($criteria))) {
							$points = new PointsExt;
						}
						foreach (['score_ball'=>'goal','lose_ball'=>'fumble','points'=>'score','win'=>'win','lose'=>'lose','same'=>'draw','year'=>'schedule','old_id'=>'no','lid'=>'league'] as $k => $v) {
							$points->$k = $value[$v];
						}
						$points->lid = $lid;
						$points->tid = $tid;
						$points->status = 1;
						// var_dump($points->attributes);exit;
						if(!$points->save()) {
							echo current(current($points->getErrors()));
						}
					} else {
						echo current(current($team->getErrors()));
					}
				}
			}
			echo "finishing 100*$page=============\n";
			$page++;
			goto begin;
		} else {
			echo "finished==================\n";
		}
	}

	public function actionMatch($date='')
	{
		begin:
		!$date && $date = date('Ymd',time());
		$url = SiteExt::getAttr('qjpz','matchApi');
		// if($id)
		$res = HttpHelper::get($url.'?date='.$date);
		// var_dump($url.'?date='.$date);exit;
		if(isset($res['content']) && $data = json_decode($res['content'],true)) {
			if($data) {
				foreach ($data as $key => $value) {
					if(Yii::app()->db->createCommand("select id from `match` where old_id=".$value['no'])->queryScalar())
						continue;
					if(!($lid = Yii::app()->db->createCommand("select id from league where old_id=".$value['leagueId'])->queryScalar()))
						continue;
					if(!($home = Yii::app()->db->createCommand("select id,name from team where old_id=".$value['homeId'])->queryRow()))
						continue;
					if(!($visit = Yii::app()->db->createCommand("select id,name from team where old_id=".$value['visitorId'])->queryRow()))
						continue;
					$model = new MatchExt;
					$model->lid = $lid;
					$model->home_id = $home['id'];
					$model->home_name = $home['name'];
					$model->visitor_id = $visit['id'];
					$model->visitor_name = $visit['name'];
					$model->old_id = $value['no'];
					$model->status = 1;
					$model->time = substr($value['time'], 0,-3);
					// var_dump($model->time);exit;
					$value['visitor_score'] = $value['visitorScore'];
					$value['home_score'] = $value['homeScore'];
					foreach (['visitor_score','home_score'] as $k => $v) {
						$model->$v = $value[$v];
					}
					if(!$model->save()) { 
						echo current(current($model->getErrors()));
					}
				}
			}
			echo "finished===============\n";
			// $page++;
			// goto begin;
		} else {
			echo "finished==================\n";
		}
	}
}
