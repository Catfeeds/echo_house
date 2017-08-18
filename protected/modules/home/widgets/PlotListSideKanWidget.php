<?php
/**
 * pc楼盘列表页右侧看房团活动数据列表
 * @author tivon
 * @version 2016-10-13
 */
class PlotListSideKanWidget extends CWidget
{
    public function init()
    {

    }

    public function run()
    {
        $criteria = new CDbCriteria(array(
            'condition' => 'expire > :expire',
            'order' => 'sort desc,created desc',
            'params' => array(':expire'=>time()),
            'limit' => 20,
        ));
        $data = PlotKanExt::model()->normal()->findAll($criteria);
        if($data) {
            $this->render('plotListSideKan', ['data'=>$data]);
        }
    }
}
