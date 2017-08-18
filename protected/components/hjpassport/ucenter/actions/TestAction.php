<?php
/**
 * UC接口动作
 * @author tivon
 * @version 2016-01-13
 */
class TestAction extends UcReceiverAction
{
    /**
     * 运行方法
     * @param  array $get  get参数
     * @param  array $post post参数
     */
    public function run()
    {
        Yii::log('服务端测试通信成功','info','passport');
        echo self::API_RETURN_SUCCEED;
    }
}
