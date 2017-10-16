<?php
/**
 * 错误处理控制器
 * @author weibaqiu
 * @date 2015-12-28
 */
class ErrorController extends HomeController
{
    /**
     * 控制处理接收处理方法
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
		{
            $this->render('error',['code'=>$error['code']]);
			// if($error['code']==404){
   //              $this->redirect(array('/home/index/error'));
   //          }else{
   //              echo $error['code'];
   //          }
		}
    }
}
