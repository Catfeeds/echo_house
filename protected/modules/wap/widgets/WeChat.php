<?php
class WeChat extends CWidget
{
    /**
     * 微信公众号AppId
     * @var string
     */
    public $appId;
    /**
     * 微信公众号appSecret
     * @var string
     */
    public $appSecret;
    /**
     * 配置有效则启用
     * @var boolean
     */
    public $enable=false;
    /**
     * 是否调试
     * @var boolean
     */
    public $debug = false;
    /**
     * jsapilist
     * @var array
     */
    public $jsApiList = array('onMenuShareTimeline','onMenuShareAppMessage');

    public function init()
    {
        if(!$this->getIsEnable()) return;
        //加载js文件
        echo '<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" charset="utf-8"></script>';
        echo '<script>';
        $this->generateConfig();
        echo 'wx.ready(function(){';
    }

    public function run()
    {
        if(!$this->getIsEnable()) return;
        echo '});</script>';
    }

    /**
     * 根据配置输出
     * @return boolean
     */
    public function getIsEnable()
    {
        return ((bool)$this->appId && (bool)$this->appSecret);
    }

    /**
     * 生成微信配置参数
     * @return array
     */
    public function generateConfig()
    {
        $wechat = new WeChatSdk($this->appId, $this->appSecret);
        $sign = $wechat->getSignPackage();
        $str = "wx.config({
                debug: {debug},
                appId: '{appId}', // 必填，公众号的唯一标识
                timestamp: {timestamp}, // 必填，生成签名的时间戳
                nonceStr: '{nonceStr}', // 必填，生成签名的随机串
                signature: '{signature}',// 必填，签名，见附录1
                jsApiList: {jsApiList} // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });";
        $search  = array('{debug}','{appId}','{timestamp}','{nonceStr}','{signature}','{jsApiList}');
        $replace = array(
            CJSON::encode($this->debug),
            isset($sign['appId']) ? $sign['appId'] : '',
            isset($sign['timestamp']) ? $sign['timestamp'] : '',
            isset($sign['nonceStr']) ? $sign['nonceStr'] : '',
            isset($sign['signature']) ? $sign['signature'] : '',
            CJSON::encode($this->jsApiList),
        );
        $js = str_replace($search, $replace, $str);
        echo $js;
    }

    /**
     * 输出js
     * @param  string $jsCode js代码
     */
    public function renderJs($jsCode)
    {
        if(!$this->getIsEnable()) return;
        echo $jsCode;
    }

    /**
     * 分享朋友圈设置
     */
    public function onMenuShareTimeline($imgUrl='', $title='', $link='')
    {
        $title = strip_tags($title);
        $str = "wx.onMenuShareTimeline({
            title: '{title}', // 分享标题
            link: '{link}', // 分享链接
            imgUrl: '{imgUrl}', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });";
        $search = array('{title}','{link}','{imgUrl}');
        $replace = array($title, $link, $imgUrl);
        $js = str_replace($search, $replace, $str);
        $this->renderJs($js);
    }

    /**
     * 分享给朋友设置
     */
    public function onMenuShareAppMessage($imgUrl='', $title='',$desc='', $link='')
    {
        $title = strip_tags($title);
        $desc = Tools::substr($desc,30);
        $str = "wx.onMenuShareAppMessage({
            title: '{title}', // 分享标题
            link: '{link}', // 分享链接
            desc: '{desc}', // 分享描述
            imgUrl: '{imgUrl}', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });";
        $search = array('{title}','{link}','{imgUrl}','{desc}');
        $replace = array($title, $link, $imgUrl, $desc);
        $js = str_replace($search, $replace, $str);
        $this->renderJs($js);
    }
}
