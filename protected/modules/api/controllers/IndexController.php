<?php
class IndexController extends ApiController
{
    public function actionIndex($cid=0)
    {
        $this->showUser();
        $this->redirect('/subwap/list.html');
    }

    public function actionPlace()
    {
        $this->showUser();
        $this->redirect('/subwap/customerlist.html');
    }


    public function showUser()
    {
        $key = '495e6105d4146af1d36053c1034bc819';
        $uid = $this->showUid();
        if($uid) {
            $url = 'http://jj58.qianfanapi.com/api1_2/user/user-info';
            $res = $this->get_response($key,$url,['user_ids'=>$uid]);
            if($res) {
                $res = json_decode($res,true);
                $data = $res['data'][$uid];
                setcookie('phone',$data['user_phone']);
                if($data['user_phone'] && $user = UserExt::model()->normal()->find("phone='".$data['user_phone']."'")) {
                    $model = new ApiLoginForm();
                    $model->isapp = true;
                    $model->username = $user->phone;
                    $model->password = $user->pwd;
                    // $model->obj = $user->attributes
                    $model->login();
                } else {
                	Yii::app()->user->logout();
                }
            }
        }
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

    public function showUid()
    {
        if(empty($_COOKIE['wap_token'])) {
            return '';
        } else {
            $token = $_COOKIE['wap_token'];
        }
        $url = 'http://jj58.qianfanapi.com/api1_2/cookie/auth-code';
        $key = '495e6105d4146af1d36053c1034bc819';
        $postArr = ['wap_token'=>$token,'secret_key'=>$key];
        $res = $this->get_response($key,$url,[],$postArr);
        $res = json_decode($res,true);
        setcookie('qf_uid',$res['uid']);
        if(!$this->staff->qf_uid) {
            $this->staff->qf_uid = $res['uid'];
            $this->staff->save();
        }
        // !$this->staff->qf_uid && $this->staff->qf_uid = $res['uid'];

        return $res['uid'];
    }

    public function actionGetQfUid()
    {
        $this->showUser();
        if(!empty($_COOKIE['qf_uid'])) {
            $this->frame['data'] = ['uid'=>$_COOKIE['qf_uid'],'phone'=>$_COOKIE['phone']];
        } else {
            $this->returnError('UID错误');
        }
    }
}
