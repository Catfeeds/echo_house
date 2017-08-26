<?php
/**
 * Project: sample
 * User: chenzhidong
 * Date: 14-6-11
 * Time: 20:20
 */ 
class ApiController extends Controller{
	/**
    * [$frame 框架]
    * @var array
    */
    public $frame = [];
    public $staff = [];
	public $layout = 'api.views.layouts.main';
	public function init()
    {
       parent::init();
       $this->frame = $this->rapFrame();
       if(!Yii::app()->user->getIsGuest()) {
            $this->staff = UserExt::model()->findByPk(Yii::app()->user->id);
       }
    }
    public function afterAction($action)
    {
    	// parent::afterAction();
    	header("Content-Type: application/json");
        $this->frame['status']=='success' && !$this->frame['msg'] && $this->frame['msg'] = '操作成功';
        echo CJSON::encode($this->frame);
        Yii::app()->end();
    }
    public function returnError($msg){
        $this->frame['status'] = 'error';
        $this->frame['msg'] = $msg;
        return false;
    }
    public function returnSuccess($msg){
        $this->frame['status'] = 'success';
        $this->frame['msg'] = $msg;
        return false;
    }
}