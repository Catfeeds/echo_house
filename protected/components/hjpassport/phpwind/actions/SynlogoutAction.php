<?php
/**
 * UC接口动作
 * @author tivon
 * @version 2016-01-19
 */
class SynlogoutAction extends UcReceiverAction
{
    /**
     * 运行方法
     */
    public function run()
    {
        Yii::app()->uc->user->logout(false);
        //同步退出代码逻辑
        Yii::log('No'.$this->getController()->requestId.'，同步退出成功','info','passport');
    }
}
