<?php
/**
 * 楼盘列表页url构造器
 */
class UrlConstructor extends CComponent
{
    /**
     * 生成的链接的route
     * @var string
     */
    public $route;

    /**
     * 构造函数
     * @param string $route [description]
     */
    public function __construct($route='')
    {
        $this->route = $route;
        if(empty($this->route)) {
            $this->route = 'home/plot/list';
            if(Yii::app()->controller->module->id=='wap'){
                $this->route = 'wap/plot/list';
            }
        }
        $this->init();
    }

    /**
     * 正构造和反解析模板
     * 数组中的key为参数标识，value是个数组，其中第一个元素为正构造的标记，第二个元素为反解析的正则模式
     * @var array
     */
    private $_extTemplate = array(
        'jzlb' => array('j','/j(\d+)/u'),
        'ditie' => array('d','/d(\d+)/u'),
        'xszt' => array('s','/s(\d+)/u'),
        'wylx' => array('w','/w(\d+)/u'),
        'zxzt' => array('z','/z(\d+)/u'),
        'kpsj' => array('k','/k([a-z]+)/u'),
        'xmts' => array('t','/t(\d+)/u'),
        'tuan' => array('o','/o(\d+)/u'),
        'order' => array('u','/u(\d+)/u'),
        'price' => array('p','/p(\d+)/u'),
        'xuexiao' => array('x','/x(\d+)/u'),
        'huxing' => array('h','/h(\d+)/u'),
        'xxlx' => array('xl','/xl(\d+)/u'),//楼盘所关联学校的类型，中学or小学
        'mianji' => array('m','/m(\d+)/u'),
    );
    /**
     * 当前选择的区域id
     * @var integer
     */
    public $place = 0;
    /**
     * 当前选择的ext参数
     * @var array
     */
    public $extMap = array();
    /**
     * 要清楚的已选项
     * @var array
     */
    public $clearItems = array();

    /**
     * 魔术方法__get
     * 获取当前页面参数时可以直接获取
     */
    public function __get($name)
    {
        if(isset($this->extMap[$name])){
            return $this->extMap[$name];
        }elseif(isset($this->_extTemplate[$name])){
            return null;
        }else{
            return parent::__set($name);
        }
    }

    /**
     * 初始化操作，反解析当前访问参数到map属性
     */
    public function init()
    {
        $ext = Yii::app()->request->getQuery('ext');
        foreach($this->_extTemplate as $k=>$v){
            if(preg_match($v[1], $ext, $matches)){
                $this->extMap[$k] = $matches[1];
            }
        }
        $this->place = Yii::app()->request->getQuery('place', 0);
    }

    /**
     * 添加参数获取链接
     * @param string $name  ext中的参数名
     * @param string $value 对应ext参数名的值
     */
    public function add($name, $value)
    {
        return $this->createUrl($name , $value);
    }

    /**
     * 添加参数，与{@link add()}不同的是add()只返回链接，这个只返回单个参数
     * @param string $name  ext中的参数名
     * @param string $value 对应ext参数名的值
     * @param return string 单个参数
     */
    public function addParam($name, $value)
    {
        if(isset($this->_extTemplate[$name])&&$value){
            return $this->_extTemplate[$name][0].$value;
        } else {
            return '';
        }
    }

    /**
     * 删除参数获取链接
     * @return string 链接地址
     */
    public function remove($name)
    {
        return $this->createUrl($name);
    }

    /**
     * 构建链接
     * @param  string $name  ext中的参数名曾
     * @param  string $value 对应ext参数的值，若为空或者等于空的值将视作删除该ext参数
     * @return string 链接
     */
    public function createUrl($name, $value = '')
    {
        $params = array();
        if($name=='place'){
            if($value){
                $params['place'] = $value;
            }
        }elseif($this->place>0){
            $params['place'] = $this->place;
        }

        $ext = $this->extMap;
        foreach($ext as $k=>$v)
        {
            $ext[$k] = $this->_extTemplate[$k][0].$v;
        }
        if(isset($ext[$name])&&empty($value)){
            unset($ext[$name]);
        }elseif(isset($this->_extTemplate[$name])&&$value){
            $ext[$name] = $this->_extTemplate[$name][0].$value;
        }
        $ext = implode('_', $ext);
        if($ext){
            $params['ext'] = $ext;
        }

        return Yii::app()->createUrl($this->route, $params);
    }

    /**
     * 已选的元素以及删除的链接
     */
    public function addClearItem($chName, $item, $url='')
    {
        if(!$url){
            $url = $this->remove($item);
        }
        $this->clearItems[$item] = array('name'=>$chName, 'url'=>$url);
    }
}
