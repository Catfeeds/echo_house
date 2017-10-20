<?php
class WxController extends Controller{
	public $weChat;
	public function actionZone($imgUrl='',$title='', $link='',$desc='',$phone='')
	{
        // $baseUrl = Yii::app()->request->getHostInfo();
        // $linkarr = explode('_', $link);

        // var_dump($link);exit;
		$this->onMenuShareTimeline($imgUrl, $title, $link);
        $this->endWeChat();
        $this->onMenuShareAppMessage($imgUrl, $title,$desc, $link);
        $this->endWeChat();
		
	}

	/**
     * 获取wechat小物件
     * @return WeChat | null
     */
    public function beginWeChat($url='')
    {
        // if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")!==false && $this->weChat===null) {
            $this->weChat = $this->beginWidget('WeChat', [
                'appId' => SiteExt::getAttr('qjpz','appid'),
                'appSecret' => SiteExt::getAttr('qjpz','appsecret'),
                'url'=>$url,
            ]);
        // }
            // var_dump(1,$this->weChat);exit;
        return $this->weChat;
    }

    /**
     * 结束wechat小物件
     * @return
     */
    public function endWeChat()
    {
        if($this->weChat!==null ) {
            $this->endWidget();
        }
    }

    /**
     * 分享到朋友圈
     * @param  string $imgUrl 头图链接，传入空字符串让微信自动抓取图片
     * @param  string $title  标题
     * @param  string $link   链接地址
     * @return void
     */
    public function onMenuShareTimeline($imgUrl='', $title='', $link='')
    {
        if($wx = $this->beginWechat($link)) {
            if($imgUrl=='' && SiteExt::getAttr('qjpz','wx_share_image')) {
                $imgUrl = ImageTools::fixImage(SiteExt::getAttr('qjpz','wx_share_image'));
            }
            if($imgUrl) $wx->onMenuShareTimeline($imgUrl, $title, $link);
        }
    }

    /**
     * 分享到聊天窗口
     * @param  string $imgUrl 头图地址，传入空字符串让微信自动抓取图片
     * @param  string $title  标题
     * @param  string $desc   摘要描述
     * @param  string $link   链接
     * @return void
     */
    public function onMenuShareAppMessage($imgUrl='', $title='',$desc='', $link='')
    {
        if($wx = $this->beginWechat()) {
            if($imgUrl) $wx->onMenuShareAppMessage($imgUrl, $title,$desc, $link);
        }
    }
}