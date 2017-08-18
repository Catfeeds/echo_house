<?php
/**
 * 数据迁移脚本
 * @author tivon
 * @date 2015-10-09
 */
class ExtraCommand extends CConsoleCommand
{
    public function actionPlotSchoolDistance()
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'order' => 'id asc',
            'limit' => 100,
            'offset' => 100*$page,
        ));
        $data = SchoolPlotRelExt::model()->findAll($criteria);
        if($data){
            foreach($data as $v)
            {
                $v->save();
            }
            unset($data);
            echo $page++."\n";
            goto begin;
        }
        echo 'finished';
    }

    public function actionWatermark()
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => '50',
            'offset' => $page*50,
            'order' => 'id asc',
            'condition' => '`created`<1454554391 AND `url` like "201%"',//将2016/02/04 10：53那次修改前的图片打上持久水印
        ));
        $images = PlotImgExt::model()->findAll($criteria);
        if($images){
            foreach($images as $v){
                $url = Yii::app()->file->waterMark($v->url);
                if($url&&$url!=$v->url){
                    PlotImgExt::model()->updateByPk($v->id, array('url'=>$url));
                }
            }
            unset($images);
            echo $page++."\n";
            goto begin;
        }
        echo 'finished';
    }

    public function actionArticleCateInit()
    {
        ArticleCateExt::model()->updateAll(array('config'=>31));
        echo 'finished';
    }

    public function actionRun()
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => 10,
            'offset' => 10*$page
        ));
        $kans = PlotKanExt::model()->findAll($criteria);
        if($kans){
            foreach($kans as $v){
                $v->save();
            }
            $page++;
            goto begin;
        }
        echo 'finished';
    }
}
