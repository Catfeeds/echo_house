<?php

/**
 * 工具类脚本
 */
class ToolCommand extends CConsoleCommand
{
    /**
     * 同步本地静态文件到七牛
     */
    public function actionQnSync()
    {
        $basePath = Yii::app()->basePath;
        $baseDir = Yii::app()->name;
        $date = date('YmdHis');
        $QnUrl = Yii::app()->staticFile->host.'/';
        $fileArr = [
            'pro.js' => '/resoldwap/build/pro.js'
        ];
        echo "Start Sync:\n";
        echo "Version:{$date}\n";
        echo "==========================\n";
        foreach ($fileArr as $name => $path) {
            $path = $basePath.'/../'.$path;
            $extPath = $baseDir.'/'.$date.'/'.$name;
            $r = Yii::app()->staticFile->consoleFileUpload($path, $extPath);

            if (isset($r['key'])) {
                echo $QnUrl.$r['key']."\n";
            } else {
                var_dump($r);
            }
        }
    }
    public function actionDo()
    {
        // $infos = PlotExt::model()->normal()->findAll();
        // 更新所有会员项目的到期时间
        $time = time();
        $vips = Yii::app()->db->createCommand("select id,vip_expire,vip_expire_new from user where vip_expire>$time or vip_expire_new>$time")->queryAll();
        if($vips) {
            foreach ($vips as $key => $value) {
                $expire = $value['vip_expire_new']>$value['vip_expire']?$value['vip_expire_new']:$value['vip_expire'];
                // $plot = PlotExt::model()->findAll("uid=".$value['id']);
                Yii::app()->db->createCommand("update plot_makert_user set expire=$expire where uid=".$value['id'])->execute();
            }
        }
        // $sql = "delete from company where name='南京好理想房地产经纪有限公司'";
        // Yii::app()->db->createCommand($sql)->execute();
        // // var_dump(count($infos));exit;
        // foreach ($infos as $key => $value) {
        //     // if(!$value->first_pay && $value->pays) {
        //     //     $value->first_pay = $value->pays[0]['price'];
        //     // }
        //     $value->save();
        //     // sleep(1);
        // }
        echo "ok";
    }

    public function actionAddPlotViews()
    {
        $hids = Yii::app()->redis->getClient()->hGetAll('plot_views');
        // var_dump($hids);exit;
        if($hids) {
            foreach ($hids as $key => $value) {
                $plot = PlotExt::model()->findByPk($key);
                if(!$plot) {
                    continue;
                }
                $plot->views+=$value;
                $plot->save();
                Yii::app()->redis->getClient()->hSet('plot_views',$key,0);
            }
        }
        echo "finished";
    }

    public function actionClearTop()
    {
        $plots = PlotExt::model()->findAll('qjsort>0 and qjtop_time<'.time());
        if($plots) {
            foreach ($plots as $key => $value) {
                $value->qjsort = 0;
                $value->save();
            }
        }
        $plots = PlotExt::model()->findAll('sort>0 and top_time<'.time());
        if($plots) {
            foreach ($plots as $key => $value) {
                $value->sort = 0;
                $value->save();
            }
        }
    }

    public function actionClearListCache()
    {
        // wap_init_plotlist
        CacheExt::delete('wap_init_plotlist');
    }

    public function actionSendNo()
    {
        $infos = UserExt::model()->findAll('vip_expire>'.time().' and vip_expire<'.(time()+86400*3));
        // $infos = PlotMarketUserExt::model()->findAll('expire>'.time().' and expire<'.time()+86400*3);
        foreach ($infos as $key => $user) {
            if($user && $user->phone) {
                SmsExt::sendMsg('会员到期通知',$user->phone,['phone'=>SiteExt::getAttr('qjpz','site_wx'),'name'=>$user->name]);
            }
            // $user = $value->user;
            // if($user&&$user->qf_uid) {
            //     Yii::app()->controller->sendNotice($user->name.'您好，您的新房通VIP会员即将到期，请点击以下链接自助续费: http://house.jj58.com.cn/api/index/vip ，或者联系客服微信:'.SiteExt::getAttr('qjpz','site_wx').'协助续费。',$user->qf_uid);
            //     // if($p = $value->plot) {
            //     //     SmsExt::sendMsg('到期通知',$user->phone,['pro'=>$p->title]);
            //     //     Yii::app()->controller->sendNotice('您的项目'.$p->title.'即将到期，请点击下面链接成为会员，成为会员后您的号码将继续展现，并且可以无限次数发布项目。 http://house.jj58.com.cn/api/index/vip',$user->qf_uid);
            //     // }
            // }
        }
    }

    public function actionAddArea()
    {
        

    $areas = AreaExt::model()->findAll("parent=0 and name!='上海'");
    foreach ($areas as $key => $area) {
        foreach ($city_arr as $k=>$c) {
            if(in_array($area->name, $c)) {
                if($cid = Yii::app()->db->createCommand("select id from area where parent=0 and name='$k'")->queryScalar()) {
                    $area->parent = $cid;

                } else {
                    $onj = new AreaExt;
                    $onj->name = $k;
                    $onj->parent = 0;
                    $onj->save();
                    $area->parent = $onj->id;
                }
                if($area->save()) {
                    $ccid = $area->parent;
                    $aaid = $area->id;
                    Yii::app()->db->createCommand("update plot set city=$ccid where area=$aaid")->execute();
                }
            }
        }
        echo ($key+1).'/'.count($areas).'============================';
    }
    }


    public function actionPlotRedis()
    {
        $allPlots = Yii::app()->db->createCommand("select id,title from plot")->queryAll();

        foreach ($allPlots as $key => $value) {
            Yii::app()->redis->getClient()->hSet('plot_title',$value['id'],$value['title']);
        }

    }

    public function actionPlotVirtual()
    {
        $ress = UserExt::model()->findAll(['condition'=>'status=1 and type=1 and virtual_no=""']);
        if($ress) {
            foreach ($ress as $key => $user) {
                // 如果项目不存在 跳过
                // if(!Yii::app()->db->createCommand("select id from plot where id=".$value->hid)->queryScalar()) {
                //     continue;
                // }
                // $user = $value->owner;
                // if(!$user) {
                //     continue;
                // }
                if(!$user->virtual_no) {
                    $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
                    $vp = $vps->phone;
                    $nowext = $vps->max?($vps->max+1):1;
                    $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
                    // var_dump($nowext);exit;
                    // 生成绑定
                    $obj = Yii::app()->axn;
                    $res = $obj->bindAxnExtension('默认号码池',$user->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));
                    if($res->Code=='OK') {
                        $user->virtual_no = $res->SecretBindDTO->SecretNo;
                        $user->virtual_no_ext = $res->SecretBindDTO->Extension;
                        $user->subs_id = $res->SecretBindDTO->SubsId;
                        $user->save();
                        $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$user->virtual_no'"]);
                        if($newvps && $user->virtual_no_ext) {
                             $newvps->max = $user->virtual_no_ext;
                         }
                        $newvps->save();
                    } else {
                        // Yii::log(json_encode($res));
                    }
                }
                echo ($key+1).'/'.count($ress).'-------------------';
            }
        }
    }

    public function actionJbVirtual()
    {
        # code...
    }

    public function actionFreePlotUser()
    {
        $num = 0;
        foreach (PlotExt::model()->findAll() as $key => $value) {
            // if($pm = $value->market_users) {
                if($value->market_users) {
                    preg_match_all('/[0-9]+/', $value->market_users, $tmps);
                    if(isset($tmps[0])) {
                        foreach ($tmps[0] as $thisphone) {
                            $user = UserExt::model()->find("phone='".$thisphone."'");
                            // 没有的话加入总代且发送短信且生成虚拟号
                            if(!$user) {
                                $user = new UserExt;
                                $user->phone = $thisphone;
                                $user->name = str_replace($thisphone, '', $value->market_users);
                                $user->type = 1;
                                $user->cid = $value->company_id;
                                $user->status = 1;
                                $user->save();
                                $num++;
                                echo $num;
                            }
                        }
                    }
                }
            // }
        }
    }

    /**
     * 每分钟自动绑定虚拟号
     */
    public function actionBandV()
    {
        // $timeb = time()-60;
        // $users = UserExt::model()->findAll('status=1 and type=1 and virtual_no=""');
        // if($users) {
        //     foreach ($users as $key => $value) {
        //         if(!$value->phone || !is_numeric($value->phone) || strlen($value->phone)!=11)
        //             continue;
        //         if(!$value->virtual_no) {
        //             $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
        //             if($vps) {
        //                 $vp = $vps->phone;
        //                 $nowext = $vps->max?($vps->max+1):1;
        //                 $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
        //                 // var_dump($nowext);exit;
        //                 // 生成绑定
        //                 $obj = Yii::app()->axn;
        //                 $res = $obj->bindAxnExtension('默认号码池',$value->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));

        //                 if($res->Code=='OK') {
        //                     $value->virtual_no = $res->SecretBindDTO->SecretNo;
        //                     $value->virtual_no_ext = $res->SecretBindDTO->Extension;
        //                     $value->subs_id = $res->SecretBindDTO->SubsId;

        //                     $value->save();
        //                     Yii::log($this->virtual_no);
        //                     $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$value->virtual_no'"]);
        //                     if($newvps && $value->virtual_no_ext) {
        //                         $newvps->max = $value->virtual_no_ext;
        //                         $newvps->save();
        //                     }
        //                     // $value->save();
                            
                            
        //                 } else {
        //                     // Yii::log(json_encode($res));
        //                 }
        //             }
        //         }
        //     }
        // }
    }

    public function actionSendFreeUser()
    {
        $timeb = time()-86400;
        $plots = PlotExt::model()->findAll('status=1 and market_users!="" and created>'.$timeb);

        if($plots) {
            foreach ($plots as $key => $value) {
                if($value->market_users) {
                    preg_match_all('/[0-9]+/',$value->market_users,$num);
                    if(isset($num[0]) && count($num[0])>0) {
                        foreach ($num[0] as $num) {
                            $res = Yii::app()->db->createCommand("select vip_expire from user where phone='$num'")->queryScalar();
                            if(!$res) {
                                SmsExt::sendMsg('免费对接人通知',$num,['lpmc'=>$value->title,'phone'=>SiteExt::getAttr('qjpz','site_phone')]);
                            }
                        }
                    }
                }
                
            }
        }
    }

    public function actionSendAllNo()
    {
        $page = 1;
        begin:
        $sql = "select phone,name from user where status=1 limit $page,200";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $key => $value) {
                if($value['phone'] && $value['name'])
                    SmsExt::sendMsg('群发虚拟号通知短信',$value['phone'],['name'=>$value['name']]);
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }
            
    }

    public function actionSetOpenId()
    {
        $key = "495e6105d4146af1d36053c1034bc819";
        $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        $res = $this->get_response($key,$url,['uid'=>11]);
        var_dump($res);exit;

        $page = 1;
        $key = "495e6105d4146af1d36053c1034bc819";
        $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        begin:
        $sql = "select id,qf_uid from user where qf_uid>0 order by qf_uid asc";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $value) {
                $res = $this->get_response($key,$url,['uid'=>$value['qf_uid']]);
                // var_dump($res);exit;
                $res = json_decode($res,true);
                // if($res['ret']==0) {
                //     var_dump(1);
                // }
                if($res && isset($res['data']['openid'])) {
                    var_dump($value['qf_uid']);
                    Yii::app()->db->createCommand("update user set jjq_openid='".$res['data']['openid']."' where id=".$value['id'])->execute();
                    // $value->jjq_openid = $res['data']['openid'];
                    // $value->save();
                }
                // if($value['phone'] && $value['name'])
                //     SmsExt::sendMsg('群发虚拟号通知短信',$value['phone'],['name'=>$value['name']]);
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }

        // $users = UserExt::model()->findAll('qf_uid>0');
        // foreach ($users as $key => $value) {
        //     $key = "495e6105d4146af1d36053c1034bc819";
        //     $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        //     $res = $this->get_response($key,$url,[],['uid'=>$value->qf_uid]);
        //     if($res && isset($res['data']['openid'])) {
        //         $value->jjq_openid = $res['data']['openid'];
        //         $value->save();
        //     }
        // }
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

    public function actionExeSql($value='')
    {
        $page = 1;
        // $key = "495e6105d4146af1d36053c1034bc819";
        // $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        begin:
        $sql = "select id,adduid from company where adduid>0 limit $page,200";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $value) {
                $addphone = Yii::app()->db->createCommand("select phone from user where qf_uid=".$value['adduid'])->queryScalar();
                if(!$addphone) {
                    continue;
                }
                Yii::app()->db->createCommand("update company set addphone='$addphone' where id=".$value['id'])->execute();
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }
    }

    public function actionImportUser()
    {
        $arr = [['name'=>'郑智强','com'=>'齐家房产鹿璟分行','phone'=>'19979923512'],
['name'=>'林星辉','com'=>'鸿基房产新力都荟荣耀店','phone'=>'19979859235'],
['name'=>'李凌彬','com'=>'鸿基房产象湖平安分部','phone'=>'19979560605'],
['name'=>'赵林梦','com'=>'中环地产诚义路卓越分行','phone'=>'19979556112'],
['name'=>'杨帆','com'=>'中环地产景奥分行','phone'=>'19979554995'],
['name'=>'刘亮','com'=>'中环地产金沙大道分行','phone'=>'19979138501'],
['name'=>'叶笑云','com'=>'齐家房产博泰分行','phone'=>'19979134069'],
['name'=>'邓剑斌','com'=>'中环地产银河城分行','phone'=>'19979123352'],
['name'=>'陈小松','com'=>'芳鑫房产红角州店','phone'=>'19979118828'],
['name'=>'万宇凡','com'=>'鸿基房产新力都荟店','phone'=>'19979115322'],
['name'=>'莫映伟','com'=>'齐家房产鹿璟分行','phone'=>'19979069066'],
['name'=>'曹振','com'=>'我爱我家豪宅部分行','phone'=>'19979068881'],
['name'=>'龚洪','com'=>'中环地产汇仁阳光分行','phone'=>'19979058263'],
['name'=>'黄洞凯','com'=>'鸿基房产康嘉苑店','phone'=>'19979056764'],
['name'=>'杨欢欢','com'=>'我爱我家万科海上传奇店','phone'=>'19979055525'],
['name'=>'万旺','com'=>'中环地产奥园二分行','phone'=>'19979051929'],
['name'=>'罗业敏','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'19979049265'],
['name'=>'吕佳成','com'=>'我爱我家象湖桃源分行','phone'=>'19979031123'],
['name'=>'刘婷','com'=>'中环地产拉菲公馆分行','phone'=>'19979026145'],
['name'=>'程志云','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'19970455624'],
['name'=>'付佳欣','com'=>'中天房产四中店','phone'=>'19970422323'],
['name'=>'罗英','com'=>'中环地产诚义路卓越分行','phone'=>'19970193242'],
['name'=>'刘鹏','com'=>'中环地产恒大城分行','phone'=>'19917909874'],
['name'=>'龙志华','com'=>'中环地产平安东门分行','phone'=>'19917907144'],
['name'=>'黄新盖','com'=>'中环地产滨河壹品分行','phone'=>'19907015664'],
['name'=>'王朋飞','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'19903716827'],
['name'=>'陈丽霞','com'=>'我爱我家莱蒙都会店','phone'=>'18979203403'],
['name'=>'胡思飞','com'=>'鸿基房产东方苑店','phone'=>'18979190021'],
['name'=>'熊斌','com'=>'齐家房产江信分行','phone'=>'18979189885'],
['name'=>'聂明晖','com'=>'鸿基房产广场东路店','phone'=>'18979172405'],
['name'=>'王俊','com'=>'我爱我家力高滨湖国际分行','phone'=>'18979158991'],
['name'=>'吴杨华','com'=>'鸿基房产绿地山庄店','phone'=>'18979155587'],
['name'=>'熊芳杰','com'=>'鸿基房产红谷凯旋分行','phone'=>'18979124111'],
['name'=>'曾晓琴','com'=>'中环地产象湖沃尔玛分行','phone'=>'18970979191'],
['name'=>'张小兰','com'=>'我爱我家正荣大湖之都分行','phone'=>'18970968195'],
['name'=>'刘辉','com'=>'我爱我家象湖新城分行','phone'=>'18970967086'],
['name'=>'余胜源','com'=>'中环地产比华利分行','phone'=>'18970954429'],
['name'=>'李毅竹','com'=>'我爱我家香域尚城分行','phone'=>'18970950508'],
['name'=>'赵青','com'=>'我爱我家丰源淳和店','phone'=>'18970932517'],
['name'=>'纪国迎','com'=>'中环地产恒大珺庭分行','phone'=>'18970930661'],
['name'=>'周世槐','com'=>'鸿基房产红谷新城店','phone'=>'18970922746'],
['name'=>'毛年平','com'=>'鸿基房产力高南门店','phone'=>'18970911800'],
['name'=>'周文','com'=>'鸿基房产恒茂东方店','phone'=>'18970903983'],
['name'=>'万欣红','com'=>'中环地产澄碧湖分行','phone'=>'18970864572'],
['name'=>'付秀乾','com'=>'我爱我家下罗分行','phone'=>'18970863309'],
['name'=>'胡璟辉','com'=>'鸿基房产三店西路店','phone'=>'18970861536'],
['name'=>'尤本豪','com'=>'齐家房产世纪花园店','phone'=>'18970856943'],
['name'=>'李跃','com'=>'中环地产昌南一分行','phone'=>'18970853414'],
['name'=>'董飞','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18970832949'],
['name'=>'胡翠杰','com'=>'中环地产苏圃路店','phone'=>'18970826199'],
['name'=>'程明','com'=>'鸿基房产抚生佳园分行','phone'=>'18970818273'],
['name'=>'熊淑芳','com'=>'芳鑫房产红角州店','phone'=>'18970807684'],
['name'=>'徐唐龙','com'=>'鸿基房产天赐良园分行','phone'=>'18970089276'],
['name'=>'徐湘杰','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18970079246'],
['name'=>'龚国英','com'=>'鸿基房产曙光小区分部','phone'=>'18970076048'],
['name'=>'姚奇坤','com'=>'中环地产力高都会分行','phone'=>'18970066969'],
['name'=>'刘晖南','com'=>'中环地产洪城分行','phone'=>'18970047989'],
['name'=>'李菁','com'=>'中环地产滨河壹品分行','phone'=>'18970032948'],
['name'=>'李欢欢','com'=>'我爱我家九里象湖二店','phone'=>'18970027618'],
['name'=>'程亮','com'=>'鸿基房产天赐良园分行','phone'=>'18970025445'],
['name'=>'何霞','com'=>'鸿基房产新力都荟店','phone'=>'18959025818'],
['name'=>'何玉龙','com'=>'齐家房产海德堡店','phone'=>'18942359450'],
['name'=>'谢年青','com'=>'鸿基房产力高滨湖国际店','phone'=>'18942349688'],
['name'=>'蔡安阳','com'=>'中环地产新力帝泊湾分行','phone'=>'18942309919'],
['name'=>'濮青青','com'=>'中环地产诚义路卓越分行','phone'=>'18942249695'],
['name'=>'曾丽丽','com'=>'中好房屋力高滨江国际店','phone'=>'18942249396'],
['name'=>'李波','com'=>'鸿基房产汤家园店','phone'=>'18942235512'],
['name'=>'方欢','com'=>'中环地产水岸菁华分行','phone'=>'18942233498'],
['name'=>'孙学飞','com'=>'鸿基房产丰和花园店','phone'=>'18942220771'],
['name'=>'万齐乐','com'=>'中环地产新建大道分行','phone'=>'18942214206'],
['name'=>'熊旭阳','com'=>'鸿基房产船山路店','phone'=>'18907093852'],
['name'=>'王花香','com'=>'中环地产世茂飞虹分行','phone'=>'18907091653'],
['name'=>'罗磊','com'=>'齐家房产世纪店','phone'=>'18907008462'],
['name'=>'夏鹃','com'=>'我爱我家莱卡小镇店','phone'=>'18897915937'],
['name'=>'翟力','com'=>'中环地产皇冠国际二分行','phone'=>'18879560273'],
['name'=>'姜正','com'=>'中环地产滨湖花城分行','phone'=>'18879179216'],
['name'=>'范义龙','com'=>'齐家房产梵顿公馆分行','phone'=>'18879170053'],
['name'=>'邹远龙','com'=>'中环地产世贸分行','phone'=>'18879153959'],
['name'=>'闵志辉','com'=>'鸿基房产锦竹幸福里店','phone'=>'18879153049'],
['name'=>'廖平','com'=>'中环地产王家庄分行','phone'=>'18879152268'],
['name'=>'叶金梅','com'=>'中环地产芳华路分行','phone'=>'18879148581'],
['name'=>'万宇峰','com'=>'鸿基房产新力都荟店','phone'=>'18879143977'],
['name'=>'吴琴芳','com'=>'鸿基房产紫金园店','phone'=>'18879133679'],
['name'=>'黄文星','com'=>'中环地产丰源淳和分行','phone'=>'18879120838'],
['name'=>'周文强','com'=>'中环地产金沙大道分行','phone'=>'18879112021'],
['name'=>'徐智宏','com'=>'鸿基房产嘉业店','phone'=>'18879109621'],
['name'=>'王朋凯','com'=>'鸿基房产曙光小区分部','phone'=>'18879103430'],
['name'=>'刘惠平','com'=>'中环地产平安东门分行','phone'=>'18870963978'],
['name'=>'樊美玲','com'=>'中环地产迎宾大道店','phone'=>'18870911549'],
['name'=>'王恒','com'=>'鸿基房产象湖平安总店','phone'=>'18870878641'],
['name'=>'艾家文','com'=>'中环地产世贸分行','phone'=>'18870876368'],
['name'=>'吴磊','com'=>'鸿基房产胜利路店','phone'=>'18870872713'],
['name'=>'刘井香','com'=>'中环地产湾里广场分行','phone'=>'18870827062'],
['name'=>'万丽华','com'=>'中环地产工商学院分行','phone'=>'18870824184'],
['name'=>'付斌','com'=>'中环地产汇仁阳光分行','phone'=>'18870808829'],
['name'=>'熊京京','com'=>'中环地产恒大城分行','phone'=>'18870808031'],
['name'=>'李来龙','com'=>'我爱我家上海新城分行','phone'=>'18870806747'],
['name'=>'姚海华','com'=>'中环地产彭家桥分行','phone'=>'18870800228'],
['name'=>'肖智祥','com'=>'中环地产景城名郡分行','phone'=>'18870663139'],
['name'=>'姜紫扬','com'=>'中环地产芳诚路分行','phone'=>'18870334327'],
['name'=>'黎鑫','com'=>'中环地产城开国际分行','phone'=>'18870099327'],
['name'=>'黄小琴','com'=>'齐家房产华府分行','phone'=>'18870097382'],
['name'=>'胡德华','com'=>'我爱我家白沙帝店','phone'=>'18870085799'],
['name'=>'王美宝','com'=>'我爱我家南京西路店','phone'=>'18870085697'],
['name'=>'欧阳敏','com'=>'中环地产金域名都分行','phone'=>'18870072665'],
['name'=>'胡志强','com'=>'中环地产恒大城分行','phone'=>'18870069921'],
['name'=>'万希敏','com'=>'我爱我家幸福里旗舰店','phone'=>'18870066532'],
['name'=>'胡京都','com'=>'鸿基房产东方苑店','phone'=>'18870060654'],
['name'=>'詹丽燕','com'=>'齐家房产博泰分行','phone'=>'18870056203'],
['name'=>'罗建辉','com'=>'中环地产天一城分行','phone'=>'18870054049'],
['name'=>'袁斯海','com'=>'中环地产东方银座分行','phone'=>'18870043007'],
['name'=>'罗向平','com'=>'南昌城拓房产一部','phone'=>'18870033153'],
['name'=>'曾祥峥','com'=>'中环地产英伦联邦分行','phone'=>'18870029046'],
['name'=>'王阿玲','com'=>'中环地产保集南门分行','phone'=>'18870017137'],
['name'=>'诸丞昊','com'=>'中环地产诚义路卓越分行','phone'=>'18870007719'],
['name'=>'罗文文','com'=>'鸿基房产正荣南岸公园分行','phone'=>'18870002709'],
['name'=>'乐小花','com'=>'我爱我家力高中心店','phone'=>'18827919253'],
['name'=>'杨祥祥','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18827911099'],
['name'=>'钟平','com'=>'鸿基房产下罗分行','phone'=>'18816483405'],
['name'=>'周韩','com'=>'中好房屋力高滨江国际店','phone'=>'18807960610'],
['name'=>'陶明明','com'=>'我爱我家莱蒙都会店','phone'=>'18807087723'],
['name'=>'丁新凤','com'=>'桥郡房产红谷滩分行','phone'=>'18797913277'],
['name'=>'谢飞飞','com'=>'中环地产下罗分行','phone'=>'18779895116'],
['name'=>'胡建华','com'=>'中环地产诚义路卓越分行','phone'=>'18779890072'],
['name'=>'赖昕','com'=>'中环地产芳诚路分行','phone'=>'18779888060'],
['name'=>'聂成成','com'=>'中环地产怡兰苑分行','phone'=>'18779881024'],
['name'=>'黄娟娟','com'=>'中天房产白沙帝分行','phone'=>'18779880078'],
['name'=>'万璐','com'=>'齐家房产世纪店','phone'=>'18779876863'],
['name'=>'徐苏勇','com'=>'中环地产景城名郡二分行','phone'=>'18779668591'],
['name'=>'王聪聪','com'=>'鸿基房产红谷滩店','phone'=>'18779197493'],
['name'=>'刘保伟','com'=>'中环地产青山湖大道分行','phone'=>'18779188678'],
['name'=>'万瑶','com'=>'我爱我家莱蒙都会店','phone'=>'18779184015'],
['name'=>'罗艺涛','com'=>'伟创房产总部','phone'=>'18779177190'],
['name'=>'俞效生','com'=>'鸿基房产中大紫都分行','phone'=>'18779173917'],
['name'=>'周婷','com'=>'中环地产金沙大道分行','phone'=>'18779168375'],
['name'=>'周敏冲','com'=>'中环地产工商学院分行','phone'=>'18779156521'],
['name'=>'李茂胜','com'=>'鸿基房产滨河一品分行','phone'=>'18779155769'],
['name'=>'赵庚英','com'=>'鸿基房产凤凰城分行','phone'=>'18779142281'],
['name'=>'唐文强','com'=>'中环地产正荣永通分行','phone'=>'18779138087'],
['name'=>'张文','com'=>'中环地产诚义路卓越分行','phone'=>'18779132901'],
['name'=>'胡成成','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18779124861'],
['name'=>'管慧国','com'=>'中环地产金沙大道分行','phone'=>'18779121021'],
['name'=>'罗成','com'=>'中环地产恒大名都分行','phone'=>'18779115948'],
['name'=>'吴涛','com'=>'中环地产华润橡树湾分行','phone'=>'18770928739'],
['name'=>'雷青','com'=>'鸿基房产正荣南岸公园分行','phone'=>'18770593537'],
['name'=>'胡清华','com'=>'中环地产梅岭分行','phone'=>'18770098839'],
['name'=>'王星','com'=>'中环地产景城名郡二分行','phone'=>'18770098134'],
['name'=>'杨婷','com'=>'中环地产景城名郡二分行','phone'=>'18770096220'],
['name'=>'张振军','com'=>'中环地产艾溪康桥分行','phone'=>'18770085470'],
['name'=>'危永超','com'=>'中环地产恒大城分行','phone'=>'18770077095'],
['name'=>'涂志龙','com'=>'中环地产金沙二路分行','phone'=>'18770065258'],
['name'=>'熊文昊','com'=>'我爱我家幸福里旗舰店','phone'=>'18770055289'],
['name'=>'喻灵坤','com'=>'中环地产银三角恒大分行','phone'=>'18770045885'],
['name'=>'王越韩','com'=>'中环地产胜利路分行','phone'=>'18770033788'],
['name'=>'黄慧娟','com'=>'中环地产诚义路卓越分行','phone'=>'18770026189'],
['name'=>'应梦奇','com'=>'中环地产蓝天碧水分行','phone'=>'18770023992'],
['name'=>'贺琼','com'=>'中环地产平安东门分行','phone'=>'18770018832'],
['name'=>'刘文坤','com'=>'鸿基房产保集半岛分行','phone'=>'18770014068'],
['name'=>'万鑫','com'=>'来淘房产红谷滩店','phone'=>'18770008971'],
['name'=>'屈蛟华','com'=>'来淘房产红谷滩店','phone'=>'18770008794'],
['name'=>'龚琴琴','com'=>'中环地产财经大学分行','phone'=>'18770007375'],
['name'=>'石杨娜','com'=>'南昌香廷房产红谷滩分部','phone'=>'18770003601'],
['name'=>'宋孝利','com'=>'中环地产幸福诚义分行','phone'=>'18720971787'],
['name'=>'王杰','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18720959484'],
['name'=>'徐声康','com'=>'中环地产体育馆分行','phone'=>'18720927157'],
['name'=>'王伟','com'=>'鸿基房产桥郡旗舰店','phone'=>'18720923578'],
['name'=>'涂伟伦','com'=>'中环地产金沙大道分行','phone'=>'18720919435'],
['name'=>'李文武','com'=>'中环地产景城名郡分行','phone'=>'18720911781'],
['name'=>'朱光磊','com'=>'鸿基房产天赐良园分行','phone'=>'18720911054'],
['name'=>'凌倩倩','com'=>'中环地产芳草路分行','phone'=>'18720909988'],
['name'=>'何路元','com'=>'中环地产滨河壹品分行','phone'=>'18720908797'],
['name'=>'方沈良','com'=>'鸿基房产象湖平安分部','phone'=>'18720902800'],
['name'=>'戈雪云','com'=>'齐家房产世纪店','phone'=>'18720649353'],
['name'=>'张颖','com'=>'云翊房产红谷滩店','phone'=>'18720412539'],
['name'=>'田杰','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18720411451'],
['name'=>'邹秀雅','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18720236626'],
['name'=>'熊犇','com'=>'中环地产正荣银河分行','phone'=>'18720098952'],
['name'=>'何永慧','com'=>'中环地产滨河壹品分行','phone'=>'18720085399'],
['name'=>'邓成康','com'=>'中环地产店中央首府分行','phone'=>'18720075309'],
['name'=>'胡文波','com'=>'中环地产象湖国际二分行','phone'=>'18720071633'],
['name'=>'谭彬','com'=>'齐家房产奥克斯店','phone'=>'18720071325'],
['name'=>'金剑刚','com'=>'中环地产工商学院分行','phone'=>'18720065754'],
['name'=>'鄢欢','com'=>'中环地产青山湖大道分行','phone'=>'18720053995'],
['name'=>'胡芸','com'=>'鸿基房产规划二路店','phone'=>'18702697662'],
['name'=>'胡文涛','com'=>'鸿基房产景奥店','phone'=>'18702643557'],
['name'=>'熊家金','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18702627760'],
['name'=>'姜青来','com'=>'南昌香廷房产红谷滩分部','phone'=>'18702617478'],
['name'=>'李小明','com'=>'中环地产文教南店','phone'=>'18702583854'],
['name'=>'黄应杰','com'=>'中环地产金沙大道分行','phone'=>'18702547957'],
['name'=>'胡幸媛','com'=>'我爱我家万科城分行','phone'=>'18702538606'],
['name'=>'邱涛祥','com'=>'中环地产新力都荟分行','phone'=>'18702533107'],
['name'=>'陈龙','com'=>'中环地产水岸皇廷分行','phone'=>'18702532959'],
['name'=>'周志伟','com'=>'中环地产正荣银河分行','phone'=>'18702530601'],
['name'=>'李禄彬','com'=>'中环地产工商学院分行','phone'=>'18702528692'],
['name'=>'李依燕','com'=>'中环地产艾溪康桥分行','phone'=>'18702515011'],
['name'=>'金方斌','com'=>'鸿基房产心怡广场店','phone'=>'18702503664'],
['name'=>'王志辉','com'=>'中环地产金沙大道分行','phone'=>'18682246162'],
['name'=>'邬剑','com'=>'鸿基房产梵顿公馆店','phone'=>'18679951053'],
['name'=>'齐逢春','com'=>'中环地产彭家桥分行','phone'=>'18679930055'],
['name'=>'饶士琪','com'=>'齐家房产万达茂A区店','phone'=>'18679829355'],
['name'=>'曾智辉','com'=>'中环地产工商学院分行','phone'=>'18679658928'],
['name'=>'裴金华','com'=>'鸿基房产象湖诚义店','phone'=>'18679577892'],
['name'=>'胡圆圆','com'=>'齐家房产世纪花园店','phone'=>'18679226526'],
['name'=>'曹若菲','com'=>'中环地产金沙大道分行','phone'=>'18679193622'],
['name'=>'卢汉文','com'=>'我爱我家万科分行','phone'=>'18679187661'],
['name'=>'宋世沛','com'=>'中环地产昌南一分行','phone'=>'18679178717'],
['name'=>'柳嘉豪','com'=>'我爱我家景城名郡分行','phone'=>'18679163653'],
['name'=>'吴云','com'=>'中环地产创新一路分行','phone'=>'18679137557'],
['name'=>'杨德彪','com'=>'齐家房产鹿璟分行','phone'=>'18679126965'],
['name'=>'谭宜华','com'=>'中天房产星港小镇分行','phone'=>'18679107195'],
['name'=>'刘妹庭','com'=>'鸿基房产正荣大湖之都店','phone'=>'18679103602'],
['name'=>'凌滔滔','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18679059083'],
['name'=>'蒋书晔','com'=>'慧达地产总部','phone'=>'18679056988'],
['name'=>'樊梅芳','com'=>'动一房产总部','phone'=>'18679050140'],
['name'=>'李海林','com'=>'中环地产保集一分行','phone'=>'18679032616'],
['name'=>'李善华','com'=>'中环地产保集一分行','phone'=>'18679032528'],
['name'=>'胡久杠','com'=>'中环地产金沙大道分行','phone'=>'18657189846'],
['name'=>'鲍懿','com'=>'中环地产文教路分行','phone'=>'18620174234'],
['name'=>'黄桂平','com'=>'中环地产保集一分行','phone'=>'18607988119'],
['name'=>'余恒','com'=>'中环地产彭家桥分行','phone'=>'18607919997'],
['name'=>'马萌萌','com'=>'鸿基房产丰和新城店','phone'=>'18607918643'],
['name'=>'陈志超','com'=>'中环地产连发分行','phone'=>'18607912980'],
['name'=>'周莉','com'=>'齐家房产联发店','phone'=>'18607910331'],
['name'=>'饶建兵','com'=>'中环地产金沙大道分行','phone'=>'18607099607'],
['name'=>'杨建坤','com'=>'中环地产京东国际花园分行','phone'=>'18607093992'],
['name'=>'袁刃','com'=>'鸿基房产前进路店','phone'=>'18607093370'],
['name'=>'王娟','com'=>'中环地产诚义路卓越分行','phone'=>'18607083635'],
['name'=>'喻愿龙','com'=>'中环地产力天阳光分行','phone'=>'18607055205'],
['name'=>'陈树杰','com'=>'中环地产小金台分行','phone'=>'18579188003'],
['name'=>'魏亮亮','com'=>'鸿基房产广州路店','phone'=>'18579185279'],
['name'=>'张诚志','com'=>'鸿基房产金涛御景店','phone'=>'18579136232'],
['name'=>'曲江南','com'=>'中环地产康城分行','phone'=>'18579125147'],
['name'=>'李莎莎','com'=>'中环地产星诚幸福分行','phone'=>'18557526132'],
['name'=>'罗星海','com'=>'云房通房产总部','phone'=>'18507917059'],
['name'=>'陈皓然','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18507912332'],
['name'=>'吴云爱','com'=>'桥郡房产红谷滩分行','phone'=>'18507099389'],
['name'=>'邓庆育','com'=>'中环地产金融大街分行','phone'=>'18507092675'],
['name'=>'朱保龙','com'=>'鸿基房产锦竹幸福里店','phone'=>'18507005869'],
['name'=>'李鹏飞','com'=>'馨业房地产红谷滩分行','phone'=>'18479550844'],
['name'=>'罗龙辉','com'=>'中环地产广电中心分行','phone'=>'18397918838'],
['name'=>'苏美青','com'=>'中环地产新力分行','phone'=>'18397836553'],
['name'=>'刘广海','com'=>'鸿基房产时代广场店','phone'=>'18397812617'],
['name'=>'罗俊方','com'=>'中环地产金沙大道分行','phone'=>'18379896314'],
['name'=>'郑加旺','com'=>'中环地产绿腾雅苑分行','phone'=>'18379245726'],
['name'=>'魏欣','com'=>'中环地产工商学院分行','phone'=>'18379195872'],
['name'=>'何小标','com'=>'齐家房产江信分行','phone'=>'18379187242'],
['name'=>'余江涛','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18379170323'],
['name'=>'张兴旺','com'=>'齐家房产国博城店','phone'=>'18379168727'],
['name'=>'曹超龙','com'=>'中环地产香溢花城分行','phone'=>'18379168446'],
['name'=>'裘玉斌','com'=>'中环地产金融大街分行','phone'=>'18379139482'],
['name'=>'幸瑞东','com'=>'中环地产新力分行','phone'=>'18379127812'],
['name'=>'夏琦','com'=>'中环地产诚义路卓越分行','phone'=>'18379002326'],
['name'=>'吴博文','com'=>'中环地产经纬府邸店','phone'=>'18370955223'],
['name'=>'甘海芳','com'=>'中环地产金沙大道分行','phone'=>'18370590103'],
['name'=>'张爱军','com'=>'中环地产南京东路店','phone'=>'18370222663'],
['name'=>'刘永静','com'=>'中环地产金沙大道分行','phone'=>'18370052672'],
['name'=>'余金琴','com'=>'中环地产财经大学分行','phone'=>'18370021299'],
['name'=>'熊宇帆','com'=>'中环地产工商学院分行','phone'=>'18350077931'],
['name'=>'许彬彬','com'=>'中环地产象湖国际二分行','phone'=>'18324422267'],
['name'=>'罗勇','com'=>'中环地产恒大中心分行','phone'=>'18322979955'],
['name'=>'余龙','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18317918157'],
['name'=>'万家力','com'=>'鸿基房产新溪桥店','phone'=>'18307918189'],
['name'=>'金苏园','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18307019299'],
['name'=>'周正钢','com'=>'鸿基房产新力都荟店','phone'=>'18307000180'],
['name'=>'周国富','com'=>'中环地产工商学院分行','phone'=>'18296962842'],
['name'=>'龚子文','com'=>'中环地产丰源淳和分行','phone'=>'18296177446'],
['name'=>'李军涛','com'=>'中环地产金沙大道分行','phone'=>'18296176330'],
['name'=>'熊辉','com'=>'鸿基房产联发君悦湖店','phone'=>'18296159526'],
['name'=>'熊南南','com'=>'南昌博越房产悦城分行','phone'=>'18296136047'],
['name'=>'夏秀琴','com'=>'来淘房产红谷滩店','phone'=>'18296128091'],
['name'=>'程高升','com'=>'中环地产江上院分行','phone'=>'18296127489'],
['name'=>'杨舒','com'=>'齐家房产悦城D区店','phone'=>'18279541850'],
['name'=>'罗能','com'=>'南昌城拓房产一部','phone'=>'18279395133'],
['name'=>'龚兆伟','com'=>'中环地产广兰大道店','phone'=>'18279199379'],
['name'=>'熊全根','com'=>'鸿基房产铁路九村分行','phone'=>'18279194896'],
['name'=>'喻通','com'=>'齐家房产万达茂A区店','phone'=>'18279175171'],
['name'=>'邱霞','com'=>'中环地产红湾大道保利分行','phone'=>'18279148673'],
['name'=>'徐美','com'=>'中环地产诚义路卓越分行','phone'=>'18279146197'],
['name'=>'王芳敏','com'=>'中环地产南大分行','phone'=>'18279135596'],
['name'=>'杨宇航','com'=>'中环地产象湖分行','phone'=>'18279131564'],
['name'=>'欧美全','com'=>'中环地产芳诚路分行','phone'=>'18279125573'],
['name'=>'龙波','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18279123738'],
['name'=>'熊晨','com'=>'齐家房产华府分行','phone'=>'18279110683'],
['name'=>'胡晨敏','com'=>'中环地产星港小镇分行','phone'=>'18279110071'],
['name'=>'王勇','com'=>'中环地产五一路分行','phone'=>'18279104005'],
['name'=>'谢云祥','com'=>'齐家房产悦城D区店','phone'=>'18270898520'],
['name'=>'魏远','com'=>'鸿基房产高新大道店','phone'=>'18270886075'],
['name'=>'苗秀改','com'=>'鸿基房产金沙大道天虹店','phone'=>'18270878950'],
['name'=>'万承辉','com'=>'鸿基房产新力愉景湾店','phone'=>'18270868463'],
['name'=>'袁斯柳','com'=>'中环地产金域名都分行','phone'=>'18270853973'],
['name'=>'杜屹文','com'=>'中环地产景城名郡二分行','phone'=>'18270815674'],
['name'=>'汪佛红','com'=>'中环地产南京东路店','phone'=>'18270800249'],
['name'=>'程世民','com'=>'鸿基房产象湖沃尔玛店','phone'=>'18270339313'],
['name'=>'张明','com'=>'中环地产恒大名都分行','phone'=>'18208297681'],
['name'=>'夏建兵','com'=>'中环地产拉菲公馆分行','phone'=>'18179564734'],
['name'=>'黄小勇','com'=>'中环地产绿地悦公馆分行','phone'=>'18179562680'],
['name'=>'刘青','com'=>'中环地产下罗分行','phone'=>'18179185439'],
['name'=>'胡遵玢','com'=>'中环地产银河城分行','phone'=>'18179184611'],
['name'=>'万军','com'=>'中环地产诚义路卓越分行','phone'=>'18179174359'],
['name'=>'吴骁润','com'=>'中环地产金沙大道分行','phone'=>'18179172547'],
['name'=>'刘心','com'=>'中好房屋力高滨江国际店','phone'=>'18179172182'],
['name'=>'喻星球','com'=>'中环地产幸福诚义分行','phone'=>'18179171219'],
['name'=>'辜陈勇','com'=>'中环地产天沐君湖分行','phone'=>'18179152067'],
['name'=>'林希','com'=>'鸿基房产红谷凯旋分行','phone'=>'18179150114'],
['name'=>'邓波波','com'=>'中环地产城市溪地分行','phone'=>'18179135511'],
['name'=>'熊吴琼','com'=>'鸿基房产莱卡小镇二期店','phone'=>'18174019908'],
['name'=>'梅丁','com'=>'云房通房产总部','phone'=>'18172964010'],
['name'=>'关婷婷','com'=>'我爱我家九里水晶岛店','phone'=>'18172897232'],
['name'=>'万俊','com'=>'中环地产红谷新城分行','phone'=>'18172889627'],
['name'=>'吴俊','com'=>'我爱我家昌东大道分行','phone'=>'18172866708'],
['name'=>'章磊','com'=>'中环地产芳诚路分行','phone'=>'18172864282'],
['name'=>'甘菊平','com'=>'中环地产芳诚路分行','phone'=>'18172863059'],
['name'=>'徐开林','com'=>'中环地产香逸澜湾分行','phone'=>'18172853639'],
['name'=>'危冬兰','com'=>'我爱我家幸福时光三店','phone'=>'18172850336'],
['name'=>'熊宗海','com'=>'中环地产上罗分行','phone'=>'18172822185'],
['name'=>'熊历波','com'=>'鸿基房产锦竹幸福里店','phone'=>'18172806678'],
['name'=>'李明','com'=>'我爱我家金涛御景分行','phone'=>'18170998600'],
['name'=>'周文文','com'=>'齐家房产悦城D区店','phone'=>'18170985368'],
['name'=>'喻小玲','com'=>'南昌欣盛房产红谷滩分行','phone'=>'18170973583'],
['name'=>'魏爱明','com'=>'鸿基房产新坛子口分行','phone'=>'18170972621'],
['name'=>'彭伟强','com'=>'中环地产金沙大道分行','phone'=>'18170955980'],
['name'=>'徐运筹','com'=>'鸿基房产壹品中央店','phone'=>'18170955288'],
['name'=>'左晓军','com'=>'鸿基房产梵顿公馆店','phone'=>'18170954485'],
['name'=>'叶求华','com'=>'中环地产南京东路店','phone'=>'18170939303'],
['name'=>'缪施琪','com'=>'鸿基房产新上海路分行','phone'=>'18170934465'],
['name'=>'吴思念','com'=>'中好房屋力高滨江国际店','phone'=>'18170931832'],
['name'=>'姚觉新','com'=>'中环地产步步高分行','phone'=>'18170909813'],
['name'=>'赵俊峰','com'=>'鸿基房产南天金源店','phone'=>'18170906107'],
['name'=>'魏天城','com'=>'我爱我家丰源淳和店','phone'=>'18170899624'],
['name'=>'徐勇莲','com'=>'中环地产向阳路分行','phone'=>'18170898728'],
['name'=>'虞梁','com'=>'鸿基房产象湖平安总店','phone'=>'18170885868'],
['name'=>'夏令','com'=>'中环地产金嘉名筑分行','phone'=>'18170884172'],
['name'=>'黄国英','com'=>'我爱我家梦里水乡分行','phone'=>'18170883391'],
['name'=>'裘有香','com'=>'鸿基房产老福山店','phone'=>'18170868889'],
['name'=>'龚飞宇','com'=>'中环地产星港小镇分行','phone'=>'18170865027'],
['name'=>'程广才','com'=>'中环地产红谷春天店','phone'=>'18170851125'],
['name'=>'曾斌峰','com'=>'我爱我家嘉业花园二店分行','phone'=>'18170845190'],
['name'=>'陈者','com'=>'中环地产京东国际花园分行','phone'=>'18170838313'],
['name'=>'尚焕丰','com'=>'鸿基房产象湖平安总店','phone'=>'18170835280'],
['name'=>'黄志坚','com'=>'鸿基房产下罗分行','phone'=>'18170833960'],
['name'=>'万由连','com'=>'中环地产南昌十中分行','phone'=>'18170826545'],
['name'=>'何伟','com'=>'中环地产藤旺居分行','phone'=>'18170096610'],
['name'=>'彭垒','com'=>'鸿基房产象湖平安分部','phone'=>'18170083145'],
['name'=>'付小辉','com'=>'中环地产康城分行','phone'=>'18170078082'],
['name'=>'姜迪扬','com'=>'我爱我家永通澄湖西路店','phone'=>'18170075785'],
['name'=>'邓文水','com'=>'中环地产谢家村分行','phone'=>'18170072318'],
['name'=>'冯飞飞','com'=>'中环地产金沙大道分行','phone'=>'18170071067'],
['name'=>'肖文斌','com'=>'万科租售中心地铁万科时代广场店','phone'=>'18170062712'],
['name'=>'喻航','com'=>'中环地产开关厂分行','phone'=>'18170059619'],
['name'=>'龚翔','com'=>'中环地产艾溪康桥分行','phone'=>'18170057049'],
['name'=>'饶南辉','com'=>'鸿基房产保集半岛分行','phone'=>'18170054154'],
['name'=>'胡祥','com'=>'鸿基房产锦竹幸福里店','phone'=>'18170052440'],
['name'=>'陶金标','com'=>'中环地产金嘉名筑分行','phone'=>'18170050330'],
['name'=>'肖佳云','com'=>'齐家房产万达茂A区店','phone'=>'18170041881'],
['name'=>'胡晓峰','com'=>'鸿基房产江大南路店','phone'=>'18170038782'],
['name'=>'刘彩虹','com'=>'我爱我家保利金香槟二店分行','phone'=>'18170031618'],
['name'=>'饶凡','com'=>'中环地产天骥俊园分行','phone'=>'18170013585'],
['name'=>'付伟','com'=>'中环地产金沙大道分行','phone'=>'18162287111'],
['name'=>'柯佳林','com'=>'中环地产绿腾雅苑分行','phone'=>'18162264874'],
['name'=>'饶国星','com'=>'中环地产金沙大道分行','phone'=>'18162109987'],
['name'=>'刘川','com'=>'易居地产瑶池房产','phone'=>'18162109402'],
['name'=>'胡韬','com'=>'齐家房产红谷分行','phone'=>'18162101151'],
['name'=>'王小青','com'=>'鸿基房产新建广场店','phone'=>'18160746882'],
['name'=>'雷丹丹','com'=>'中环地产新力分行','phone'=>'18146782196'],
['name'=>'邱联华','com'=>'鸿基房产恒茂东方店','phone'=>'18107091697'],
['name'=>'何杰','com'=>'中环地产南京东路店','phone'=>'18107006360'],
['name'=>'高强强','com'=>'鸿基房产保集半岛分行','phone'=>'18100797588'],
['name'=>'万红宾','com'=>'中环地产玺园分行','phone'=>'18100797474'],
['name'=>'徐艳艳','com'=>'鸿基房产红谷名门店','phone'=>'18100795008'],
['name'=>'李帆','com'=>'中环地产雍锦王府分行','phone'=>'18100782755'],
['name'=>'姚名雄','com'=>'齐家房产鹿璟分行','phone'=>'18079358636'],
['name'=>'胡亮','com'=>'中环地产象湖分行','phone'=>'18079178700'],
['name'=>'高芳英','com'=>'中环地产顺外路分行','phone'=>'18079176889'],
['name'=>'杨宇帆','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18079174076'],
['name'=>'金西飘','com'=>'鸿基房产新力禧园店','phone'=>'18079173737'],
['name'=>'黄成','com'=>'鸿基房产象湖平安分部','phone'=>'18079172399'],
['name'=>'袁兰','com'=>'鸿基房产汇仁阳光店','phone'=>'18079162275'],
['name'=>'邹倩','com'=>'鸿基房产力高店','phone'=>'18079147050'],
['name'=>'陈以强','com'=>'思翊房产总部','phone'=>'18079136597'],
['name'=>'王芳','com'=>'鸿基房产前进路店','phone'=>'18079133298'],
['name'=>'周宵','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18079131400'],
['name'=>'林鑫','com'=>'中环地产紫金城分行','phone'=>'18079117772'],
['name'=>'曾菊红','com'=>'鸿基房产老福山店','phone'=>'18079117011'],
['name'=>'熊满花','com'=>'中环地产伟梦玉龙苑分行','phone'=>'18079114633'],
['name'=>'徐双双','com'=>'芳鑫房产红角州店','phone'=>'18079113493'],
['name'=>'程敬敬','com'=>'鸿基房产国博城店','phone'=>'18079111914'],
['name'=>'刘安','com'=>'中环地产紫金城分行','phone'=>'18079100388'],
['name'=>'尤崇宇','com'=>'齐家房产世纪花园店','phone'=>'18070593761'],
['name'=>'胡敦辉','com'=>'中环地产平安东门分行','phone'=>'18070560240'],
['name'=>'丁彪','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18070542015'],
['name'=>'郎轩','com'=>'中环地产居住主题分行','phone'=>'18070392023'],
['name'=>'叶左印','com'=>'鸿基房产力高店','phone'=>'18070391780'],
['name'=>'戴玉莲','com'=>'桥郡房产红谷滩分行','phone'=>'18070383232'],
['name'=>'陈谷明','com'=>'中环地产金融大街分行','phone'=>'18070380961'],
['name'=>'黄清霞','com'=>'中环地产建设路分行','phone'=>'18070292189'],
['name'=>'孙容勇','com'=>'我爱我家丰和南大道分行','phone'=>'18070139937'],
['name'=>'彭怡宁','com'=>'我爱我家东莲路店','phone'=>'18070132675'],
['name'=>'衷思思','com'=>'中环地产百城经典分行','phone'=>'18070132272'],
['name'=>'熊峻','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'18070118486'],
['name'=>'高爱平','com'=>'我爱我家世纪风情分行','phone'=>'18070110909'],
['name'=>'邓杨','com'=>'鸿基房产新力帝泊湾店','phone'=>'18070096185'],
['name'=>'聂小妹','com'=>'中环地产保集御河湾店','phone'=>'18070072112'],
['name'=>'姚娟娟','com'=>'桥郡房产红谷滩分行','phone'=>'18070070612'],
['name'=>'姚小红','com'=>'中晨地产湾里分行','phone'=>'18070051352'],
['name'=>'谌广艺','com'=>'齐家房产万达茂A区店','phone'=>'18070037689'],
['name'=>'章云云','com'=>'我爱我家水悦湾一区店','phone'=>'18070037430'],
['name'=>'涂海龙','com'=>'中环地产力高君御国际','phone'=>'18070032931'],
['name'=>'龚循保','com'=>'中环地产金沙大道分行','phone'=>'18070031344'],
['name'=>'杨保华','com'=>'鸿基房产丰源淳和店','phone'=>'18070031181'],
['name'=>'徐达','com'=>'鸿基房产象湖平安总店','phone'=>'18046858245'],
['name'=>'徐建华','com'=>'齐家房产梵顿公馆分行','phone'=>'18007098939'],
['name'=>'王平华','com'=>'中环地产金沙逸都分行','phone'=>'18007092087'],
['name'=>'付志辉','com'=>'中环地产文教路分行','phone'=>'18000715225'],
['name'=>'鲍辉烈','com'=>'中环地产文教路分行','phone'=>'18000711331'],
['name'=>'樊芸芸','com'=>'齐家房产莱蒙分行','phone'=>'18000209993'],
['name'=>'黄阳风','com'=>'我爱我家万科公望分行','phone'=>'18000209278'],
['name'=>'毛丽明','com'=>'鸿基房产新力都荟店','phone'=>'18000207927'],
['name'=>'胡丽芝','com'=>'中环地产芳华路分行','phone'=>'18000204381'],
['name'=>'黄红丹','com'=>'鸿基房产象湖平安总店','phone'=>'18000203190'],
['name'=>'周小辉','com'=>'我爱我家恒大城分行','phone'=>'18000202132'],
['name'=>'张良兵','com'=>'万科租售中心四季花城南区店','phone'=>'18000200475'],
['name'=>'张小娃','com'=>'中环地产金沙大道分行','phone'=>'17858623743'],
['name'=>'吴广成','com'=>'中环地产澄湖北大道分行','phone'=>'17779519003'],
['name'=>'贺茂龙','com'=>'齐家房产比华利分行','phone'=>'17779196646'],
['name'=>'胡康','com'=>'我爱我家湾上首座店','phone'=>'17779164780'],
['name'=>'王正','com'=>'中环地产彭家桥分行','phone'=>'17779115869'],
['name'=>'周兵兵','com'=>'中环地产南大高校分行','phone'=>'17779115650'],
['name'=>'周运万','com'=>'中环地产新建中心商业街分行','phone'=>'17779112452'],
['name'=>'戴海霞','com'=>'鸿基房产康城店','phone'=>'17779111356'],
['name'=>'文挣','com'=>'鸿基房产青山湖北大道店','phone'=>'17779110031'],
['name'=>'刘伟','com'=>'中环地产雍锦王府分行','phone'=>'17779109165'],
['name'=>'刘平','com'=>'中环地产洪都新村店','phone'=>'17779104550'],
['name'=>'周丹燕','com'=>'鸿基房产康城店','phone'=>'17770893635'],
['name'=>'艾国保','com'=>'中环地产居住主题分行','phone'=>'17770892277'],
['name'=>'陶玲','com'=>'我爱我家九里水晶岛店','phone'=>'17770889400'],
['name'=>'胡方旭','com'=>'中环地产枫林美庐分行','phone'=>'17770888748'],
['name'=>'付强强','com'=>'中环地产天星湾分行','phone'=>'17770882321'],
['name'=>'袁堃','com'=>'鸿基房产金涛御景店','phone'=>'17770881471'],
['name'=>'魏峰','com'=>'南昌保护伞房产总部','phone'=>'17770878370'],
['name'=>'张明武','com'=>'中环地产幸福诚义分行','phone'=>'17770868092'],
['name'=>'彭凯凯','com'=>'中环地产奥园三组分行','phone'=>'17770861993'],
['name'=>'罗林林','com'=>'南昌保护伞房产总部','phone'=>'17770860359'],
['name'=>'钟日聪','com'=>'鸿基房产新力都荟店','phone'=>'17770796330'],
['name'=>'李春辉','com'=>'中环地产向阳路分行','phone'=>'17770504648'],
['name'=>'张慧','com'=>'中环地产工商学院分行','phone'=>'17770501617'],
['name'=>'龚永进','com'=>'我爱我家中央香榭分行','phone'=>'17770085959'],
['name'=>'李荣','com'=>'中环地产金沙大道分行','phone'=>'17770085868'],
['name'=>'胡铁勇','com'=>'中环地产金沙大道分行','phone'=>'17770080047'],
['name'=>'万志琴','com'=>'中环地产幸福时光三分行','phone'=>'17770069861'],
['name'=>'黄敏','com'=>'鸿基房产朝阳新城分行','phone'=>'17770066829'],
['name'=>'罗云龙','com'=>'我爱我家梦里水乡分行','phone'=>'17770052059'],
['name'=>'肖虎明','com'=>'中环地产恒茂梦时代分行','phone'=>'17770038423'],
['name'=>'徐锦钊','com'=>'鸿基房产新力都荟店','phone'=>'17746699664'],
['name'=>'涂博','com'=>'中环地产工商学院分行','phone'=>'17707959793'],
['name'=>'黄一帆','com'=>'鸿基房产前进路店','phone'=>'17707096233'],
['name'=>'边丽红','com'=>'鸿基房产象湖平安总店','phone'=>'17707082543'],
['name'=>'杨琴琴','com'=>'中环地产金沙大道分行','phone'=>'17707002951'],
['name'=>'吴池杰','com'=>'中环地产恒大城分行','phone'=>'17689231405'],
['name'=>'李凌娟','com'=>'鸿基房产象湖平安分部','phone'=>'17687918692'],
['name'=>'肖顺洋','com'=>'中环地产紫金城分行','phone'=>'17687917606'],
['name'=>'罗恒','com'=>'齐家房产莱蒙分行','phone'=>'17687916558'],
['name'=>'黄星','com'=>'中环地产天星湾分行','phone'=>'17679318802'],
['name'=>'杨启辉','com'=>'中环地产丰和大道分行','phone'=>'17679217951'],
['name'=>'胡盛勇','com'=>'中环地产城市溪地分行','phone'=>'17679216645'],
['name'=>'李强','com'=>'中环地产工商学院分行','phone'=>'17679106657'],
['name'=>'熊德聪','com'=>'中环地产振兴大道分行','phone'=>'17679100683'],
['name'=>'颜辉辉','com'=>'中环地产力高五期分行','phone'=>'17679087516'],
['name'=>'杨松','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'17679002685'],
['name'=>'戴良平','com'=>'鸿基房产翠苑路店','phone'=>'17679000006'],
['name'=>'潘昊东','com'=>'中环地产工商学院分行','phone'=>'17674122055'],
['name'=>'万子艳','com'=>'中环地产湾里分行','phone'=>'17666254543'],
['name'=>'曹芸菲','com'=>'中环地产金沙大道分行','phone'=>'17625567657'],
['name'=>'喻敏杰','com'=>'鸿基房产象湖平安总店','phone'=>'17607908026'],
['name'=>'李港','com'=>'中环地产昌南月星分行','phone'=>'17607099244'],
['name'=>'余振兴','com'=>'齐家房产世纪花园店','phone'=>'17607096069'],
['name'=>'陈可欣','com'=>'中环地产店中央首府分行','phone'=>'17607094191'],
['name'=>'宋春华','com'=>'我爱我家梵顿公馆二店','phone'=>'17607091991'],
['name'=>'陈萍萍','com'=>'齐家房产莱蒙分行','phone'=>'17607083080'],
['name'=>'郭娟','com'=>'中好房屋力高滨江国际店','phone'=>'17607053327'],
['name'=>'吴敏','com'=>'鸿基房产幸福里分行','phone'=>'17607009173'],
['name'=>'晏世文','com'=>'齐家房产万达东区店','phone'=>'17607003559'],
['name'=>'程超','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'17607001875'],
['name'=>'徐志远','com'=>'鸿基房产芳草路店','phone'=>'17507095929'],
['name'=>'陈文颖','com'=>'中环地产平安东门分行','phone'=>'17379626252'],
['name'=>'郝小花','com'=>'中环地产工商学院分行','phone'=>'17379177460'],
['name'=>'喻杰','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'17379165669'],
['name'=>'黄应华','com'=>'鸿基房产洪都北苑店','phone'=>'17379163988'],
['name'=>'聂兰英','com'=>'中环地产银河城分行','phone'=>'17379156054'],
['name'=>'姜尔琪','com'=>'中环地产金沙大道分行','phone'=>'17379128103'],
['name'=>'张林军','com'=>'中环地产绿腾雅苑分行','phone'=>'17379127029'],
['name'=>'吴邦业','com'=>'中环地产洛阳东路分行','phone'=>'17370889097'],
['name'=>'袁旭东','com'=>'中环地产艾溪康桥分行','phone'=>'17370888695'],
['name'=>'严德明','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'17370885737'],
['name'=>'万拉拉','com'=>'中环地产中大永通分行','phone'=>'17370867513'],
['name'=>'叶新宽','com'=>'中环地产康泽园分行','phone'=>'17370866160'],
['name'=>'杨平','com'=>'鸿基房产新力金沙湾店','phone'=>'17370843007'],
['name'=>'李新亮','com'=>'齐家房产鹿璟分行','phone'=>'17370453544'],
['name'=>'何昌华','com'=>'中环地产康城分行','phone'=>'17370087582'],
['name'=>'魏争争','com'=>'鸿基房产八一大道店','phone'=>'17370072559'],
['name'=>'郭逢春','com'=>'中环地产藤旺居分行','phone'=>'17370069710'],
['name'=>'李小东','com'=>'中环地产奥园三组分行','phone'=>'17370061910'],
['name'=>'屈国静','com'=>'中环地产天一城分行','phone'=>'17370053779'],
['name'=>'陈佳明','com'=>'中环地产中大城分行','phone'=>'17370052111'],
['name'=>'李兵','com'=>'鸿基房产恒大绿洲店','phone'=>'17370032700'],
['name'=>'范万成','com'=>'中环地产金沙大道分行','phone'=>'17370029355'],
['name'=>'熊良广','com'=>'创嘉房产红谷滩分行','phone'=>'17370028793'],
['name'=>'万子朋','com'=>'中环地产玫瑰华庭分行','phone'=>'17370025453'],
['name'=>'吴强','com'=>'万科租售中心地铁万科时代广场店','phone'=>'17370009105'],
['name'=>'罗文强','com'=>'中环地产诚义路卓越分行','phone'=>'17370006949'],
['name'=>'陈涛','com'=>'中环地产中大城分行','phone'=>'17370001559'],
['name'=>'付为栋','com'=>'我爱我家丰和中大道店','phone'=>'17346669046'],
['name'=>'邓斌海','com'=>'齐家房产万达东区店','phone'=>'17346654232'],
['name'=>'陈明','com'=>'中环地产诚义路卓越分行','phone'=>'17346629065'],
['name'=>'熊彪','com'=>'中环地产正荣恒大分行','phone'=>'17307098681'],
['name'=>'邬书龙','com'=>'中好房屋力高滨江国际店','phone'=>'17307091618'],
['name'=>'余佳珍','com'=>'中好房屋力高滨江国际店','phone'=>'17307091618'],
['name'=>'魏杰','com'=>'中环地产景城南门分行','phone'=>'17307006302'],
['name'=>'金立强','com'=>'中环地产力高君御国际','phone'=>'16679097487'],
['name'=>'解乐','com'=>'鸿基房产幸福时光店','phone'=>'16679087236'],
['name'=>'王令宗','com'=>'中环地产金嘉名筑分行','phone'=>'16607009615'],
['name'=>'吴丽军','com'=>'中环地产象湖分行','phone'=>'16607005005'],
['name'=>'熊志科','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15979197077'],
['name'=>'肖卫健','com'=>'齐家房产比华利分行','phone'=>'15979192318'],
['name'=>'叶松保','com'=>'中环地产金域名都分行','phone'=>'15979191460'],
['name'=>'谭美珍','com'=>'中环地产正荣澄湖分行','phone'=>'15979189815'],
['name'=>'陈琳','com'=>'中环地产振兴大道分行','phone'=>'15979189691'],
['name'=>'王明英','com'=>'鸿基房产南大高校店','phone'=>'15979185192'],
['name'=>'刘佳强','com'=>'中环地产澜湖商业街分行','phone'=>'15979173521'],
['name'=>'占雨勇','com'=>'中环地产平安南门分行','phone'=>'15979161635'],
['name'=>'俞君','com'=>'中环地产恒大城分行','phone'=>'15979140864'],
['name'=>'黄加庆','com'=>'中环地产经纬府邸店','phone'=>'15979111106'],
['name'=>'谢辉','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15979095398'],
['name'=>'杨润松','com'=>'中环地产南昌八中分行','phone'=>'15979094739'],
['name'=>'李汉光','com'=>'中环地产庐山花园分行','phone'=>'15979089063'],
['name'=>'熊晶晶','com'=>'来淘房产红谷滩店','phone'=>'15979070154'],
['name'=>'李冬英','com'=>'鸿基房产青山路一店','phone'=>'15979067574'],
['name'=>'金美','com'=>'中环地产兴华路分行','phone'=>'15979067356'],
['name'=>'张令秀','com'=>'鸿基房产洪都店','phone'=>'15979056026'],
['name'=>'姚允皓','com'=>'中环地产幸福滨河分行','phone'=>'15979054605'],
['name'=>'陶艳梅','com'=>'中环地产金沙大道分行','phone'=>'15979047158'],
['name'=>'余铭','com'=>'鸿基房产朝阳新城分行','phone'=>'15979034049'],
['name'=>'喻庆','com'=>'鸿基房产象湖平安总店','phone'=>'15979029617'],
['name'=>'李玉兰','com'=>'我爱我家九里象湖二店','phone'=>'15979026721'],
['name'=>'王刚','com'=>'中环地产正荣大湖之都分行','phone'=>'15979016107'],
['name'=>'王玉杰','com'=>'鸿基房产祥瑞店','phone'=>'15979012700'],
['name'=>'黄安','com'=>'中环地产诚义路卓越分行','phone'=>'15979006619'],
['name'=>'姚永红','com'=>'我爱我家斗柏路分行','phone'=>'15970677828'],
['name'=>'邓叶钦','com'=>'鸿基房产金沙大道店','phone'=>'15970677006'],
['name'=>'张家彪','com'=>'南昌博越房产悦城分行','phone'=>'15970660242'],
['name'=>'龚鹏','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15970651625'],
['name'=>'陈见广','com'=>'中环地产诚义路卓越分行','phone'=>'15970636520'],
['name'=>'樊圆元','com'=>'中环地产银河城分行','phone'=>'15970632472'],
['name'=>'徐鹏','com'=>'齐家房产梵顿公馆分行','phone'=>'15970626274'],
['name'=>'赵盼','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15970625914'],
['name'=>'钱志刚','com'=>'我爱我家正荣御尊店','phone'=>'15970498213'],
['name'=>'罗小花','com'=>'中环地产拉菲公馆分行','phone'=>'15970483046'],
['name'=>'吕华','com'=>'中环地产天沐君湖分行','phone'=>'15970441160'],
['name'=>'熊闵华','com'=>'齐家房产华府分行','phone'=>'15970440334'],
['name'=>'彭文亮','com'=>'鸿基房产丁香怡景店','phone'=>'15970422274'],
['name'=>'彭有根','com'=>'中环地产工商学院分行','phone'=>'15970406060'],
['name'=>'晏强','com'=>'鸿基房产洛阳东路店','phone'=>'15907097004'],
['name'=>'徐卫峰','com'=>'鸿基房产力高店','phone'=>'15907088813'],
['name'=>'邓盛勇','com'=>'鸿基房产友邦皇家公馆店','phone'=>'15907002818'],
['name'=>'金平花','com'=>'中晨地产红谷新城店','phone'=>'15907002058'],
['name'=>'雷香港','com'=>'中环地产文教南店','phone'=>'15890739538'],
['name'=>'徐文平','com'=>'鸿基房产幸福时光店','phone'=>'15879198677'],
['name'=>'袁军','com'=>'鸿基房产幸福时光店','phone'=>'15879198577'],
['name'=>'陶燕兵','com'=>'中环地产金域名都分行','phone'=>'15879193323'],
['name'=>'曾鹏','com'=>'我爱我家湾里幸福路店','phone'=>'15879192869'],
['name'=>'熊子龙','com'=>'中环地产奥林匹克分行','phone'=>'15879191313'],
['name'=>'毛强','com'=>'中环地产凤凰城分行','phone'=>'15879180815'],
['name'=>'钟海波','com'=>'鸿基房产天赐良园分行','phone'=>'15879168737'],
['name'=>'缪鹏云','com'=>'鸿基房产天赐良园分行','phone'=>'15879161652'],
['name'=>'陈大伟','com'=>'鸿基房产青岚大道店','phone'=>'15879155205'],
['name'=>'高珊','com'=>'中环地产昌南月星分行','phone'=>'15879152460'],
['name'=>'章海平','com'=>'鸿基房产青山湖小区分行','phone'=>'15879152237'],
['name'=>'陈立','com'=>'中环地产龙江花园分行','phone'=>'15879131205'],
['name'=>'洪海涛','com'=>'中环地产象湖中幸分行','phone'=>'15879129754'],
['name'=>'李运婷','com'=>'鸿基房产象湖国际店','phone'=>'15879108387'],
['name'=>'袁满','com'=>'鸿基房产阳明东路二部','phone'=>'15879106311'],
['name'=>'肖云云','com'=>'中环地产诚义路卓越分行','phone'=>'15879103971'],
['name'=>'邓林','com'=>'中环地产力高都会分行','phone'=>'15879099611'],
['name'=>'刘政','com'=>'中环地产紫金园分行','phone'=>'15879098824'],
['name'=>'徐志强','com'=>'中环地产船山路分行','phone'=>'15879093560'],
['name'=>'李进进','com'=>'中环地产星诚幸福分行','phone'=>'15879093301'],
['name'=>'余小丽','com'=>'中环地产幸福滨河分行','phone'=>'15879088647'],
['name'=>'金小强','com'=>'中环地产金融大街分行','phone'=>'15879083857'],
['name'=>'姜国鹏','com'=>'中环地产工商学院分行','phone'=>'15879072720'],
['name'=>'姜红红','com'=>'中环地产海嘉路分行','phone'=>'15879071719'],
['name'=>'衷汉枭','com'=>'鸿基房产象湖国际店','phone'=>'15879068992'],
['name'=>'魏建辉','com'=>'鸿基房产昌南分行','phone'=>'15879067525'],
['name'=>'李骞','com'=>'中晨地产象湖中心店','phone'=>'15879065505'],
['name'=>'万俊强','com'=>'中环地产诚义学校分行','phone'=>'15879062662'],
['name'=>'胡涛','com'=>'云房通房产总部','phone'=>'15879052896'],
['name'=>'江男','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15879049294'],
['name'=>'徐海','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15879049275'],
['name'=>'陈德友','com'=>'中环地产世贸分行','phone'=>'15879048992'],
['name'=>'梁俊林','com'=>'中环地产世贸分行','phone'=>'15879048992'],
['name'=>'陈贤军','com'=>'齐家房产红谷分行','phone'=>'15879044450'],
['name'=>'罗建强','com'=>'芳鑫房产红角州店','phone'=>'15879042046'],
['name'=>'姜成朋','com'=>'我爱我家永通澄湖西路店','phone'=>'15879035097'],
['name'=>'胡明','com'=>'我爱我家祥瑞福园店','phone'=>'15879031587'],
['name'=>'曾建英','com'=>'中环地产滨湖花城分行','phone'=>'15879027360'],
['name'=>'盛厚委','com'=>'中环地产香逸澜湾分行','phone'=>'15879018182'],
['name'=>'吴灵','com'=>'中环地产东方银座分行','phone'=>'15870695300'],
['name'=>'叶定湖','com'=>'中环地产华润橡府分行','phone'=>'15870692770'],
['name'=>'龚浩','com'=>'齐家房产红谷分行','phone'=>'15870689005'],
['name'=>'黄作鑫','com'=>'鸿基房产力高店','phone'=>'15870686612'],
['name'=>'曹树平','com'=>'鸿基房产振中小区店','phone'=>'15870680925'],
['name'=>'郑志豪','com'=>'鸿基房产华南城分行','phone'=>'15870675150'],
['name'=>'张泽宝','com'=>'鸿基房产天琴湾店','phone'=>'15870663514'],
['name'=>'廖天保','com'=>'齐家房产比华利分行','phone'=>'15870661385'],
['name'=>'胡文心','com'=>'中环地产金沙大道分行','phone'=>'15870659898'],
['name'=>'欧阳伟伟','com'=>'鸿基房产金沙大道天虹店','phone'=>'15870649609'],
['name'=>'张婵','com'=>'中环地产天沐君湖分行','phone'=>'15870621104'],
['name'=>'胡裕平','com'=>'鸿基房产莱卡长堎市场店','phone'=>'15870611287'],
['name'=>'陈超','com'=>'中环地产音乐花园分行','phone'=>'15870023248'],
['name'=>'赵虎','com'=>'鸿基房产水岸菁华店','phone'=>'15870021668'],
['name'=>'袁晖','com'=>'鸿基房产星加坡花园店','phone'=>'15870020266'],
['name'=>'胡江','com'=>'中环地产力天阳光分行','phone'=>'15870015280'],
['name'=>'周俞','com'=>'中环地产恒大绿洲分行','phone'=>'15870014717'],
['name'=>'刘安安','com'=>'鸿基房产罗马壹号店','phone'=>'15870003113'],
['name'=>'邹志敏','com'=>'齐家房产比华利分行','phone'=>'15870002837'],
['name'=>'郑海莉','com'=>'齐家房产比华利分行','phone'=>'15870002730'],
['name'=>'丁小红','com'=>'中环地产南京东路店','phone'=>'15857181810'],
['name'=>'汪大梅','com'=>'乐有家洪城比华利店','phone'=>'15824451224'],
['name'=>'勒伍兵','com'=>'中环地产凤凰城分行','phone'=>'15807910575'],
['name'=>'杨礼宗','com'=>'中环地产拉菲公馆分行','phone'=>'15807005658'],
['name'=>'程丽','com'=>'齐家房产海德堡店','phone'=>'15797944134'],
['name'=>'黄辉','com'=>'鸿基房产城邦店','phone'=>'15797941477'],
['name'=>'梁苏友','com'=>'鸿基房产保集半岛分行','phone'=>'15797940604'],
['name'=>'凌霞','com'=>'我爱我家新建大道分行','phone'=>'15797846723'],
['name'=>'刘千梅','com'=>'鸿基房产梅湖明珠店','phone'=>'15797828825'],
['name'=>'陈礼涛','com'=>'中环地产学府馨苑分行','phone'=>'15797815872'],
['name'=>'刘红浩','com'=>'中环地产香溢花城分行','phone'=>'15797793984'],
['name'=>'叶春梅','com'=>'中环地产诚义路卓越分行','phone'=>'15797790816'],
['name'=>'熊艳','com'=>'中环地产万达东分行','phone'=>'15797787715'],
['name'=>'魏昊','com'=>'中环地产青春家园分行','phone'=>'15797783899'],
['name'=>'胡翔','com'=>'中环地产象湖天虹分行','phone'=>'15797780398'],
['name'=>'李绍基','com'=>'中环地产幸福时光南门分行','phone'=>'15797775886'],
['name'=>'陶强云','com'=>'鸿基房产象湖平安总店','phone'=>'15797746975'],
['name'=>'刘长安','com'=>'中环地产店中央首府分行','phone'=>'15797727191'],
['name'=>'涂文华','com'=>'中环地产奥克斯盛世华庭分行','phone'=>'15779579381'],
['name'=>'尧晓艳','com'=>'中环地产诚义路卓越分行','phone'=>'15779577680'],
['name'=>'鲍伟炜','com'=>'中环地产幸福时光三分行','phone'=>'15779573963'],
['name'=>'黄振','com'=>'中环地产工商学院分行','phone'=>'15779570807'],
['name'=>'王豪','com'=>'齐家房产联发店','phone'=>'15779507205'],
['name'=>'周根鑫','com'=>'齐家房产尚城分行','phone'=>'15770827398'],
['name'=>'万涛','com'=>'鸿基房产保集半岛分行','phone'=>'15727652771'],
['name'=>'黄乐曦','com'=>'齐家房产世纪店','phone'=>'15727652455'],
['name'=>'李小牛','com'=>'齐家房产尚城分行','phone'=>'15727649848'],
['name'=>'黄新伟','com'=>'中环地产滨河壹品分行','phone'=>'15727641693'],
['name'=>'邓新文','com'=>'鸿基房产保集半岛分行','phone'=>'15727641377'],
['name'=>'汪诗文','com'=>'鸿基房产恒大珺庭店','phone'=>'15727639688'],
['name'=>'孙才英','com'=>'中环地产芳华路分行','phone'=>'15727630779'],
['name'=>'朱晓强','com'=>'中环地产碧海云天分行','phone'=>'15727630735'],
['name'=>'金武','com'=>'中环地产万达广场分行','phone'=>'15727630578'],
['name'=>'万宏','com'=>'鸿基房产正荣御品店','phone'=>'15717922144'],
['name'=>'叶青枝','com'=>'鸿基房产朝阳小学店','phone'=>'15717099803'],
['name'=>'邓云辉','com'=>'中环地产保集御河湾店','phone'=>'15717099163'],
['name'=>'熊芸','com'=>'中环地产金沙大道分行','phone'=>'15717007471'],
['name'=>'况泰权','com'=>'中环地产蔚蓝郡分行','phone'=>'15717003181'],
['name'=>'黄挺','com'=>'乐有家洪城比华利店','phone'=>'15706295412'],
['name'=>'甘嘉俊','com'=>'齐家房产世纪花园店','phone'=>'15679549086'],
['name'=>'李景赛','com'=>'万科租售中心四季花城北区店','phone'=>'15679172529'],
['name'=>'肖杰','com'=>'中环地产财经大学分行','phone'=>'15679169414'],
['name'=>'何尚','com'=>'中环地产闽江路分行','phone'=>'15679133733'],
['name'=>'张启苹','com'=>'中环地产青山湖国际分行','phone'=>'15579223882'],
['name'=>'程跃文','com'=>'中环地产象湖幸福里分行','phone'=>'15579160809'],
['name'=>'涂玉良','com'=>'齐家房产奥克斯店','phone'=>'15579157291'],
['name'=>'揭宁','com'=>'中环地产幸福时光二分行','phone'=>'15579136518'],
['name'=>'李诗民','com'=>'鸿基房产兴华路店','phone'=>'15570371519'],
['name'=>'胡志平','com'=>'中环地产新力分行','phone'=>'15505905850'],
['name'=>'何建锋','com'=>'中环地产工商学院分行','phone'=>'15397945646'],
['name'=>'王超毅','com'=>'中环地产正荣朗逸湾分行','phone'=>'15387917615'],
['name'=>'万明','com'=>'中天房产四中店','phone'=>'15387915285'],
['name'=>'万绍珍','com'=>'鸿基房产金易店','phone'=>'15350118663'],
['name'=>'刘庆雯','com'=>'鸿基房产金沙大道店','phone'=>'15350114159'],
['name'=>'刘毅峰','com'=>'鸿基房产保集半岛分行','phone'=>'15350005049'],
['name'=>'万涛','com'=>'中环地产艾溪康桥分行','phone'=>'15307008513'],
['name'=>'杨清','com'=>'中环地产天星湾分行','phone'=>'15307005118'],
['name'=>'熊文','com'=>'中环地产怡兰苑分行','phone'=>'15297919136'],
['name'=>'谢候群','com'=>'中环地产东方红分行','phone'=>'15279663867'],
['name'=>'淦方巧','com'=>'中环地产金沙大道分行','phone'=>'15279196773'],
['name'=>'刘颖','com'=>'中环地产星港小镇分行','phone'=>'15279194792'],
['name'=>'彭勇强','com'=>'中环地产东岳大道分行','phone'=>'15279192289'],
['name'=>'熊鸿臣','com'=>'芳鑫房产红角州店','phone'=>'15279187139'],
['name'=>'王海华','com'=>'鸿基房产保集半岛分行','phone'=>'15279159193'],
['name'=>'魏阳阳','com'=>'中环地产怡兰苑分行','phone'=>'15279158791'],
['name'=>'罗亻毛亻毛','com'=>'鸿基房产绿地香颂店','phone'=>'15279153228'],
['name'=>'朱文','com'=>'鸿基房产朝阳正荣店','phone'=>'15279148784'],
['name'=>'李传超','com'=>'鸿基房产新建中心店','phone'=>'15279124807'],
['name'=>'甘凌云','com'=>'中环地产金沙大道分行','phone'=>'15279124553'],
['name'=>'汪秀玲','com'=>'万科租售中心万科城店','phone'=>'15279100156'],
['name'=>'喻文强','com'=>'齐家房产奥克斯店','phone'=>'15270991620'],
['name'=>'夏雨川','com'=>'齐家房产万达茂A区店','phone'=>'15270983117'],
['name'=>'刘龙','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15270982553'],
['name'=>'张伟','com'=>'中环地产保集一分行','phone'=>'15270975004'],
['name'=>'夏丛','com'=>'中环地产南京东路店','phone'=>'15270967429'],
['name'=>'罗强','com'=>'中环地产昌北分行','phone'=>'15270958903'],
['name'=>'舒秀平','com'=>'鸿基房产中大紫都分行','phone'=>'15270950800'],
['name'=>'杨振威','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15270945069'],
['name'=>'胡春燕','com'=>'中环地产金涛御景分行','phone'=>'15270937116'],
['name'=>'吴治国','com'=>'齐家房产莱蒙分行','phone'=>'15270934625'],
['name'=>'任志勇','com'=>'我爱我家绿地兰宫店','phone'=>'15270916125'],
['name'=>'喻文','com'=>'中环地产文教路分行','phone'=>'15270908092'],
['name'=>'毛美发','com'=>'中环地产金沙大道分行','phone'=>'15270907811'],
['name'=>'赖加登','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15270905244'],
['name'=>'潘希宁','com'=>'鸿基房产绿地山庄店','phone'=>'15270898578'],
['name'=>'夏淑云','com'=>'我爱我家新建大道分行','phone'=>'15270896942'],
['name'=>'万月华','com'=>'鸿基房产南大高校店','phone'=>'15270893386'],
['name'=>'李敏','com'=>'中环地产梅岭分行','phone'=>'15270890722'],
['name'=>'吴绿英','com'=>'中环地产滨河壹品分行','phone'=>'15270885256'],
['name'=>'侯松','com'=>'中环地产澄湖北大道分行','phone'=>'15270877371'],
['name'=>'高星','com'=>'中环地产抚生路分行','phone'=>'15270872856'],
['name'=>'翟聪聪','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15270872017'],
['name'=>'魏鹏','com'=>'我爱我家幸福时光二店','phone'=>'15270870868'],
['name'=>'吴刚','com'=>'中环地产保集一分行','phone'=>'15270870688'],
['name'=>'何江涛','com'=>'鸿基房产景奥店','phone'=>'15270846993'],
['name'=>'邹纪康','com'=>'中环地产工商学院分行','phone'=>'15270832395'],
['name'=>'胡辉刚','com'=>'鸿基房产璜垦店','phone'=>'15270028686'],
['name'=>'熊菊兰','com'=>'芳鑫房产红角州店','phone'=>'15270025581'],
['name'=>'涂巧玲','com'=>'中环地产恒大绿洲分行','phone'=>'15270025078'],
['name'=>'张童童','com'=>'中环地产恒大珺庭分行','phone'=>'15270017900'],
['name'=>'周智慧','com'=>'中环地产桥郡分行','phone'=>'15270010990'],
['name'=>'唐小龙','com'=>'中环地产工商学院分行','phone'=>'15270005449'],
['name'=>'邹菊萍','com'=>'中环地产幸福时光三分行','phone'=>'15270005025'],
['name'=>'熊伟','com'=>'中环地产下罗分行','phone'=>'15270000029'],
['name'=>'张家伟','com'=>'中环地产新力分行','phone'=>'15180583257'],
['name'=>'李冬波','com'=>'中环地产金沙大道分行','phone'=>'15180506387'],
['name'=>'芦国金','com'=>'中环地产青山湖大道分行','phone'=>'15180485408'],
['name'=>'万晨','com'=>'我爱我家铁路八村分行','phone'=>'15180468258'],
['name'=>'胡章建','com'=>'鸿基房产东方苑店','phone'=>'15180464576'],
['name'=>'赵志勇','com'=>'中环地产芳诚路分行','phone'=>'15180458846'],
['name'=>'万亚俊','com'=>'鸿基房产船山路店','phone'=>'15180441662'],
['name'=>'宣玉','com'=>'中环地产铁路九村分店','phone'=>'15180428872'],
['name'=>'邓成龙','com'=>'中环地产保集御河湾店','phone'=>'15180425393'],
['name'=>'陈学仁','com'=>'中环地产金嘉名筑分行','phone'=>'15180411057'],
['name'=>'徐儒斌','com'=>'中环地产紫荆路分行','phone'=>'15180410021'],
['name'=>'徐志胜','com'=>'鸿基房产幸福时光店','phone'=>'15180406140'],
['name'=>'江萍锋','com'=>'中环地产三纬路分行','phone'=>'15180310014'],
['name'=>'刘红文','com'=>'鸿基房产包家花园店','phone'=>'15180185782'],
['name'=>'郑志刚','com'=>'中环地产康城一分行','phone'=>'15180183748'],
['name'=>'喻智钢','com'=>'中环地产紫金城分行','phone'=>'15180180235'],
['name'=>'胡海飞','com'=>'中环地产白帝沙分行','phone'=>'15180177467'],
['name'=>'李乔琳','com'=>'我爱我家丰源淳和店','phone'=>'15180177409'],
['name'=>'余浩','com'=>'中环地产玺园分行','phone'=>'15180167170'],
['name'=>'刘燕琴','com'=>'中环地产中海铂宫分行','phone'=>'15180135412'],
['name'=>'李霞','com'=>'鸿基房产荣昌店','phone'=>'15180134490'],
['name'=>'田小春','com'=>'鸿基房产广场南路分部','phone'=>'15180119423'],
['name'=>'刘京','com'=>'中环地产经纬府邸店','phone'=>'15180104641'],
['name'=>'毛燕鹏','com'=>'中环地产金沙大道分行','phone'=>'15179382834'],
['name'=>'陶勇强','com'=>'中环地产金沙二路分行','phone'=>'15179188699'],
['name'=>'熊聪','com'=>'中环地产绿地玫瑰城分行','phone'=>'15179186312'],
['name'=>'陈鹏','com'=>'齐家房产红谷分行','phone'=>'15179184636'],
['name'=>'刘海','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15179180182'],
['name'=>'夏世恒','com'=>'齐家房产世纪花园店','phone'=>'15179177769'],
['name'=>'彭志敏','com'=>'鸿基房产曙光馨居店','phone'=>'15179173990'],
['name'=>'熊成强','com'=>'鸿基房产心怡广场店','phone'=>'15179172311'],
['name'=>'胡建军','com'=>'鸿基房产景奥店','phone'=>'15179167529'],
['name'=>'戴聪','com'=>'我爱我家丰源淳和店','phone'=>'15179166121'],
['name'=>'聂赟','com'=>'博仁房产总部','phone'=>'15179159166'],
['name'=>'洪纪武','com'=>'万科租售中心四季花城北区店','phone'=>'15179154175'],
['name'=>'涂阳武','com'=>'中环地产银河城分行','phone'=>'15179138240'],
['name'=>'熊进辉','com'=>'中环地产金沙大道分行','phone'=>'15179137703'],
['name'=>'陈洁','com'=>'中环地产贵都国际分行','phone'=>'15179119397'],
['name'=>'刘学成','com'=>'中环地产岭口路分行','phone'=>'15179109446'],
['name'=>'金冬冬','com'=>'中环地产万达东分行','phone'=>'15179108640'],
['name'=>'朱帅兵','com'=>'鸿基房产广场南路分部','phone'=>'15179101751'],
['name'=>'刘青','com'=>'中环地产工商学院分行','phone'=>'15170800544'],
['name'=>'何慧','com'=>'中环地产恒大绿洲分行','phone'=>'15170703921'],
['name'=>'刘伟','com'=>'中环地产青山湖分行','phone'=>'15170487031'],
['name'=>'刘越','com'=>'中环地产澜湖郡分行','phone'=>'15170483880'],
['name'=>'徐聪聪','com'=>'安泰居置业地中海阳光分行','phone'=>'15170446628'],
['name'=>'庄志志','com'=>'鸿基房产世纪风情店','phone'=>'15170433873'],
['name'=>'黄凯','com'=>'我爱我家中央香榭分行','phone'=>'15170431193'],
['name'=>'胡强兵','com'=>'齐家房产莱蒙分行','phone'=>'15170422300'],
['name'=>'雷玲','com'=>'鸿基房产阳明东路店','phone'=>'15170413641'],
['name'=>'罗优','com'=>'齐家房产莱蒙分行','phone'=>'15170403040'],
['name'=>'邓建平','com'=>'中环地产拉菲公馆分行','phone'=>'15170229235'],
['name'=>'詹文坚','com'=>'中环地产幸福时光三分行','phone'=>'15170219387'],
['name'=>'周望军','com'=>'中环地产文教路恒茂花园店','phone'=>'15170217733'],
['name'=>'龚素贞','com'=>'鸿基房产红谷滩店','phone'=>'15170213366'],
['name'=>'徐武华','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15170205120'],
['name'=>'缪诺愚','com'=>'鸿基房产新上海路分行','phone'=>'15170202676'],
['name'=>'周冬','com'=>'中环地产聚仁店','phone'=>'15170087463'],
['name'=>'付萍','com'=>'中环地产洪都新村店','phone'=>'15170077607'],
['name'=>'江先羽','com'=>'中环地产南天金源店','phone'=>'15170077084'],
['name'=>'王蟠正','com'=>'鸿基房产北京东路分店','phone'=>'15170049902'],
['name'=>'李建雄','com'=>'鸿基房产新力金沙湾店','phone'=>'15170044964'],
['name'=>'邹利仔','com'=>'我爱我家景城名郡分行','phone'=>'15170041398'],
['name'=>'张凌斌','com'=>'中环地产金沙大道分行','phone'=>'15170041191'],
['name'=>'姜青鹏','com'=>'中环地产银三角恒大分行','phone'=>'15170036644'],
['name'=>'蔡淑兰','com'=>'我爱我家爱国路店','phone'=>'15170023628'],
['name'=>'况昊翔','com'=>'中环地产南大附中分行','phone'=>'15170020597'],
['name'=>'何丽芳','com'=>'我爱我家景城名郡分行','phone'=>'15170005065'],
['name'=>'李志军','com'=>'中环地产金沙大道分行','phone'=>'15170004559'],
['name'=>'万嘉欣','com'=>'中环地产金盘路分行','phone'=>'15170000630'],
['name'=>'游晨辉','com'=>'中环地产星诚幸福分行','phone'=>'15157168120'],
['name'=>'夏雷','com'=>'鸿基房产永叔路店','phone'=>'15083845194'],
['name'=>'章立晨','com'=>'齐家房产万达茂A区店','phone'=>'15083825361'],
['name'=>'朱镕璟','com'=>'齐家房产万达茂A区店','phone'=>'15083825355'],
['name'=>'谢小剑','com'=>'中环地产湖景分行','phone'=>'15083817726'],
['name'=>'周美健','com'=>'鸿基房产南天金源店','phone'=>'15083812515'],
['name'=>'郭海','com'=>'中环地产紫金城分行','phone'=>'15083554327'],
['name'=>'龚紫超','com'=>'鸿基房产昌南分行','phone'=>'15083533740'],
['name'=>'李红梅','com'=>'鸿基房产壹品中央店','phone'=>'15083504930'],
['name'=>'陈文强','com'=>'我爱我家白沙地店','phone'=>'15079317275'],
['name'=>'万志','com'=>'齐家房产莱蒙分行','phone'=>'15079191089'],
['name'=>'万云龙','com'=>'鸿基房产金沙大道店','phone'=>'15079182119'],
['name'=>'席平安','com'=>'鸿基房产莱蒙中心店','phone'=>'15079181235'],
['name'=>'刘成','com'=>'中环地产国安路店','phone'=>'15079173115'],
['name'=>'刘兵','com'=>'中环地产象湖中幸分行','phone'=>'15079171662'],
['name'=>'王红','com'=>'中环地产南京东路店','phone'=>'15079167127'],
['name'=>'姜辉','com'=>'中环地产莲四中分行','phone'=>'15079163512'],
['name'=>'殷雪雪','com'=>'鸿基房产金沙大道店','phone'=>'15079161582'],
['name'=>'邱雪梅','com'=>'桥郡房产红谷滩分行','phone'=>'15079158589'],
['name'=>'毛震霖','com'=>'中环地产体育馆分行','phone'=>'15079151252'],
['name'=>'高嘉竣','com'=>'中环地产迎宾大道店','phone'=>'15079151059'],
['name'=>'胡志高','com'=>'齐家房产莱蒙分行','phone'=>'15079131513'],
['name'=>'罗文','com'=>'中环地产城开国际分行','phone'=>'15079128510'],
['name'=>'钟卫华','com'=>'中环地产诚义路卓越分行','phone'=>'15079126552'],
['name'=>'付新谋','com'=>'中环地产皇冠国际二分行','phone'=>'15079124559'],
['name'=>'饶敏','com'=>'我爱我家景城名郡三店','phone'=>'15079112593'],
['name'=>'魏松松','com'=>'鸿基房产广场东路店','phone'=>'15079106596'],
['name'=>'邹韩','com'=>'鸿基房产盛世店','phone'=>'15079100562'],
['name'=>'罗云','com'=>'芳鑫房产红角州店','phone'=>'15079098773'],
['name'=>'彭琼华','com'=>'我爱我家皇冠国际店','phone'=>'15079096575'],
['name'=>'李年迎','com'=>'鸿基房产水岸菁华店','phone'=>'15079096415'],
['name'=>'邹庆飞','com'=>'中环地产华润朝阳分行','phone'=>'15079095653'],
['name'=>'罗晨阳','com'=>'鸿基房产象湖平安总店','phone'=>'15079090853'],
['name'=>'钟小妹','com'=>'博仁房产总部','phone'=>'15079089097'],
['name'=>'徐斌华','com'=>'中环地产银河城分行','phone'=>'15079086979'],
['name'=>'罗震','com'=>'中环地产诚义路卓越分行','phone'=>'15079073504'],
['name'=>'倪小洋','com'=>'中环地产金沙逸城二分行','phone'=>'15079064176'],
['name'=>'叶松林','com'=>'中环地产青山湖大道分行','phone'=>'15079062416'],
['name'=>'黄临川','com'=>'中环地产金沙逸城分行','phone'=>'15079061493'],
['name'=>'刘荣龙','com'=>'鸿基房产新力金沙湾店','phone'=>'15079058697'],
['name'=>'谢智星','com'=>'中环地产金域名都分行','phone'=>'15079023343'],
['name'=>'姜珑','com'=>'鸿基房产新溪桥店','phone'=>'15079017397'],
['name'=>'余文玉','com'=>'齐家房产鹿璟分行','phone'=>'15079014903'],
['name'=>'姚辉群','com'=>'中环地产幸福时光二分行','phone'=>'15070989786'],
['name'=>'舒三毛','com'=>'鸿基房产象湖平安分部','phone'=>'15070985521'],
['name'=>'袁玉民','com'=>'中环地产白帝沙分行','phone'=>'15070979026'],
['name'=>'李祥','com'=>'中环地产庐山南大道分行','phone'=>'15070963830'],
['name'=>'黄志诚','com'=>'中晨地产坛子口店','phone'=>'15070958551'],
['name'=>'陈玲','com'=>'中环地产滨湖花城分行','phone'=>'15070952663'],
['name'=>'张悦','com'=>'鸿基房产象湖平安分部','phone'=>'15070941274'],
['name'=>'徐怡杰','com'=>'中环地产金沙大道分行','phone'=>'15070929463'],
['name'=>'万云','com'=>'齐家房产下罗分行','phone'=>'15070928104'],
['name'=>'黄龙华','com'=>'我爱我家莱蒙都会店','phone'=>'15070925359'],
['name'=>'陶桓龙','com'=>'中环地产金沙大道分行','phone'=>'15070920705'],
['name'=>'蔡燕如','com'=>'中环地产金涛御景分行','phone'=>'15070920243'],
['name'=>'夏昆','com'=>'伟创房产总部','phone'=>'15070915873'],
['name'=>'邹赵慧','com'=>'我爱我家景城名郡分行','phone'=>'15070900165'],
['name'=>'车兵根','com'=>'中环地产绿地玫瑰城分行','phone'=>'15070890755'],
['name'=>'周兴达','com'=>'我爱我家景城名郡分行','phone'=>'15070888708'],
['name'=>'谢旭文','com'=>'鸿基房产天赐良园分行','phone'=>'15070887101'],
['name'=>'涂小菊','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15070881334'],
['name'=>'龚著成','com'=>'齐家房产红谷分行','phone'=>'15070864681'],
['name'=>'何龙华','com'=>'中环地产康城分行','phone'=>'15070863971'],
['name'=>'熊传文','com'=>'中环地产力天阳光分行','phone'=>'15070843049'],
['name'=>'袁子超','com'=>'中环地产东方银座分行','phone'=>'15070842806'],
['name'=>'黄慧宾','com'=>'我爱我家福山花园分行','phone'=>'15070834053'],
['name'=>'熊建春','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15070818108'],
['name'=>'李涛','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'15070815077'],
['name'=>'叶秀长','com'=>'中环地产工商学院分行','phone'=>'15070699700'],
['name'=>'邓权','com'=>'中环地产诚义路卓越分行','phone'=>'15070616732'],
['name'=>'唐亮','com'=>'鸿基房产滨河一品分行','phone'=>'15070567447'],
['name'=>'刘宾','com'=>'中环地产金沙大道分行','phone'=>'15070558799'],
['name'=>'熊辉','com'=>'鸿基房产象湖平安分部','phone'=>'15070548522'],
['name'=>'熊方林','com'=>'鸿基房产新力都荟店','phone'=>'15070415074'],
['name'=>'王莉纯','com'=>'鸿基房产新力都荟店','phone'=>'15070226525'],
['name'=>'张伟','com'=>'中环地产上罗分行','phone'=>'15070099002'],
['name'=>'付政文','com'=>'中环地产比华利分行','phone'=>'15070094096'],
['name'=>'程应平','com'=>'中环地产英伦联邦分行','phone'=>'15070089213'],
['name'=>'喻桂花','com'=>'鸿基房产兴国路店','phone'=>'15070087400'],
['name'=>'黄辉','com'=>'鸿基房产蛟桥店','phone'=>'15070086133'],
['name'=>'刘文斌','com'=>'中环地产伟梦清水湾分行','phone'=>'15070082017'],
['name'=>'吴信俊','com'=>'中环地产金沙大道分行','phone'=>'15070080426'],
['name'=>'黎秘高','com'=>'中环地产芳诚路分行','phone'=>'15070080030'],
['name'=>'王海','com'=>'中环地产京东小区分行','phone'=>'15070077698'],
['name'=>'万志超','com'=>'齐家房产万达分行','phone'=>'15070072541'],
['name'=>'余书奇','com'=>'中环地产工商学院分行','phone'=>'15070056774'],
['name'=>'熊世晨','com'=>'中环地产象湖中幸分行','phone'=>'15070034331'],
['name'=>'刘小群','com'=>'鸿基房产洛阳东路店','phone'=>'15070020489'],
['name'=>'樊孝东','com'=>'鸿基房产保集半岛分行','phone'=>'15070007423'],
['name'=>'万鸿亮','com'=>'中环地产彭家桥分行','phone'=>'15007915328'],
['name'=>'邓青青','com'=>'中环地产京安路分行','phone'=>'15007008952'],
['name'=>'陈隆','com'=>'中环地产幸福时光二分行','phone'=>'13989554913'],
['name'=>'范士宏','com'=>'鸿基房产世纪华联店','phone'=>'13979577334'],
['name'=>'陈乐','com'=>'鸿基房产府前路店','phone'=>'13979186716'],
['name'=>'杨志刚','com'=>'鸿基房产东方塞纳店','phone'=>'13979183579'],
['name'=>'彭花','com'=>'中环地产苏圃路店','phone'=>'13979180012'],
['name'=>'王玲','com'=>'鸿基房产大士院店','phone'=>'13979146660'],
['name'=>'应红红','com'=>'中环地产子固路分行','phone'=>'13979133312'],
['name'=>'徐月芳','com'=>'鸿基房产凤凰城分行','phone'=>'13979126757'],
['name'=>'罗俊','com'=>'鸿基房产青春家园店','phone'=>'13979110081'],
['name'=>'陈春香','com'=>'中环地产诚义路卓越分行','phone'=>'13979106027'],
['name'=>'曾一洺','com'=>'中环地产艾溪康桥分行','phone'=>'13970970228'],
['name'=>'张斌','com'=>'中环地产景城名郡二分行','phone'=>'13970962287'],
['name'=>'熊学宽','com'=>'中环地产坛子口分行','phone'=>'13970958033'],
['name'=>'田俊健','com'=>'云房通房产总部','phone'=>'13970950800'],
['name'=>'曾园兰','com'=>'鸿基房产城东百大店','phone'=>'13970932558'],
['name'=>'孙孔江','com'=>'鸿基房产十九中店','phone'=>'13970921711'],
['name'=>'胡伟','com'=>'鸿基房产澄湖东路店','phone'=>'13970915837'],
['name'=>'熊书霞','com'=>'鸿基房产象山北路店','phone'=>'13970910999'],
['name'=>'刘春勇','com'=>'鸿基文教路小学店','phone'=>'13970909984'],
['name'=>'胡艳丽','com'=>'桥郡房产红谷滩分行','phone'=>'13970906925'],
['name'=>'刘珍珍','com'=>'芳鑫房产红角州店','phone'=>'13970880670'],
['name'=>'张欣','com'=>'中环地产奥园三组分行','phone'=>'13970846186'],
['name'=>'陶表发','com'=>'鸿基房产紫金城店','phone'=>'13970838548'],
['name'=>'万仁照','com'=>'中环地产创新一路分行','phone'=>'13970836234'],
['name'=>'闵志群','com'=>'中环地产京山分行','phone'=>'13970812981'],
['name'=>'袁斌','com'=>'中环地产彭家桥分行','phone'=>'13970809428'],
['name'=>'李湘铎','com'=>'万科租售中心四季花城南区店','phone'=>'13970206414'],
['name'=>'罗冲','com'=>'中环地产上海北分行','phone'=>'13970086757'],
['name'=>'徐志强','com'=>'鸿基房产保集半岛分行','phone'=>'13970079385'],
['name'=>'邹俊','com'=>'我爱我家东莲路店','phone'=>'13970079003'],
['name'=>'万书祥','com'=>'中好房屋力高滨江国际店','phone'=>'13970077301'],
['name'=>'刘玉兰','com'=>'鸿基房产广场东路店','phone'=>'13970073009'],
['name'=>'张小敏','com'=>'我爱我家水悦湾一区店','phone'=>'13970063207'],
['name'=>'占飞','com'=>'中环地产彭家桥分行','phone'=>'13970053995'],
['name'=>'徐文娟','com'=>'鸿基房产迎宾大道分行','phone'=>'13970035202'],
['name'=>'胡文涛','com'=>'中晨地产阳光家园店','phone'=>'13970030309'],
['name'=>'李志伟','com'=>'中环地产绿地玫瑰城分行','phone'=>'13970016486'],
['name'=>'邓琼','com'=>'我爱我家北司独立经纪人部','phone'=>'13970010632'],
['name'=>'涂文忠','com'=>'鸿基房产正荣蓝汀岸分行','phone'=>'13907096149'],
['name'=>'徐浩','com'=>'鸿基房产朝阳正荣店','phone'=>'13907009477'],
['name'=>'杨文琴','com'=>'中环地产银三角恒大分行','phone'=>'13907007981'],
['name'=>'吴少辉','com'=>'中环地产白帝沙分行','phone'=>'13907002565'],
['name'=>'董美华','com'=>'中环地产金沙大道分行','phone'=>'13879294689'],
['name'=>'毛广剑','com'=>'中环地产怡兰苑分行','phone'=>'13879194684'],
['name'=>'万飞','com'=>'鸿基房产九里西街店','phone'=>'13879180761'],
['name'=>'魏涛','com'=>'中环地产工商学院分行','phone'=>'13879167741'],
['name'=>'胡旭芳','com'=>'中环地产京东二分行','phone'=>'13879156110'],
['name'=>'杨菊','com'=>'中环地产向阳路分行','phone'=>'13879148039'],
['name'=>'唐奇','com'=>'中环地产景城名郡二分行','phone'=>'13879123337'],
['name'=>'徐斌','com'=>'鸿基房产力高店','phone'=>'13879121603'],
['name'=>'周小青','com'=>'鸿基房产六中联发店','phone'=>'13879108885'],
['name'=>'刘春','com'=>'鸿基房产东城一品店','phone'=>'13870974124'],
['name'=>'夏建军','com'=>'中环地产青春家园分行','phone'=>'13870970506'],
['name'=>'闵玉梅','com'=>'中环地产松柏巷分行','phone'=>'13870956110'],
['name'=>'涂小玲','com'=>'我爱我家后墙路店','phone'=>'13870953356'],
['name'=>'姜海龙','com'=>'鸿基房产新力愉景湾店','phone'=>'13870943769'],
['name'=>'魏夕春','com'=>'鸿基房产庐山花园分行','phone'=>'13870937962'],
['name'=>'王雨萍','com'=>'中天房产四中店','phone'=>'13870937793'],
['name'=>'李文涛','com'=>'我爱我家青山湖大道分行','phone'=>'13870936179'],
['name'=>'丁小妹','com'=>'鸿基房产英伦联邦店','phone'=>'13870926790'],
['name'=>'黄同辉','com'=>'鸿基房产锦竹幸福里店','phone'=>'13870918593'],
['name'=>'樊欢','com'=>'鸿基房产新力都荟店','phone'=>'13870908601'],
['name'=>'万红梅','com'=>'我爱我家湖滨东路分店','phone'=>'13870905014'],
['name'=>'周清','com'=>'鸿基房产新上海路分行','phone'=>'13870896248'],
['name'=>'任魁','com'=>'我爱我家后墙路店','phone'=>'13870890565'],
['name'=>'丁加叶','com'=>'齐家房产悦城D区店','phone'=>'13870811372'],
['name'=>'熊少平','com'=>'我爱我家象湖新城学校店','phone'=>'13870810833'],
['name'=>'王磊','com'=>'鸿基房产城邦店','phone'=>'13870810613'],
['name'=>'王芬霞','com'=>'鸿基房产庐山花园分行','phone'=>'13870800763'],
['name'=>'胡丹丹','com'=>'鸿基房产恒辉花园分行','phone'=>'13870699934'],
['name'=>'李惟虎','com'=>'鸿达房产康城分行','phone'=>'13870685091'],
['name'=>'程伟','com'=>'中环地产紫金城分行','phone'=>'13870681338'],
['name'=>'谢兵','com'=>'中环地产万科四季花城分行','phone'=>'13870664060'],
['name'=>'万嵋洪','com'=>'鸿基房产广场东路店','phone'=>'13870645320'],
['name'=>'李勇','com'=>'中环地产工商学院分行','phone'=>'13870634672'],
['name'=>'王俊建','com'=>'中环地产力高君御国际','phone'=>'13870631373'],
['name'=>'周建卫','com'=>'鸿基房产新力都荟店','phone'=>'13870621344'],
['name'=>'王勇','com'=>'鸿基房产保集半岛分行','phone'=>'13870087645'],
['name'=>'陈朝兵','com'=>'鸿基房产昌南分行','phone'=>'13870086254'],
['name'=>'黎全华','com'=>'我爱我家幸福里旗舰店','phone'=>'13870075277'],
['name'=>'何伟','com'=>'鸿基房产华润橡府店','phone'=>'13870072763'],
['name'=>'胡海辉','com'=>'中环地产万达同乐分行','phone'=>'13870066173'],
['name'=>'喻振林','com'=>'中环地产力高君御国际','phone'=>'13870056560'],
['name'=>'龚志伟','com'=>'中环地产诚义路卓越分行','phone'=>'13870053053'],
['name'=>'刘震群','com'=>'中环地产洪都新村店','phone'=>'13807094362'],
['name'=>'杨琪','com'=>'中晨地产新建中心店','phone'=>'13807081165'],
['name'=>'何海莉','com'=>'中环地产象湖启航分行','phone'=>'13807059783'],
['name'=>'俞兰','com'=>'鸿基房产南京东路店','phone'=>'13807037647'],
['name'=>'陶子文','com'=>'中环地产诚义路卓越分行','phone'=>'13807033176'],
['name'=>'程进梅','com'=>'中环地产诚义路卓越分行','phone'=>'13807000619'],
['name'=>'吴秀楚','com'=>'中环地产保利金香槟分行','phone'=>'13806834252'],
['name'=>'刘玉林','com'=>'齐家房产凯旋店','phone'=>'13803522350'],
['name'=>'李嘉宾','com'=>'中环地产星诚幸福分行','phone'=>'13803521858'],
['name'=>'李波','com'=>'中环地产怡园名门分行','phone'=>'13803518620'],
['name'=>'李燕','com'=>'中环地产桃苑分行','phone'=>'13803511967'],
['name'=>'吴爱香','com'=>'中环地产北京东路分行','phone'=>'13803508560'],
['name'=>'熊美兰','com'=>'鸿基房产庐山花园分行','phone'=>'13803502862'],
['name'=>'何鹏','com'=>'中环地产南京东路店','phone'=>'13802689449'],
['name'=>'李红','com'=>'鸿基房产洛阳东路店','phone'=>'13767991876'],
['name'=>'李建洪','com'=>'鸿基房产新建五中店','phone'=>'13767989708'],
['name'=>'黄鹏','com'=>'中环地产岭口路分行','phone'=>'13767971468'],
['name'=>'范永成','com'=>'中环地产金沙大道分行','phone'=>'13767553058'],
['name'=>'舒明冲','com'=>'中环地产工商学院分行','phone'=>'13767483326'],
['name'=>'周公平','com'=>'鸿基房产正荣大湖之都店','phone'=>'13767478091'],
['name'=>'徐金琴','com'=>'鸿基房产子固路分部','phone'=>'13767476581'],
['name'=>'章鹏','com'=>'中环地产东岳大道分行','phone'=>'13767454665'],
['name'=>'肖乐','com'=>'鸿基房产象湖平安分部','phone'=>'13767437263'],
['name'=>'杨志朋','com'=>'鸿基房产朝阳新城分行','phone'=>'13767434423'],
['name'=>'王永锋','com'=>'中环地产保集一分行','phone'=>'13767424581'],
['name'=>'刘军','com'=>'鸿基房产金涛御景店','phone'=>'13767419197'],
['name'=>'万乐群','com'=>'中环地产保集御河湾店','phone'=>'13767410671'],
['name'=>'邓隆美','com'=>'中环地产南大分行','phone'=>'13767410052'],
['name'=>'魏海红','com'=>'鸿基房产红谷新城店','phone'=>'13767198966'],
['name'=>'张慧红','com'=>'我爱我家福山花园分行','phone'=>'13767185322'],
['name'=>'熊浩文','com'=>'中环地产恒大城分行','phone'=>'13767179007'],
['name'=>'曾兵兵','com'=>'中环地产昌南体育中心分行','phone'=>'13767178804'],
['name'=>'施芳','com'=>'中环地产力天阳光分行','phone'=>'13767175159'],
['name'=>'欧阳恬','com'=>'中环地产闽顺分行','phone'=>'13767170471'],
['name'=>'魏春艳','com'=>'中环地产比华利分行','phone'=>'13767138070'],
['name'=>'万强强','com'=>'鸿基房产迎宾大道分行','phone'=>'13767137897'],
['name'=>'熊丽华','com'=>'鸿基房产红谷滩店','phone'=>'13767137827'],
['name'=>'龚良鹏','com'=>'鸿基房产迎宾大道分行','phone'=>'13767128251'],
['name'=>'熊国武','com'=>'中环地产京东一分行','phone'=>'13767118621'],
['name'=>'吴波','com'=>'中环地产万科北区分行','phone'=>'13767112218'],
['name'=>'刘琼','com'=>'鸿基房产景江豪城店','phone'=>'13767109564'],
['name'=>'黄力','com'=>'齐家房产红谷分行','phone'=>'13767103733'],
['name'=>'刘文俊','com'=>'中环地产香溢花城分行','phone'=>'13767100754'],
['name'=>'刘文龙','com'=>'中环地产艾溪康桥分行','phone'=>'13767099403'],
['name'=>'杨波','com'=>'我爱我家建德观店','phone'=>'13767080558'],
['name'=>'樊丽','com'=>'鸿基房产东城一品店','phone'=>'13767077743'],
['name'=>'夏雪梅','com'=>'中环地产桥郡分行','phone'=>'13767059605'],
['name'=>'高菊花','com'=>'鸿基房产宜居店','phone'=>'13767058565'],
['name'=>'张义祥','com'=>'鸿基房产象湖平安分部','phone'=>'13767053957'],
['name'=>'王志坚','com'=>'鸿基房产八大山人店','phone'=>'13767053376'],
['name'=>'殷春香','com'=>'我爱我家嘉业花园二店分行','phone'=>'13767053158'],
['name'=>'潘潇丽','com'=>'乐有家洪城比华利店','phone'=>'13767038898'],
['name'=>'张锋波','com'=>'中环地产金涛御景分行','phone'=>'13767034389'],
['name'=>'涂雄飞','com'=>'齐家房产万达茂A区店','phone'=>'13767031086'],
['name'=>'李浩天','com'=>'齐家房产莱蒙分行','phone'=>'13767026716'],
['name'=>'魏凌军','com'=>'中环地产新魏路分行','phone'=>'13767017325'],
['name'=>'黄强强','com'=>'中环地产新力都荟分行','phone'=>'13767016029'],
['name'=>'林旺森','com'=>'齐家房产悦城D区店','phone'=>'13766353843'],
['name'=>'邓毓杰','com'=>'中环地产工商学院分行','phone'=>'13755799049'],
['name'=>'金雪梅','com'=>'中晨地产红谷新城二部','phone'=>'13755791770'],
['name'=>'胡勤','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13755789239'],
['name'=>'张雪珍','com'=>'南昌欣盛房产红谷滩分行','phone'=>'13755788653'],
['name'=>'杜彩凤','com'=>'中环地产新力分行','phone'=>'13755786170'],
['name'=>'刘九龙','com'=>'中环地产南大分行','phone'=>'13755785575'],
['name'=>'陈良','com'=>'中环地产星诚幸福分行','phone'=>'13755780149'],
['name'=>'熊信扬','com'=>'鸿基房产红谷凯旋分行','phone'=>'13755767259'],
['name'=>'郭琼','com'=>'我爱我家幸福里旗舰店','phone'=>'13755761716'],
['name'=>'胡爱勇','com'=>'我爱我家府前西路分行','phone'=>'13755759866'],
['name'=>'吴问信','com'=>'鸿基房产恒茂东方店','phone'=>'13755756791'],
['name'=>'曹永安','com'=>'中环地产澄湖华润分行','phone'=>'13755698976'],
['name'=>'刘凤','com'=>'中环地产藤旺居分行','phone'=>'13755686583'],
['name'=>'魏军','com'=>'中环地产滨江明珠分行','phone'=>'13755683868'],
['name'=>'熊守高','com'=>'鸿基房产力高店','phone'=>'13755661252'],
['name'=>'涂序伟','com'=>'我爱我家美罗嘉苑店','phone'=>'13755653042'],
['name'=>'樊启文','com'=>'鸿基房产松柏巷分行','phone'=>'13755651655'],
['name'=>'胡德新','com'=>'我爱我家恒大绿洲分行','phone'=>'13755650382'],
['name'=>'刘文亮','com'=>'中环地产星港小镇分行','phone'=>'13755648003'],
['name'=>'周启刚','com'=>'我爱我家丁香怡景店','phone'=>'13755601967'],
['name'=>'陈永强','com'=>'中环地产星港小镇分行','phone'=>'13751054858'],
['name'=>'胡玲玲','com'=>'鸿基房产丰源淳和店','phone'=>'13732973580'],
['name'=>'罗广','com'=>'中环地产工商学院分行','phone'=>'13732967457'],
['name'=>'胡方建','com'=>'中环地产枫林美庐分行','phone'=>'13732956435'],
['name'=>'吴永华','com'=>'鸿基房产银亿上尚城店','phone'=>'13732951590'],
['name'=>'程佳斌','com'=>'鸿基房产新力都荟店','phone'=>'13732950036'],
['name'=>'徐昭明','com'=>'中环地产枫林大道分行','phone'=>'13732922836'],
['name'=>'刘雷','com'=>'中环地产国安路店','phone'=>'13732920827'],
['name'=>'钟清红','com'=>'中环地产桃苑分行','phone'=>'13732914240'],
['name'=>'袁卿','com'=>'我爱我家丰源淳和店','phone'=>'13711567217'],
['name'=>'郑志强','com'=>'齐家房产奥克斯店','phone'=>'13707972311'],
['name'=>'龚平平','com'=>'鸿基房产友邦皇家公馆店','phone'=>'13707096728'],
['name'=>'龚冬冬','com'=>'中环地产保利金香槟分行','phone'=>'13707085951'],
['name'=>'刘晓艳','com'=>'鸿基房产广场东路店','phone'=>'13699563653'],
['name'=>'熊文远','com'=>'鸿基房产迎宾大道分行','phone'=>'13699552498'],
['name'=>'徐勇辉','com'=>'鸿基房产中央首府店','phone'=>'13699550300'],
['name'=>'谢晓祥','com'=>'我爱我家北司独立经纪人部','phone'=>'13699544630'],
['name'=>'熊敏','com'=>'中环地产南京东路店','phone'=>'13699535561'],
['name'=>'丁树水','com'=>'鸿基房产万达城店','phone'=>'13699530226'],
['name'=>'蒋贤德','com'=>'鸿基房产铜锣湾店','phone'=>'13699516882'],
['name'=>'周栋辉','com'=>'中环地产彭家桥分行','phone'=>'13699513060'],
['name'=>'胡清华','com'=>'鸿基房产伟梦清水湾店','phone'=>'13699505114'],
['name'=>'徐水华','com'=>'中环地产金沙大道分行','phone'=>'13699504479'],
['name'=>'王花平','com'=>'齐家房产世纪店','phone'=>'13699502832'],
['name'=>'徐涛','com'=>'中环地产新力都荟分行','phone'=>'13699502399'],
['name'=>'魏霆锋','com'=>'中环地产工商学院分行','phone'=>'13698090698'],
['name'=>'余冬根','com'=>'齐家房产红谷分行','phone'=>'13697080523'],
['name'=>'刘峰','com'=>'中环地产店中央首府分行','phone'=>'13697053304'],
['name'=>'陈琦','com'=>'齐家房产悦城D区店','phone'=>'13697005303'],
['name'=>'扶霞','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13694882059'],
['name'=>'胡慧兰','com'=>'鸿基房产樟树林店','phone'=>'13687919601'],
['name'=>'揭林花','com'=>'鸿基房产鑫发店','phone'=>'13687915746'],
['name'=>'梁莹莹','com'=>'齐家房产悦城D区店','phone'=>'13687914251'],
['name'=>'管少武','com'=>'中环地产彭家桥分行','phone'=>'13687905800'],
['name'=>'万文亮','com'=>'中环地产平安东门分行','phone'=>'13687085735'],
['name'=>'程建兵','com'=>'中环地产金沙逸都分行','phone'=>'13687082680'],
['name'=>'施建军','com'=>'中环地产金沙大道分行','phone'=>'13687007100'],
['name'=>'熊万萍','com'=>'中环地产京东二分行','phone'=>'13677096277'],
['name'=>'黄少婷','com'=>'鸿基房产国博城店','phone'=>'13677088745'],
['name'=>'曹泮炉','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13677084269'],
['name'=>'欧阳兴','com'=>'中环地产平安东门分行','phone'=>'13677080345'],
['name'=>'胡张张','com'=>'中环地产正荣润城分行','phone'=>'13677007554'],
['name'=>'罗天','com'=>'鸿基房产九里西街店','phone'=>'13677005500'],
['name'=>'唐拓','com'=>'中环地产幸福五期商业街分行','phone'=>'13672284636'],
['name'=>'廖尧','com'=>'齐家房产博泰分行','phone'=>'13672229986'],
['name'=>'熊罕钰','com'=>'中环地产连发分行','phone'=>'13672227402'],
['name'=>'姜涛','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13672227042'],
['name'=>'熊自标','com'=>'中环地产奥林匹克分行','phone'=>'13672223035'],
['name'=>'熊招琴','com'=>'鸿基房产建设路分店','phone'=>'13672220903'],
['name'=>'吕永泉','com'=>'齐家房产红谷分行','phone'=>'13672217170'],
['name'=>'陈雯雯','com'=>'鸿基房产保集半岛分行','phone'=>'13672213599'],
['name'=>'黄峰峰','com'=>'鸿基房产中央首府店','phone'=>'13672212350'],
['name'=>'黄长彪','com'=>'鸿基房产昌南分行','phone'=>'13672205092'],
['name'=>'万卫华','com'=>'鸿基房产南师附小店','phone'=>'13672200094'],
['name'=>'刘正稳','com'=>'中环地产天沐君湖分行','phone'=>'13667085829'],
['name'=>'熊中平','com'=>'中环地产双港东分行','phone'=>'13667081278'],
['name'=>'周洋','com'=>'鸿基房产东莲路店','phone'=>'13667080776'],
['name'=>'丁光平','com'=>'鸿基房产新力帝泊湾店','phone'=>'13667006135'],
['name'=>'方杨','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13667002459'],
['name'=>'冷文豪','com'=>'我爱我家莱蒙都会店','phone'=>'13657919648'],
['name'=>'何宜雨','com'=>'中环地产岭口路分行','phone'=>'13657093300'],
['name'=>'余际利','com'=>'中环地产象湖华联分行','phone'=>'13657089882'],
['name'=>'喻玲燕','com'=>'中环地产工商学院分行','phone'=>'13657085369'],
['name'=>'万冬琴','com'=>'中环地产金涛御景分行','phone'=>'13657007872'],
['name'=>'徐方玉','com'=>'中环地产工商学院分行','phone'=>'13647917501'],
['name'=>'范小进','com'=>'中环地产金沙大道分行','phone'=>'13647916995'],
['name'=>'江旭','com'=>'中环地产水岸菁华分行','phone'=>'13647099658'],
['name'=>'上官志垚','com'=>'中环地产京东国际花园分行','phone'=>'13647094283'],
['name'=>'马钰旋','com'=>'中环地产北艾分行','phone'=>'13647091701'],
['name'=>'周文','com'=>'鸿基房产九里西街店','phone'=>'13647085182'],
['name'=>'邱建能','com'=>'中环地产工商学院分行','phone'=>'13642346686'],
['name'=>'万松','com'=>'中环地产金沙大道分行','phone'=>'13636975951'],
['name'=>'谭镇洲','com'=>'鸿基房产象湖平安分部','phone'=>'13636972039'],
['name'=>'万炎','com'=>'来淘房产红谷滩店','phone'=>'13627094621'],
['name'=>'周勇','com'=>'鸿基房产保集半岛分行','phone'=>'13627007851'],
['name'=>'李志远','com'=>'齐家房产世纪花园店','phone'=>'13627003460'],
['name'=>'龚正涛','com'=>'鸿基房产力高店','phone'=>'13627003298'],
['name'=>'程红','com'=>'鸿基房产广场东路店','phone'=>'13617918340'],
['name'=>'胡辉','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13617915134'],
['name'=>'宁鹏飞','com'=>'中环地产京东二分行','phone'=>'13617096285'],
['name'=>'李以标','com'=>'中环地产芳湖苑分行','phone'=>'13617089261'],
['name'=>'殷鑫方','com'=>'中环地产幸福时光三分行','phone'=>'13617004122'],
['name'=>'张龙','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13617001295'],
['name'=>'张慧','com'=>'鸿基房产绿地香颂店','phone'=>'13607919076'],
['name'=>'叶亮','com'=>'中环地产工商学院分行','phone'=>'13607099141'],
['name'=>'雷明','com'=>'我爱我家幸福时光二店','phone'=>'13607066045'],
['name'=>'邓蓓','com'=>'中天房产四中店','phone'=>'13607057412'],
['name'=>'李平','com'=>'鸿基房产新洪城大市场店','phone'=>'13586553108'],
['name'=>'杨娟娟','com'=>'中环地产南昌八中分行','phone'=>'13576999877'],
['name'=>'帅小玲','com'=>'中环地产广场南分行','phone'=>'13576999811'],
['name'=>'甘春艳','com'=>'鸿基房产象湖平安总店','phone'=>'13576985794'],
['name'=>'张宇','com'=>'中环地产紫荆路分行','phone'=>'13576969645'],
['name'=>'罗会强','com'=>'中环地产双港东分行','phone'=>'13576964933'],
['name'=>'王志宇','com'=>'鸿基房产九里西街店','phone'=>'13576964385'],
['name'=>'王子葵','com'=>'中环地产保集一分行','phone'=>'13576958418'],
['name'=>'胡海涛','com'=>'云房通房产总部','phone'=>'13576956937'],
['name'=>'梁昌芬','com'=>'中环地产正荣天镜分行','phone'=>'13576949296'],
['name'=>'朱伟','com'=>'中环地产万达东分行','phone'=>'13576934371'],
['name'=>'胡正屹','com'=>'中环地产金沙大道分行','phone'=>'13576917885'],
['name'=>'江海燕','com'=>'中环地产紫金城分行','phone'=>'13576913114'],
['name'=>'胡旗','com'=>'鸿基房产锦竹幸福里店','phone'=>'13576298776'],
['name'=>'彭结华','com'=>'中环地产谢家村分行','phone'=>'13576293745'],
['name'=>'王援兴','com'=>'中环地产金沙大道分行','phone'=>'13576273182'],
['name'=>'史鹏中','com'=>'中环地产南昌八中分行','phone'=>'13576263437'],
['name'=>'刘杰','com'=>'中环地产金沙大道分行','phone'=>'13576262686'],
['name'=>'林莉','com'=>'鸿基房产五纬路店','phone'=>'13576144520'],
['name'=>'文淑华','com'=>'易居地产华晨房产店','phone'=>'13576132618'],
['name'=>'雷开辉','com'=>'我爱我家幸福时光二店','phone'=>'13576127667'],
['name'=>'程其花','com'=>'鸿基房产天赐良园分行','phone'=>'13576126380'],
['name'=>'蔡道贵','com'=>'睿隆房产总部','phone'=>'13576117779'],
['name'=>'黄刚','com'=>'中环地产广场南分行','phone'=>'13576092625'],
['name'=>'李宜娜','com'=>'鸿基房产广州路店','phone'=>'13576077031'],
['name'=>'宋康仁','com'=>'我爱我家后墙路店','phone'=>'13576071446'],
['name'=>'舒伟荣','com'=>'我爱我家后墙路店','phone'=>'13576071440'],
['name'=>'朱圣明','com'=>'天越房产总部','phone'=>'13576062054'],
['name'=>'朱志鹏','com'=>'中环地产丰和大道分行','phone'=>'13576041140'],
['name'=>'罗佳','com'=>'宸和地产总部','phone'=>'13576029495'],
['name'=>'汪林香','com'=>'芳鑫房产红角州店','phone'=>'13576025701'],
['name'=>'袁斌','com'=>'鸿基房产绿地香颂店','phone'=>'13576013451'],
['name'=>'张林','com'=>'云房通房产总部','phone'=>'13576002447'],
['name'=>'史璐霞','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13517089021'],
['name'=>'胡小闽','com'=>'鸿基房产正荣南岸公园分行','phone'=>'13517085805'],
['name'=>'涂文鹏','com'=>'鸿基房产幸福时光店','phone'=>'13517083504'],
['name'=>'宋涛','com'=>'中环地产海关桥分行','phone'=>'13517083376'],
['name'=>'肖爱勤','com'=>'鸿基房产京东小区店','phone'=>'13517001391'],
['name'=>'熊其松','com'=>'中环地产紫金城分行','phone'=>'13507097901'],
['name'=>'聂水文','com'=>'鸿基房产愉景湾北门店','phone'=>'13507097661'],
['name'=>'邬文娟','com'=>'中环地产岭口路分行','phone'=>'13507089826'],
['name'=>'戴悦','com'=>'中环地产嘉业花园分行','phone'=>'13507086280'],
['name'=>'金焕亮','com'=>'中环地产天一城分行','phone'=>'13507058741'],
['name'=>'李涛','com'=>'鸿基房产青山湖北大道店','phone'=>'13507009000'],
['name'=>'万长青','com'=>'中环地产创新南师分行','phone'=>'13507003738'],
['name'=>'程芳','com'=>'中环地产拉菲公馆分行','phone'=>'13507003573'],
['name'=>'陈祥鹏','com'=>'我爱我家丰和新城分行','phone'=>'13479567728'],
['name'=>'徐冬花','com'=>'芳鑫房产红角州店','phone'=>'13479122817'],
['name'=>'董浩','com'=>'鸿基房产丰和大道店','phone'=>'13479115151'],
['name'=>'丁敏强','com'=>'齐家房产鹿璟分行','phone'=>'13437080864'],
['name'=>'闻彬','com'=>'鸿基房产前进路店','phone'=>'13407957088'],
['name'=>'李丽','com'=>'中环地产庐山南大道分行','phone'=>'13397911774'],
['name'=>'刘伟强','com'=>'中环地产金沙大道分行','phone'=>'13397081969'],
['name'=>'胡晓菊','com'=>'中环地产奥园二分行','phone'=>'13397005960'],
['name'=>'李三妹','com'=>'中环地产昌南月星分行','phone'=>'13397001886'],
['name'=>'万志鹏','com'=>'齐家房产悦城D区店','phone'=>'13387097300'],
['name'=>'李菊平','com'=>'中环地产莲西大道分行','phone'=>'13387089660'],
['name'=>'李丹','com'=>'中环地产紫金城分行','phone'=>'13387087668'],
['name'=>'付琦','com'=>'中环地产居住主题分行','phone'=>'13367099466'],
['name'=>'陈志强','com'=>'鸿基房产世纪华联店','phone'=>'13367098100'],
['name'=>'吴涛','com'=>'鸿基房产世纪华联店','phone'=>'13367001771'],
['name'=>'徐海天','com'=>'乐有家洪城比华利店','phone'=>'13361645491'],
['name'=>'黄海超','com'=>'中环地产怡兰苑分行','phone'=>'13361626242'],
['name'=>'陈兆文','com'=>'中环地产振兴大道分行','phone'=>'13330086517'],
['name'=>'蔡力乔','com'=>'鸿基房产西站大街店','phone'=>'13330078006'],
['name'=>'甘德高','com'=>'中环地产红湾大道分行','phone'=>'13320013023'],
['name'=>'徐文韬','com'=>'中环地产连发分行','phone'=>'13319440888'],
['name'=>'郭频','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13319413840'],
['name'=>'万志强','com'=>'齐家房产世纪店','phone'=>'13317057035'],
['name'=>'程文红','com'=>'鸿基房产国贸阳光分行','phone'=>'13317054688'],
['name'=>'胡娇','com'=>'中环地产绿湖豪城分行','phone'=>'13263916363'],
['name'=>'周钟华','com'=>'我爱我家丁香怡景店','phone'=>'13263005120'],
['name'=>'黄朕','com'=>'我爱我家丰和新城分行','phone'=>'13257095621'],
['name'=>'魏浩成','com'=>'鸿基房产莱蒙中心店','phone'=>'13257089581'],
['name'=>'万远腾','com'=>'中环地产青春家园分行','phone'=>'13257009873'],
['name'=>'杨涛','com'=>'鸿基房产西站大街店','phone'=>'13257006707'],
['name'=>'朱武越','com'=>'中环地产正荣澄湖分行','phone'=>'13257000100'],
['name'=>'万绍海','com'=>'鸿基房产滨河一品分行','phone'=>'13247818578'],
['name'=>'刘林妹','com'=>'中环地产董家窑分行','phone'=>'13247816722'],
['name'=>'李春','com'=>'中环地产芳华路分行','phone'=>'13247769636'],
['name'=>'张鹏','com'=>'鸿基房产正荣御尊店','phone'=>'13247711634'],
['name'=>'张令飞','com'=>'中环地产丰源淳和分行','phone'=>'13247710523'],
['name'=>'晏田川','com'=>'中环地产平安象湖分行','phone'=>'13247080998'],
['name'=>'罗娟','com'=>'中环地产绿地新都会分行','phone'=>'13247003303'],
['name'=>'樊杨帆','com'=>'中环地产诚义路卓越分行','phone'=>'13237515885'],
['name'=>'林凯','com'=>'齐家房产华府分行','phone'=>'13237006731'],
['name'=>'甘礼煌','com'=>'中环地产红湾大道分行','phone'=>'13207912152'],
['name'=>'杨园园','com'=>'鸿基房产天赐良园分行','phone'=>'13207099083'],
['name'=>'李凡','com'=>'中环地产诚义路卓越分行','phone'=>'13207098581'],
['name'=>'邓丹','com'=>'中环地产湖景分行','phone'=>'13207092222'],
['name'=>'喻志勇','com'=>'中环地产诚义路卓越分行','phone'=>'13207008986'],
['name'=>'易伟','com'=>'中环地产紫金城分行','phone'=>'13184662813'],
['name'=>'李子健','com'=>'鸿基房产凤凰城分行','phone'=>'13184589257'],
['name'=>'陈小健','com'=>'鸿基房产前湖大道分行','phone'=>'13184588856'],
['name'=>'赵艳萍','com'=>'鸿基房产高新店','phone'=>'13184579938'],
['name'=>'张卫菊','com'=>'我爱我家岭口路店','phone'=>'13184565178'],
['name'=>'吴丽娟','com'=>'中环地产皇冠支路分行','phone'=>'13177884518'],
['name'=>'熊能','com'=>'鸿基房产步步高分行','phone'=>'13177883561'],
['name'=>'王智','com'=>'我爱我家景城名郡分行','phone'=>'13177880623'],
['name'=>'闫永鹏','com'=>'中环地产青山湖大道分行','phone'=>'13177867701'],
['name'=>'邓建坡','com'=>'中环地产紫荆路分行','phone'=>'13177801848'],
['name'=>'冉帅','com'=>'鸿基房产保集半岛分行','phone'=>'13177781690'],
['name'=>'黄燕霞','com'=>'中环地产金嘉名筑分行','phone'=>'13177780219'],
['name'=>'胡胜辉','com'=>'中环地产芳诚路分行','phone'=>'13177777457'],
['name'=>'万涛','com'=>'中环地产福山花园分行','phone'=>'13177774909'],
['name'=>'董瑞波','com'=>'鸿基房产金沙大道店','phone'=>'13177773556'],
['name'=>'刘国辉','com'=>'中环地产丰和大道分行','phone'=>'13170913071'],
['name'=>'卢志翔','com'=>'中环地产九仰梧桐分行','phone'=>'13170868300'],
['name'=>'朱杰锦','com'=>'鸿基房产东方海德堡店','phone'=>'13170861186'],
['name'=>'龚良城','com'=>'中环地产九仰梧桐分行','phone'=>'13164699137'],
['name'=>'谢明','com'=>'鸿基房产桥郡旗舰店','phone'=>'13155836866'],
['name'=>'陶超超','com'=>'中环地产芳诚路分行','phone'=>'13155830173'],
['name'=>'郭玉辉','com'=>'中环地产下罗分行','phone'=>'13155802990'],
['name'=>'马海鸿','com'=>'鸿基房产汇仁阳光店','phone'=>'13133830196'],
['name'=>'熊涛涛','com'=>'中环地产恒大城分行','phone'=>'13133815373'],
['name'=>'李可','com'=>'鸿基房产都市未来店','phone'=>'13133813072'],
['name'=>'邹小欢','com'=>'中环地产红湾大道分行','phone'=>'13117916778'],
['name'=>'丁志祎','com'=>'中环地产幸福诚义分行','phone'=>'13117811062'],
['name'=>'许大银','com'=>'中环地产桥郡分行','phone'=>'13077990602'],
['name'=>'文衍伟','com'=>'我爱我家伟梦清水湾分部','phone'=>'13077986959'],
['name'=>'李志平','com'=>'中环地产金沙二路分行','phone'=>'13077965058'],
['name'=>'王炯','com'=>'我爱我家金沙二路店','phone'=>'13077953959'],
['name'=>'杨洋','com'=>'鸿基房产罗家塘店','phone'=>'13077952825'],
['name'=>'黄玉富','com'=>'我爱我家象湖新城分行','phone'=>'13077950571'],
['name'=>'万亮华','com'=>'鸿基房产凤凰假日广场店','phone'=>'13077933212'],
['name'=>'黄艳秋','com'=>'中环地产金沙大道分行','phone'=>'13065183587'],
['name'=>'李强','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13065173815'],
['name'=>'胡志坚','com'=>'我爱我家象湖新城分行','phone'=>'13065125199'],
['name'=>'卢泽义','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13065118516'],
['name'=>'宋福林','com'=>'慧达地产总部','phone'=>'13065110120'],
['name'=>'甘兴民','com'=>'中环地产阳光家园店','phone'=>'13064112492'],
['name'=>'林斌','com'=>'红谷滩满堂红租售中心世纪中央城店','phone'=>'13036210120'],
['name'=>'陶永兵','com'=>'中环地产蓝天碧水分行','phone'=>'13027229003'],
['name'=>'黄志建','com'=>'中环地产金沙大道分行','phone'=>'13027215318'],
['name'=>'熊炜','com'=>'中环地产比华利分行','phone'=>'13027215008'],
['name'=>'丁光晴','com'=>'鸿基房产新力帝泊湾店','phone'=>'13027202781'],
['name'=>'卢为民','com'=>'中环地产保集御河湾店','phone'=>'13026200082'],
['name'=>'杨宽','com'=>'中环地产芳诚路分行','phone'=>'13007233738'],
['name'=>'陶侕春','com'=>'中环地产恒大珺庭分行','phone'=>'13007229906'],
['name'=>'龙俊','com'=>'中环地产平安东门分行','phone'=>'13007226956'],
['name'=>'杨伟明','com'=>'中环地产昌南体育中心分行','phone'=>'13007207055'],
['name'=>'闵李东','com'=>'中环地产居住主题分行','phone'=>'13006218117'],
['name'=>'易楠','com'=>'中环地产象湖幸福里分行','phone'=>'13006204704'],];





        foreach ($arr as $key => $value) {
            $com = $value['com'];
            if(!($comobj = CompanyExt::model()->find("name='$com'"))) {
                $comobj = new CompanyExt;
                $comobj->name = $com;
                $comobj->type = 2;
                $comobj->status = 1;
                $comobj->area = 443;
                $comobj->street = 444;
                $comobj->save();
            }
            $phone = $value['phone'];
            if(UserExt::model()->find("phone='$phone'")) {
                continue;
            }
            $user = new UserExt;
            $user->cid = $comobj->id;
            $user->name = $value['name'];
            $user->phone = $phone;
            $user->type = 2;
            $user->status = 1;
            $user->save();
        }
        echo "finished";
    }
}