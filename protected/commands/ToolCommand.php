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

    public function actionSendNo()
    {
        $infos = PlotMarketUserExt::model()->findAll('expire>'.time().' and expire<'.time()+86400*3);
        foreach ($infos as $key => $value) {
            $user = $value->user;
            if($user&&$user->qf_uid) {
                if($p = $value->plot) {
                    SmsExt::sendMsg('到期通知',$user->phone,['pro'=>$p->title]);
                    Yii::app()->controller->sendNotice('您的项目'.$p->title.'即将到期，请点击下面链接成为会员，成为会员后您的号码将继续展现，并且可以无限次数发布项目。 http://house.jj58.com.cn/api/index/vip',$user->qf_uid);
                }
            }
        }
    }
}