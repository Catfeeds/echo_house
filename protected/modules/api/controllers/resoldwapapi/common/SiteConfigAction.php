<?php
/**
 * 站点配置接口
 * @author steven allen <[<email address>]>
 * @date(2016.10.31)
 */
class SiteConfigAction extends CAction
{
	public function run()
	{
		$data = [
			// UC登录相关的东西
			'login_url'=>Yii::app()->uc->getLoginPageUrl(),
			'logout_url'=>Yii::app()->uc->getLogoutPageUrl(),
			'updatepwd_url'=>Yii::app()->uc->getUpdatePwdPageUrl(),
			'resetpwd_url'=>Yii::app()->uc->getResetPwdPageUrl(),
			'updatephone_url'=>Yii::app()->uc->getUpdatePhonePageUrl(),
			// 站点名
			'site_name'=>SM::globalConfig()->siteName(),
			// 城市名
			'city_name'=>SM::urmConfig()->cityName(),
			// 站点logo
			'site_logo'=>ImageTools::fixImage(SM::resoldImageConfig()->resoldWapSiteLogo()),
			// 微信分享图片
			'wx_share_img'=>ImageTools::fixImage(SM::resoldImageConfig()->resoldWxShareImage()),
			// 站点客服
			'site_phone'=>SM::globalConfig()->sitePhone(),
			// wap统计代码
			'wap_statistic'=>SM::resoldConfig()->resoldWapStatistic(),
			// 公积金计算器二维码
			'calculator_url'=>Yii::app()->controller->createUrl('/wap/calculator/index'),
			// 百科二维码
			'baike_url'=>Yii::app()->controller->createUrl('/wap/baike/index'),
			// 七牛上传地址
			'qiniu_uptoken_url'=>Yii::app()->controller->createUrl('/api/userapi/uptoken'),
			// 七牛主机
			'qiniu_domain'=>Yii::app()->file->host,
			// cnzz站点统计id
			'cnzz_siteid'=>SM::resoldConfig()->resoldWapCNZZSiteId(),
			// 开关
			'is_open_street'=>SM::resoldConfig()->resoldIsOpenStreet(),
			'is_open_plot_trend'=>SM::resoldConfig()->resoldIsOpenPlotTrend(),
			// wap首页SEO
			'wap_index_seo'=>SM::resoldSeoConfig()->resoldWapHomeIndex(), 
		];
		$this->controller->frame['data'] = $data;
	}
}