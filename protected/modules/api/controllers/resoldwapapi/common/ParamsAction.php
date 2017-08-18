<?php

/**
 * User: fanqi
 * Date: 2016/9/29
 * Time: 9:12
 *
 * 参数接口
 * ================================
 * [request]请求：
 * string key 参数名
 * ================================
 * [response]响应：
 * object params params参数无法确定没设参数
 */
class ParamsAction extends CAction
{
    public function run()
    {
        $key = Yii::app()->request->getQuery('key');
//        $key = 'userApi';
        $params = Yii::app()->params[$key];
//        print_r($params;
//        die;
        $this->getController()->frame['data'] = [
            'params'=>$params
        ];
    }
}