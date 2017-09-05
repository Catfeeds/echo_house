<?php
/**
 * 首页控制器
 */
class IndexController extends HomeController
{
    public function actionIndex($cid=0)
    {
        // $this->showUser();
        $this->redirect('/subwap/list.html');
    }

    public function actionShowUser()
    {
        $key = '495e6105d4146af1d36053c1034bc819';
        $uid = $this->showUid();
        if($uid) {
            $url = 'http://jj58.qianfanapi.com/api1_2/user/user-info';
            $res = $this->get_response($key,$url,['user_ids'=>$uid]);
            if($res) {
                $res = json_decode($res,true);
                $data = $res['data'][$uid];
                var_dump($data);
                if($data['user_phone'] && $user = UserExt::model()->normal()->find("phone='".$data['user_phone']."'")) {
                    var_dump($user->phone);
                    $model = new ApiLoginForm();
                    $model->username = $user->phone;
                    $model->pwd = md5($user->pwd);
                    var_dump($model->login());
                }
            }
        }exit;
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
        return $res['uid'];
    }

    public function get_response($secret_key, $url, $get_params, $post_data = array())
    {
        $nonce         = rand(10000, 99999);
        $timestamp  = time();
        $array = array($nonce, $timestamp, $secret_key);
        sort($array, SORT_STRING);
        $token = md5(implode($array));
        $params['nonce'] = $nonce;
        $params['timestamp'] = $timestamp;
        $params['token']     = $token;
        $params = array_merge($params,$get_params);  
        $url .= '?';
        foreach ($params as $k => $v) 
        {
            $url .= $k .'='. $v . '&';
        }
        $url = rtrim($url,'&');   
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);   
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);   
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);   
        curl_setopt($curlHandle, CURLOPT_POST, count($post_data));  
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_data);  
        $data = curl_exec($curlHandle);    
        $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);    
        return $data;
    }
}
