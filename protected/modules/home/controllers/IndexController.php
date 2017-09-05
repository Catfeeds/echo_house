<?php
/**
 * 首页控制器
 */
class IndexController extends HomeController
{
    public function actionIndex($cid=0)
    {
        $this->redirect('/subwap/list.html');
    }

    public function actionAbout()
    {
        $info = SiteExt::getAttr('qjpz','about');
        // var_dump($info);exit;
        $this->render('about',['info'=>$info]);
    }

    public function actionContact()
    {
        $info = SiteExt::getAttr('qjpz','contact');
        // var_dump($info->attributes);exit;
        $this->render('contact',['info'=>$info]);
    }
    public function actionTest($name='')
    {
        Yii::app()->db->createCommand("delete from article_tag where name='$name' or name='测试'")->execute();
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if($error['code']==404){
                $this->redirect(array('/home/index/index'));
            }else{
                echo $error['code'];
            }
        } 
        
    }

    public function actionShowCoo()
    {
        $token = $_COOKIE['wap_token'];
        $url = 'http://jj58.qianfanapi.com/api1_2/cookie/auth-code';
        $res = $this->post($url,['wap_token'=>$token,'secret_key'=>'495e6105d4146af1d36053c1034bc819']);
        var_dump($res);exit;
    }
}
