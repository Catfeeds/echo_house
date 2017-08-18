<?php
/**
 * 测试通信接口，类似uc中的testAction
 * @author tivon
 * @version 2016-01-13
 */
class ConnectAction extends UcReceiverAction
{
    /**
     * 运行方法
     * @param  array $get  get参数
     * @param  array $post post参数
     */
    public function run()
    {
        Yii::log('No:'.$this->getController()->requestId.'，服务端测试通信成功','info','passport');
        echo $this->response(1);
    }
}
