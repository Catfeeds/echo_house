<?php
/**
 * 用于Ar类
 */
class ArHelper
{
    /**
     * 类似CHtml::listData
     * 将一个二维数组$arr转换成一维数组
     * 其中$arr中二维数组key为name的键值对将被提出作为新数组的值，$arr中一维数组的key将被提出作为新数组的键
     * 若$arr中二维数组存在键名为key的值，该值将代替一维数组中的key被提出作为新数组的键
     * 例子：
     * $arr = array(
     *      0=>array('name'=>'demo1','key'=>3,'ext'=>'..'),
     *      1=> array('name'=>'demo2','ext'=>'..'),
     * )
     * 将被转换成
     * $newarr = array(
     *      3 => 'demo1',
     *      1 => 'demo2'
     * )
     * @param  array $arr AR类中定义的静态变量数组
     * @return array 组合后的新数组
     */
    public static function listData(array $arr)
    {
        $newArr = array();
        foreach($arr as $k=>$v)
        {
            if(isset($v['key']))
                $newArr[$v['key']] = $v['name'];
            else
                $newArr[$k] = $v['name'];
        }
        return $newArr;
    }
}
?>