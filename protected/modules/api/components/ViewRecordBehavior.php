<?php
/**
 *
 * User: jt
 * Date: 2016/11/14 14:28
 */
class ViewRecordBehavior extends CBehavior{

    const VIEW_RECORD_NAME = 'wap_view_record';
    private $_data;
    public $expire_time;
    static $category = [1=>'zz',2=>'shop',3=>'xzl'];

    public function __construct()
    {
        $record = isset(Yii::app()->request->cookies[self::VIEW_RECORD_NAME]) ? Yii::app()->request->cookies[self::VIEW_RECORD_NAME]->value : '';
        if($record === ''){
            $this->_data = array();
        }else{
            $this->_data = CJSON::decode($record);
        }
        if($this->expire_time === null)
            $this->expire_time = time() + 3600*24*30;
    }

    /**
     * 增加浏览记录
     * $key sell rent qg qz
     * $value id title category
     */
    public function addViewRecord($key , $value){

        $_url = '/resoldwap/#/detail/{key}/{category}/{id}';
        $category = self::$category[$value['category']];
        $url = strtr($_url,array(
            '{key}'=>$key,
            '{category}'=>$category,
            '{id}'=>$value['id']
        ));
        $this->remove($key.$value['id']);
        $data = array($key.$value['id'] =>array('title' => $value['title'], 'url'=>$url));
        $this->_data = $data + $this->_data ;
        if(count($this->_data) > 5)
            array_pop($this->_data);
        return $this->setRecord();
    }

    public function viewRecordClear(){
        return Yii::app()->request->cookies->clear();
    }

    protected function setRecord(){
        if($this->_data === null)
            return ;
        $cookie = new CHttpCookie(self::VIEW_RECORD_NAME , CJSON::encode($this->_data));
        $cookie->expire = $this->expire_time;
        Yii::app()->request->cookies[self::VIEW_RECORD_NAME] = $cookie;
        return ;
    }

    public function remove($key){
        if(isset($this->_data[$key]) && $this->_data[$key])
            unset($this->_data[$key]);
        return ;
    }

    public function getViewRecord(){
        return $this->_data;
    }

}