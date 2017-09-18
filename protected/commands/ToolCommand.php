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
                $plot->views+=$value;
                $plot->save();
                Yii::app()->redis->getClient()->hSet('plot_views',$key,0);
            }
        }
        echo "finished";
    }
}