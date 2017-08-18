<?php
class DiyRules extends CComponent
{
    /**
     * 新路由规则对应route关系
     * 后台配置项=>route
     * @var array
     */
    static $map = [
        'plotIndex' => 'home/plot/index',//旧平台楼盘页规则
        'plotDetail' => 'home/plot/detail',//旧平台楼盘详情页规则
        'plotEvaluate' => 'home/plot/evaluate',//旧平台楼盘评测页规则
        'plotHouseType' => 'home/plot/huxing',//旧平台楼盘户型页规则
        'plotAlbum' => 'home/plot/album',//旧平台楼盘相册页规则
        'plotAround' => 'home/plot/around',//旧平台楼盘周边页规则
        'plotPrice' => 'home/plot/price',//旧平台楼盘价格趋势规则
        'plotNews' => 'home/plot/news',//旧平台楼盘资讯页规则
        'plotAsk' => 'home/plot/faq',//旧平台楼盘问答页规则
        'plotComment' => 'home/plot/comment',//旧平台点评页规则
        'articleDetail' => 'home/news/detail',//旧平台资讯页规则
    ];
    public static function generateNewRules()
    {
        $newRules = [];
        // foreach(self::$map as $name=>$route) {
        //     if(SM::routeDiyConfig()->hasAttribute($name) && SM::routeDiyConfig()->{$name}()) {
        //         $newRules[str_replace(['{id}','{pinyin}'],['<old_id:\d+>','<pinyin:\w+>'],SM::routeDiyConfig()->{$name}())] = $route;
        //     }
        // }
        $componentsConfigs = Yii::app()->getComponents(false);
        $newConfig = CMap::mergeArray(['rules'=>$newRules], $componentsConfigs['urlManager']);
        Yii::app()->setComponent('urlManager', $newConfig, false);
    }
}
