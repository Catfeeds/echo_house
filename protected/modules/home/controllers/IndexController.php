<?php
/**
 * 首页控制器
 */
class IndexController extends HomeController
{
    public function actionIndex($cid=0)
    {
        // seo
        $t = SiteExt::getAttr('seo','home_index_index_title');
        $k = SiteExt::getAttr('seo','home_index_index_keyword');
        $d = SiteExt::getAttr('seo','home_index_index_desc');
        $t && $this->pageTitle = $t;
        $k && $this->keyword = $k;
        $d && $this->description = $d;
        // 三场比赛
        $matchs = MatchExt::model()->normal()->findAll(['limit'=>3]);
        // 所有分类
        $cates = ArticleCateExt::model()->normal()->findAll(['limit'=>8]);
        
        // if(!$cid) {
        //     isset($cates[0]) && $cid = $cates[0]['id'];
        // }\
        $news = [];
        if($cates) {
            foreach ($cates as $key => $value) {
                $criteria = new CDbCriteria;
                $criteria->limit = 5;
                $criteria->addCondition('cid=:cid');
                $criteria->params[':cid'] = $value['id'];
                if($infos = ArticleExt::model()->normal()->findAll($criteria))
                    $news[$value->id] = $infos;
            }
            
        }
        // 精品导读
        $jpdds = RecomExt::getObjFromCate(1,2);
        // 热门推荐
        $rmtjs = RecomExt::getObjFromCate(2,10);
        
        // $news = ArticleExt::model()->normal()->findAll($criteria);
        // 三个联赛
        $leas = LeagueExt::model()->normal()->findAll(['limit'=>5]);
        // 三个视频
        $videos = ArticleExt::model()->isvideo()->findAll(['limit'=>3]);
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
        // var_dump($points[0]);exit;
        // 十个评论
        $comms = CommentExt::model()->normal()->findAll(['limit'=>10,'order'=>'praise desc, created asc']);
        $rights = ['leas'=>$leas,'points'=>$points,'rmtjs'=>$rmtjs,'comms'=>$comms,'matchs'=>$matchs];
        $this->render('index',['matchs'=>$matchs,'cates'=>$cates,'cid'=>$cid,'news'=>$news,'jpdds'=>$jpdds,'rights'=>$rights,'videos'=>$videos]);
    }

    public function actionAbout()
    {
        $info = SiteExt::getAttr('qjpz','about');
        // var_dump($info);exit;
        $this->render('about',['info'=>$info]);
    }

    public function actionContact()
    {
        $info = SiteExt::getAttr('qjpz','contact');
        // var_dump($info->attributes);exit;
        $this->render('contact',['info'=>$info]);
    }
    public function actionTest($name='')
    {
        Yii::app()->db->createCommand("delete from article_tag where name='$name' or name='测试'")->execute();
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if($error['code']==404){
                $this->redirect(array('/home/index/index'));
            }else{
                echo $error['code'];
            }
        } 
        
    }
}
