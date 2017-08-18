<?php
/**
 * 微信配置接口
 * @author steven allen <[<email address>]>
 * @date(2016.11.25)
 */
class WechatAction extends CAction
{
	public function run($url)
	{
		$appId = SM::wechatConfig()->appid();
		$appSecret = SM::wechatConfig()->appSecret();
		$wechat = new WeChatSdk($appId, $appSecret, $url);
        $sign = $wechat->getSignPackage();
        $this->controller->frame['data'] = $sign;
	}
}