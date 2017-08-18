<?php
/**
 * 问答提交的api
 * @author weibaqiu
 * @date 2015-11-10
 */
class AskController extends ApiController
{   
    /**
     * ajax提交问答
     * 提交方式：Ajax\POST
     * 接收参数：
     *         question 问题[必须]
     *         name 姓名[可选]
     *         phone 电话[必须]
     *         hid 楼盘页面提交的话，则记录楼盘id[可选]
     */
    public function actionAjaxSubmit()
    {
        if(Yii::app()->request->getIsAjaxRequest())
        {
            $model = new AskExt;
            $model->question = Yii::app()->request->getPost('question');
            $model->hid = Yii::app()->request->getPost('hid', 0);
            $model->name = Yii::app()->request->getPost('name', '');
            $model->phone = Yii::app()->request->getPost('phone',0);
            if($model->save())
                $this->response(true, '提交成功！');
            else
                $this->response(false, '提交失败！');
        }
        else
            $this->response(false,'提交失败！');
    }

    public function actionTest()
    {
        // var_dump( Yii::app()->mRedis);exit;
        // Yii::app()->mRedis->hSet('test1','id',1);
        // var_dump(Yii::app()->mRedis->hGetAll('test1'));
       // $redis = new Redis();
       // $redis->connect('127.0.0.1', 6379);
       // echo "Connection to server sucessfully";
       //       //查看服务是否运行
       // echo "Server is running: " . $redis->ping();
    }
}
?>

