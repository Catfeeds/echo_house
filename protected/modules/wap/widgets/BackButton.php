<?php
/**
 * 返回按钮小物件
 * 适用：v2版房产
 * @author weibaqiu
 * @version 2016-06-08
 */
class BackButton extends CWidget
{
    public $backUrl;

    public $isIcon=false; //判断是否要用侧边按钮

    private $moduleId;

    private $controllerId;

    private $actionId;

    private $route;

    private $get;

    /**
     * 默认层级返回url
     * 返回url与页面深度的层次有一定关系，页面与页面之间存在父子关系或者同级关系
     * 父子关系中，子页面返回默认链接是其父级页面
     * 同级关系中，页面可以互相返回，但要防止死循环（也就是两个页面返回来返回去总是这两个页面）
     * 该方法中存放着层级关系，当没有指定需要返回的页面时（一般具体楼盘页面的情况下，需要人工指定具体返
     * 回页面，公共页面情况下则可根据默认值返回），会根据上面的规则调取返回链接
     * -------------------------------------------------------------------
     * 以下数组规则为：
     * key值为要返回的地址的route，值为数组，数组中有键值为'route'、'controller'、'action'的元素
     * 该类函数{@link generateUrl()}会根据相应的元素的key及value生成对应的返回地址
     * 具体请见{@link generateUrl()}
     * @return [type] [description]
     */
    public function defaultBackUrl()
    {
        return array(
            //楼盘页面
            'wap/plot/list' => array(
                'controller' => array('plot'),
            ),
        );
    }

    /**
     * 解析route
     */
    public function parseRoute()
    {
        $controller = $this->getOwner();
        $this->moduleId = $controller->getModule()->getId();
        $this->controllerId = $controller->getId();
        $this->actionId = $controller->getAction()->getId();
        $this->route = $controller->getRoute();
        $this->get = $_GET;
    }

    /**
     * 生成返回链接
     */
    public function generateUrl()
    {
        //若小物件内直接设置属性得到的url地址
        if($this->backUrl!==null) return $this->backUrl;

        //获取上一页地址
        if(Yii::app()->request->getUrlReferrer()!==null) {
            return $this->backUrl = 'javascript:history.back();';
        }

        //{$this->defaultBackUrl()}中'route'、'controller'是或的关系，而不是与的关系
        //只要出现了，就会使用其key作为返回地址，这与权限控制的accessRules是不同的
        foreach($this->defaultBackUrl() as $backRoute=>$backItem) {
            foreach($backItem as $type=>$item) {
                if($type=='controller' && in_array($this->controllerId,$item)
                || $type=='route' && in_array($this->route, $item)) {
                    return $this->backUrl = $this->owner->createUrl('/'.trim($backRoute,'/'));
                }
            }
        }

        return $this->backUrl = $this->getDefaultBackUrl();
    }

    /**
     * 生成默认链接
     * @return [type] [description]
     */
    public function getDefaultBackUrl()
    {
        return $this->owner->createUrl('/wap/index/index');
    }

    /**
     * init()
     */
    public function init()
    {
        $this->parseRoute();
        $this->generateUrl();
    }

    /**
     * run()
     */
    public function run()
    {
        $this->render('backButton',array('isIcon'=>$this->isIcon));
    }
}
