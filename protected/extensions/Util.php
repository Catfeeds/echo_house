<?php
class Util
{
    /**
     * get方法，用于获取对象属性或者数组键值
     * 会先判断需要获取的值是否存在
     * @param  object|array $val 需要取值的数组或者对象
     * @param  string $param     数组键值名｜对象属性名
     * @param  string $default   默认值
     * @return mixed
     */
    public static function get($val, $param, $default = '')
    {
        if (is_array($val)) {
            if (isset($val[$param])) {
                return $val[(string)$param];
            }
        } elseif (is_object($val)) {
            if (property_exists($val, (string)$param)) {
                return $val->{$param};
            }
        }
        return $default;
    }
}