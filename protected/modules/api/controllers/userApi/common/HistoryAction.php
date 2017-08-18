<?php
/**
 * 浏览纪录
 * User: jt
 * Date: 2016/10/14 11:55
 */

class HistoryAction extends CAction{

    public function run(){
        $view_record = new ViewRecordBehavior();
        $data = $view_record->getViewRecord();
        return $this->getController()->frame['data'] = $data;
    }

}