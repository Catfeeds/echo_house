<?php
/**
 * pc楼盘列表页右侧帮你找房报名表单
 * 报名订单除了order表之外的字段全部统一整理成文字放在note备注字段中
 * 请勿更新user表的相关字段，user表中的数据都是需要小编回访后确认的信息，不能随意覆盖。
 * @author tivon
 * @version 2016-10-13
 */
class PlotListSideFindWidget extends CWidget
{
    public $allArea = [];

    public function init()
    {

    }

    public function run()
    {
        if($this->allArea) {
            $this->allArea = AreaExt::model()->frontendShow()->findAll(['index'=>'id']);
        }
        $area = [];
        foreach($this->allArea as $v) {
            if($v->getIsFirstLevel()) {
                $area[$v->name] = $v->name;
            }
        }
        $this->allArea = $area;
        $this->render('plotListSideFind');
    }
}
