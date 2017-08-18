<?php
/**
 * 更新脚本
 */
class HuxingCommand extends CConsoleCommand
{
    public function actionRun($p)
    {
        if($p==='newhouse'){
            //转换相册数据
            $this->convertHouseType();
            $this->prompt('转换完成');
        } else {
            $this->prompt('升级口令错误');
        }
    }

    public function convertHouseType()
    {
        $offset = 0;
        $cate = $arr = TagExt::model()->find('name="户型图"');
        if(!$cate){
            $this->prompt('户型分类未找到');
            Yii::app()->end();
        }
        $count = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => 100,
            'offset' => $offset * 100,
            'condition' => 'type='.$cate->id,
            'order' => 'id asc',
        ));
        $houseTypes = PlotImgExt::model()->findAll($criteria);
        if($houseTypes){
            foreach($houseTypes as $row){
                $arr = $this->findWord($row->title);
                if($row->size>0){
                    $arr['size'] = $row->size;
                }
                if($row->room>0){
                    $arr['room'] = $row->room;
                }
                $data = array(
                    'hid' => $row->hid,
                    'title' => $row->title,
                    'image' => $row->url,
                    'bedroom' => $arr['shi'],
                    'livingroom' => $arr['ting'],
                    'bathroom' => $arr['wei'],
                    'cookroom' => $arr['chu'],
                    'size' => $arr['size'],
                    'is_cover' => $row->is_cover,
                    'sort' => $row->sort,
                    'created' => $row->created,
                    'updated' => $row->updated,
                );
                $model = new PlotHouseTypeExt;
                $model->attributes = $data;
                $model->save();
                $count++;
            }
            echo '[offset:'.$offset++.']已完成'.$count.'条'."\n";
            goto begin;
        }
        $this->prompt('已结束');
    }

    /**
     * 抓取标题中参数
     * @return array
     */
    public function findWord($title)
    {
        $arr = ['shi'=>0,'ting'=>0,'wei'=>0,'chu'=>0,'size'=>0];
        $pattern = array(
            '/一/','/二/','/两/','/三/','/四/','/五/','/六/','/七/','/八/','/九/','/十/','/㎡/'
        );
        $num = array(
            '1','2','2','3','4','5','6','7','8','9','10','平'
        );
        if(!preg_match('/(\d+)房/', $title, $shi)) {//处理特殊情况"卢森堡 A7三房113.73㎡"，下同
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)房/', $title2, $shi);
        }
        if(isset($shi[1])){
            $arr['shi'] = (int)$shi[1];
        }

        if(!preg_match('/(\d+)室/', $title, $shi)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)室/', $title2, $shi);
        }
        if(isset($shi[1])){
            $arr['shi'] = (int)$shi[1];
        }

        if(!preg_match('/(\d+)厅/', $title, $ting)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)厅/', $title2, $ting);
        }
        if(isset($ting[1])){
            $arr['ting'] = (int)$ting[1];
        }

        if(!preg_match('/(\d+)卫/', $title, $wei)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)卫/', $title2, $wei);
        }
        if(isset($wei[1])){
            $arr['wei'] = (int)$wei[1];
        }

        if(!preg_match('/(\d+)厨/', $title, $chu)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)厨/', $title2, $chu);
        }
        if(isset($chu[1])){
            $arr['chu'] = (int)$chu[1];
        }
        if(!preg_match('/(\d+(\.\d+)?)平/', $title, $size)) {
            preg_match('/(\d+(\.\d+)?)㎡/', $title, $size);
        }
        if(isset($size[1])){
            $arr['size'] = $size[1];
        }
        return $arr;
    }
}
