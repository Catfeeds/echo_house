<?php
/**
 * 配置元素行为类
 * 为了扩展配置元素相关配套的函数
 * @author tivon
 * @version 2016-11-03
 */
class SiteSettingBehavior extends CActiveRecordBehavior
{
    /**
     * 扩展函数类的实例
     * @var CComponent
     */
    private $_funcObj;

    /**
     * 在该行为类实例化后进行类属性声明
     * 目的是为了使得在[[$owner]]中能直接调用行为类中的方法（可见CComponent::__call
     * 要么是行为类中存在的方法，要么是类属性并且是一个闭包函数，这里我们采用第二种）
     */
    public function init()
    {
        $reflectionClass = new ReflectionClass($this->getClass());
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach($methods as $method) {
            $this->{$method->name} = function(){
                return call_user_func_array([$this->_funcObj, $method->name], func_get_args());
            };
            var_dump($this->{$method->name});die;
        }
    }

    /**
     * 获取该配置元素对应的函数方法类的类名
     * @return string
     */
    public function getClassName()
    {
        $className = $this->owner->class_name;
        $className = str_replace('ConfigExt', '', $className);
        $attributeName = $this->owner->name;
        return ucfirst($className) . ucfirst($attributeName) . 'Func';
    }

    /**
     * 获得该配置元素对应的函数方法类的类示例
     * @param  boolean $reNew true表示重新new一个方法类对象
     * @return Object
     */
    public function getClass($reNew = false)
    {
        $className = $this->getClassName();
        if($this->_funcObj===null || $reNew) {
            Yii::import('application.behaviors.siteSetting.'.$className);
            $this->_funcObj = new $className();
        }
        return $this->_funcObj;
    }
}
