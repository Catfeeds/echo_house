<?php

/**
 * User: fanqi
 * Date: 2016/9/7
 * Time: 10:42
 */
class DaterangepickerWidget extends CWidget
{
    private $time;
    private $params = ['class'=>'form-control','readOnly'=>true];

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setParams($params){
        $this->params = array_merge($this->params,$params);
    }

    public function run()
    {
        $this->render("daterangepicker",['time'=>$this->time,'params'=>$this->params]);
        Yii::app()->clientScript->registerScriptFile('/static/global/scripts/daterangepicker.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerCssFile('/static/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css');
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-daterangepicker/moment.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-daterangepicker/daterangepicker.js', CClientScript::POS_END);
    }
}