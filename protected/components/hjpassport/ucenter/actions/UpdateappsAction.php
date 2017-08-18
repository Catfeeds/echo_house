<?php
/**
 * UC接口动作
 * @author tivon
 * @version 2016-01-13
 */
class UpdateappsAction extends UcReceiverAction
{
    /**
     * 运行方法
     * @param  array $get  get参数
     * @param  array $post post参数
     */
    public function run()
    {
        if(!self::API_UPDATEAPPS) {
			echo self::API_RETURN_FORBIDDEN;
		}
        Yii::app()->cache->set(HjUc::CACHE_NAME_APPS,$this->post);
        Yii::log('成功更新app','info','passport');
		echo self::API_RETURN_SUCCEED;
    }
}
