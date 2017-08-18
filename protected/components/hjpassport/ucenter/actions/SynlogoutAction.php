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
        if(!self::API_SYNLOGOUT) {
			echo self::API_RETURN_FORBIDDEN;
		}
        //同步退出代码逻辑
        Yii::app()->uc->user->logout(false);
        Yii::log('同步退出成功','info','passport');

        echo self::API_RETURN_SUCCEED;
    }
}
