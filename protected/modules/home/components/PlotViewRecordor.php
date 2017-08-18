<?php
/**
 * 最近浏览的楼盘记录器
 */
class PlotViewRecordor extends CComponent
{
    /**
     * cookie 名称
     */
    const COOKIE_NAME = 'recent_view_plot';
    /**
     * 存放浏览的楼盘id
     * @var array
     */
    private $_list = array();

    public function __construct()
    {
        $this->getFromCookie();
    }

    /**
     * 将数据存储在cookie中
     * @return boolean
     */
    private function storeToCookie()
    {
        $cookie = new CHttpCookie(self::COOKIE_NAME, CJSON::encode($this->_list), array(
            'expire' => time() + 3600*24*30
        ));
        Yii::app()->request->cookies[self::COOKIE_NAME] = $cookie;
        return true;
    }

    /**
     * 从cookie中获取浏览记录
     * @return array
     */
    private function getFromCookie()
    {
        $cookie = Yii::app()->request->getCookies();
        if(isset($cookie[self::COOKIE_NAME]) && $data = CJSON::decode($cookie[self::COOKIE_NAME]->value)){
            $this->_list = $data;
        }
    }

    /**
     * 记录当前浏览楼盘
     * @param PlotExt $plot 要记录的楼盘AR类
     */
    public function add(PlotExt $plot)
    {
        if(!in_array($plot->id, $this->_list)){
            $this->_list[] = $plot->id;
        }
        if($this->getTotal()>10){
            array_shift($this->_list);
        }
        $this->storeToCookie();
    }

    /**
     * 移除指定楼盘浏览记录，暂未实现，用不到
     */
    public function remove()
    {

    }

    /**
     * 获取浏览的楼盘AR实例
     * @return PlotExt[] 返回数组
     */
    public function getViewedPlots()
    {
        $return = array();
        if(is_array($this->_list) && $this->_list){
            $plots = PlotExt::model()->findAllByPk($this->_list, array('index'=>'id'));
            foreach($this->_list as $id){
                isset($plots[$id]) && $return[$id] = $plots[$id];
            }
        }
        return $return;
    }

    /**
     * 获取当前已经记录的浏览量
     * @return integer
     */
    public function getTotal()
    {
        return count($this->_list);
    }
}
