<?php
class MyController extends WapController{
    public function actionIndex()
    {
        $this->render('index',['staff'=>$this->staff]);
    }
}