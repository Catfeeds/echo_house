<?php
class MyController extends WapController{
    public function init()
    {
        parent::init();
        $this->layout = '/layouts/base';
    }
    public function actionIndex()
    {
        $this->render('index',['staff'=>$this->staff]);
    }
}