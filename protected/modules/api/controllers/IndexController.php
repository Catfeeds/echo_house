<?php

class IndexController extends ApiController
{
    public function actionIndex($cid=0,$area='')
    {
        $this->showUser();
        if($area) {
            $this->redirect('/subwap/list.html?area='.$area);
        }else
            $this->redirect('/subwap/list.html');
    }
    public function actionPub()
    {
        $this->showUser();
        $this->redirect('/subwap/personaladd.html');
    }
    public function actionPublist()
    {
        $this->showUser();
        $this->redirect('/subwap/personallist.html');
    }
    public function actionRegister()
    {
        $this->showUser();
        $this->redirect('/subwap/register.html');
    }
    public function actionVip()
    {
        $this->showUser();
        $this->redirect('/subwap/duijierennew.html');
    }

    public function actionPlace()
    {
        $this->showUser();
        $this->redirect('/subwap/customerlist.html');
    }
    public function actionSalelist()
    {
        $this->showUser();
        $this->redirect('/subwap/salelist.html');
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
        if($this->staff && !$this->staff->qf_uid) {
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
            if(empty($_COOKIE['phone'])) {
                $this->returnError('请绑定经纪圈手机号');
            } else
                $this->frame['data'] = ['uid'=>$_COOKIE['qf_uid'],'phone'=>$_COOKIE['phone']];
        } else {
            $this->returnError('UID错误');
        }
    }

    public function actionXcxLogin()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $phone = Yii::app()->request->getPost('phone','');
            $openid = Yii::app()->request->getPost('openid','');
            if(!$phone||!$openid) {
                $this->returnError('参数错误');
                return false;
            }
            if($phone) {
                $user = UserExt::model()->find("phone='$phone'");
            } elseif($openid) {
                $user = UserExt::model()->find("openid='$openid'");
            }
        // $phone = '13861242596';
            if($user) {
                if($openid&&$user->openid!=$openid){
                    $user->openid=$openid;
                    $user->save();
                }
                
            } else {
                $user = new UserExt;
                $user->phone = $phone;
                $user->openid = $openid;
                $user->name = $this->get_rand_str();
                $user->status = 1;
                $user->is_true = 0;
                $user->type = 3;
                $user->pwd = md5('jjqxftv587');
                $user->save();

                // $this->returnError('用户尚未登录');
            }
            $model = new ApiLoginForm();
            $model->isapp = true;
            $model->username = $user->phone;
            $model->password = $user->pwd;
            // $model->obj = $user->attributes
            $model->login();
            $this->staff = $user;
            $data = [
                'id'=>$this->staff->id,
                'phone'=>$this->staff->phone,
                'name'=>$this->staff->name,
                'type'=>$this->staff->type,
                'is_true'=>$this->staff->is_true,
                'company_name'=>$this->staff->companyinfo?$this->staff->companyinfo->name:'独立经纪人',
            ];
            $this->frame['data'] = $data;
        }
    }

    public function actionGetUserInfo()
    {
        if(!Yii::app()->user->getIsGuest()) {
            $data = [
                'id'=>$this->staff->id,
                'phone'=>$this->staff->phone,
                'name'=>$this->staff->name,
                'type'=>$this->staff->type,
                'is_user'=>$this->staff->is_user,
                'company_name'=>$this->staff->is_user==1?($this->staff->companyinfo?$this->staff->companyinfo->name:'独立经纪人'):'您尚未实名认证',
            ];
            $this->frame['data'] = $data;
        } else {
            $this->returnError('用户尚未登录');
        }
    }

    public function actionAddCo()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $hid = Yii::app()->request->getPost('hid','');
            $uid = Yii::app()->request->getPost('uid','');
            $phone = Yii::app()->request->getPost('phone','');
            $name = Yii::app()->request->getPost('name','');
            $userphone = Yii::app()->request->getPost('userphone','');
            $usercompany = Yii::app()->request->getPost('usercompany','');
            $plot = PlotExt::model()->findByPk($hid);
            if(!$uid) {
                if($usercompany && !($com = CompanyExt::model()->normal()->find("name='$usercompany'"))) {
                    $com = new CompanyExt;
                    $com->name = $usercompany;
                    $com->type = 2;
                    $com->status = 1;
                    $com->phone = $userphone;
                    $com->save();
                }
                if(!($user = UserExt::model()->normal()->find("phone='$phone'"))){
                    $user = new UserExt;
                    $user->name = $name;
                    $user->type = $usercompany?2:3;
                    $user->pwd = md5('jjqxftv587');
                    $user->status = 1;
                    $user->cid = $usercompany?$com->id:0;
                    $user->save();
                }
            } else {
                $user = UserExt::model()->findByPk($uid);
            }
            if($user->type>1 && $plot && !Yii::app()->db->createCommand("select id from cooperate where deleted=0 and uid=$uid and hid=$hid")->queryScalar()) {
                    $company = $user->companyinfo?$user->companyinfo->name:'';
                    
                    $obj = new CooperateExt;
                    // $obj->attributes = $tmp;
                    $obj->com_phone = $phone;
                    $obj->hid = $hid;
                    $obj->uid = $user->id;
                    $obj->status = 0;
                    if($obj->save()) {
                        SmsExt::sendMsg('分销',$phone,['staff'=>$company.$user->name.$user->phone,'plot'=>$plot->title]);
                        $noticeuid = Yii::app()->db->createCommand("select qf_uid from user where phone='".$phone."'")->queryScalar();
                        $noticeuid && Yii::app()->controller->sendNotice('分销合同签约申请：'.$company.$user->name.$user->phone.'，正在经纪圈APP中申请合作（'.$plot->title.'）项目，请尽快联系哦！',$noticeuid);
                    }
                } elseif($user->type<=1) {
                    $this->returnError('您的账户类型为总代公司，不支持申请分销签约');
                } else {
                    $this->returnError('您已经提交申请，请勿重复提交');
                }
            // $tmp['uid'] = $this->staff->id;

        }
    }

    public function actionAddSave($hid='',$uid='')
    {
        if($uid&&$hid) {
            $staff = UserExt::model()->findByPk($uid);
            if($save = SaveExt::model()->find('hid='.(int)$hid.' and uid='.$staff->id)) {
                SaveExt::model()->deleteAllByAttributes(['hid'=>$hid,'uid'=>$staff->id]);
                $this->returnSuccess('取消收藏成功');
            } else {
                $save = new SaveExt;
                $save->uid = $staff->id;
                $save->hid = $hid;
                $save->save();
                $this->returnSuccess('收藏成功');
            }
        }else {
            $this->returnError('请登录后操作');
        }
    }

    public function actionCompleteInfo()
    {
        $name = Yii::app()->request->getPost('name','');
        $userphone = Yii::app()->request->getPost('userphone','');
        $usercompany = Yii::app()->request->getPost('usercompany','');
        $openid = Yii::app()->request->getPost('openid','');
        if($usercompany) {
            if(is_numeric($usercompany)) {
                $com = CompanyExt::model()->find("code='$usercompany'");
            } else {
                $com = CompanyExt::model()->find("name='$usercompany'");
            }
        }
        if($usercompany && !$com) {
            $com = new CompanyExt;
            $com->name = $usercompany;
            $com->type = 2;
            $com->status = 1;
            $com->phone = $userphone;
            if(!$com->save()){
                return $this->returnError(current(current($com->getErrors())));
            }
        }
        if(!($user = UserExt::model()->find("phone='$userphone'"))){
            $user = new UserExt;
        }
        $user->name = $name;
        $user->type = $usercompany?$com->type:3;
        !$user->pwd &&  $user->pwd = md5('jjqxftv587');
        $user->status = 1;
        $user->phone = $userphone;
        $user->openid = $openid;
        $user->is_true = 1;
        $user->cid = $usercompany?$com->id:0;
        if(!$user->save()){
            return $this->returnError(current(current($user->getErrors())));
        }

    }

    public function actionSub()
    {
        $name = Yii::app()->request->getPost('name','');
        $userphone = Yii::app()->request->getPost('userphone','');
        $usercompany = Yii::app()->request->getPost('usercompany','');
        $uid = Yii::app()->request->getPost('uid','');
        if(!$uid) {
            if($usercompany && !($com = CompanyExt::model()->normal()->find("name='$usercompany'"))) {
                $com = new CompanyExt;
                $com->name = $usercompany;
                $com->type = 2;
                $com->status = 1;
                $com->phone = $userphone;
                $com->save();
            }
            if(!($user = UserExt::model()->normal()->find("phone='$phone'"))){
                $user = new UserExt;
                $user->name = $name;
                $user->type = $usercompany?2:3;
                $user->pwd = md5('jjqxftv587');
                $user->status = 1;
                $user->cid = $usercompany?$com->id:0;
                $user->save();
            }
        } else {
            $user = UserExt::model()->findByPk($uid);
        }
        if(($tmp['hid'] = $this->cleanXss($_POST['hid'])) && ($plot = PlotExt::model()->findByPk($_POST['hid'])) && ($tmp['phone'] = $this->cleanXss($_POST['phone']))) {
                $tmp['name'] = $this->cleanXss($_POST['name']);
                $tmp['time'] = strtotime($this->cleanXss($_POST['time']));
                $tmp['sex'] = $this->cleanXss($_POST['sex']);
                $tmp['note'] = $this->cleanXss(Yii::app()->request->getPost('note',''));
                $tmp['visit_way'] = $this->cleanXss($_POST['visit_way']);
                $tmp['is_only_sub'] = $this->cleanXss($_POST['is_only_sub']);
                $tmp['notice'] = $notice = $this->cleanXss($_POST['notice']);
                $tmp['uid'] = $user->id;

                if($user->type<=1) {
                    return $this->returnError('您的账户类型为总代公司，不支持快速报备');
                } 

                if(Yii::app()->db->createCommand("select id from sub where uid=".$tmp['uid']." and hid=".$tmp['hid']." and deleted=0 and phone='".$tmp['phone']."' and created<=".TimeTools::getDayEndTime()." and created>=".TimeTools::getDayBeginTime())->queryScalar()) {
                    return $this->returnError("同一组客户每天最多报备一次，请勿重复操作");
                }
                $obj = new SubExt;
                $obj->attributes = $tmp;
                $obj->status = 0;
                if($tmp['uid']) {
                    $companyname = Yii::app()->db->createCommand("select c.name from company c left join user u on u.cid=c.id where u.id=".$tmp['uid'])->queryScalar();
                    $obj->company_name = $companyname;
                }
                // 新增6位客户码 不重复
                $code = 700000+rand(0,99999);
                // var_dump($code);exit;
                while (SubExt::model()->find('code='.$code)) {
                    $code = 700000+rand(0,99999);
                }
                $obj->code = $code;
                if($obj->save()) {
                    $pro = new SubProExt;
                    $pro->sid = $obj->id;
                    $pro->uid = $user->id;
                    $pro->note = '新增客户报备';
                    $pro->save();
                    SmsExt::sendMsg('客户通知',$user->phone,['pro'=>$plot->title,'pho'=>substr($tmp['phone'], -4,4),'code'=>$code]);
                    
                    $user->qf_uid && Yii::app()->controller->sendNotice('您好，你对'.$plot->title.'的报备已经成功，客户的尾号是'.substr($tmp['phone'], -4,4).'，客户码为'.$code.'，请牢记您的客户码。',$user->qf_uid);

                    if($notice) {
                        $noticename = Yii::app()->db->createCommand("select name from user where phone='$notice'")->queryScalar();
                        SmsExt::sendMsg('报备',$notice,['staff'=>($user->cid?CompanyExt::model()->findByPk($user->cid)->name:'独立经纪人').$user->name.$user->phone,'user'=>$tmp['name'].$tmp['phone'],'time'=>$_POST['time'],'project'=>$plot->title,'type'=>($obj->visit_way==1?'自驾':'班车')]);

                        $noticeuid = Yii::app()->db->createCommand("select qf_uid from user where phone='$notice'")->queryScalar();
                        // $noticeuid && $this->staff->qf_uid && Yii::app()->controller->sendNotice('项目名称：'.$plot->title.'；客户：'.$tmp['name'].$tmp['phone'].'；来访时间：'.$_POST['time'].'；来访方式：'.($obj->visit_way==1?'自驾':'班车').'；业务员：'.($this->staff->cid?CompanyExt::model()->findByPk($this->staff->cid)->name:'独立经纪人').$this->staff->name.$this->staff->phone,$noticeuid);
                        if($noticeuid && $user->qf_uid) {
                            Yii::app()->controller->sendNotice(
                                '报备项目：'.$plot->title.'
客户姓名：'.$tmp['name'].'
客户电话： '.$tmp['phone'].'
公司门店：'.($user->cid?CompanyExt::model()->findByPk($user->cid)->name:'独立经纪人').'
业务员姓名：'.$user->name.'
业务员电话：'.$user->phone.'
市场对接人：'.$noticename.'
对接人电话：'.$notice.'
带看时间：'.$_POST['time'].'
来访方式：'.($obj->visit_way==1?'自驾':'班车'),$noticeuid);
                        }
                            

                    }
                        
                    
                } else {
                    $this->returnError(current(current($obj->getErrors())));
                }
                // }
            }
    }

    public function actionDecode()
    {
        include_once "wxBizDataCrypt.php";
        $appid = 'wxc4b995f8ee3ef609';
        $sessionKey = $_POST['accessKey'];
        $encryptedData = $_POST['encryptedData'];
        $iv = $_POST['iv'];
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $data = json_decode($data,true);
            $this->frame['data'] = $data['phoneNumber'];
            echo $data['phoneNumber'];
            Yii::app()->end();
            // print($data . "\n");
        } else {
            echo '';
            Yii::app()->end();
        }
    }

    public function getSessionKey($code='' )
    {
        $appid='wxc4b995f8ee3ef609';$apps='48d79f6b24890a88ef5b53a5e5119f5a';
        $res = HttpHelper::get("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");
        if($res){
            var_dump("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");exit;
            return $res['content']['session_key'];
        }

    }

    public function actionGetOpenId($code='')
    {
        $appid='wxc4b995f8ee3ef609';$apps='48d79f6b24890a88ef5b53a5e5119f5a';
        // $res = HttpHelper::get("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");
        $res = HttpHelper::getHttps("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$apps&js_code=$code&grant_type=authorization_code");
        if($res){
            $cont = $res['content'];
            if($cont) {
                $cont = json_decode($cont,true);
                $openid = $cont['openid'];
                if($openid) {
                    $user = UserExt::model()->find("openid='$openid'");
                    if($user&&$user->is_true==1) {
                        $data = [
                            'id'=>$user->id,
                            'phone'=>$user->phone,
                            'name'=>$user->name,
                            'type'=>$user->type,
                            'is_true'=>$user->is_true,
                            'company_name'=>$user->companyinfo?$user->companyinfo->name:'独立经纪人',
                        ];
                        echo json_encode($data);
                    } else {
                        echo json_encode(['open_id'=>$cont['openid'],'session_key'=>$cont['session_key']]);
                    }
                }
                // echo json_encode(['open_id'=>$cont['openid'],'session_key'=>$cont['session_key']]);
                // echo $cont['session_key'];
                Yii::app()->end();
                // $this->frame['data'] = $cont['session_key'];
            }
                
        }
    }

    public function actionUserList()
    {
        if($uid = Yii::app()->request->getQuery('uid',0)) {
            $page = Yii::app()->request->getQuery('page',1);
            $user = UserExt::model()->findByPk($uid);
            $criteria = new CDbCriteria;
            $criteria->addCondition('uid='.$user->id);
            $kw = Yii::app()->request->getQuery('kw','');
            $status = Yii::app()->request->getQuery('status','');
            if($kw) {
                if(is_numeric($kw)) {
                    $criteria->addSearchCondition('phone',$kw);
                } else {
                    $criteria->addSearchCondition('name',$kw);
                }
            }
            if(is_numeric($status)) {
                $criteria->addCondition('status=:status');
                $criteria->params[':status'] = $status;
            }
            $criteria->order = 'created desc';
            $subs = SubExt::model()->undeleted()->getList($criteria);
            $data = $data['list'] = [];
            if($subs->data) {

                foreach ($subs->data as $key => $value) {
                    
                    $itsstaff = $user;
                    $tmp['id'] = $value->id;
                    $tmp['user_name'] = $value->name;
                    $tmp['user_phone'] = $value->phone;
                    $tmp['staff_name'] = Yii::app()->db->createCommand("select name from user where phone='".$value->notice."'")->queryScalar();
                    $tmp['staff_phone'] = $value->notice;
                    $tmp['time'] = date('m-d H:i',$value->updated);
                    $tmp['status'] = SubExt::$status[$value->status];
                    $tmp['staff_company'] = $value->plot?$value->plot->title:'';
                    $data['list'][] = $tmp;
                }
            }
            $data['page'] = $page;
            $data['page_count'] = $subs->pagination->pageCount;
            $data['num'] = $subs->pagination->itemCount;
            $this->frame['data'] = $data;
        } else {
            $this->returnError('用户类型错误，只支持分销或独立经纪人访问');
        }
    }

    public function actionGetPhone()
    {
        $phone = SiteExt::getAttr('qjpz','site_phone');
        $this->frame['data'] = $phone;
    }
}
