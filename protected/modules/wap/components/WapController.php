<?php
/**
 * wap模块控制器基类
 * @author weibaqiu
 * @date 2015-09-22
 */
class WapController extends Controller
{
    /**
     * 返回链接
     * @var string
     */
    public $backUrl = '';
    public $banner = 'nobanner';
    /**
     * @var string 布局文件路径
     */
    public $layout = '/layouts/base';
    /**
     * @var string 页面description meta信息
     */
    public $pageDescription = '';
    /**
     * @var string 微信分享头图
     */
    public $_wxShareImg = '';
    /**
     * @var string 微信分享标题
     */
    public $_wxShareTitle = '';
    /**
     * @var WeChat小物件
     */
    public $weChat;

    /**
     * init方法
     */
    public function init()
    {
        parent::init();
        $this->backUrl = $this->createUrl('/wap/staff/index');
    }

    /**
     * 调用前端编写的js文件
     * 不在布局和模板中调用了，会涉及到公共js与单页js加载顺序问题，以及公共js并不是所有页公共，有的页面不需要用
     * @param array $fileNames 数组内为引入js的文件名，无需带.js格式后缀
     * @param boolean $posHead 是否放到body开头位置，默认放body结尾处
     */
    public function registerScriptFile(array $fileNames,$posHead=false)
    {
        $pos = $posHead===false ? CClientScript::POS_END : CClientScript::POS_HEAD;
        foreach($fileNames as $fileName){
            $pathRoot = Yii::app()->theme&&Yii::app()->theme->name=='v1' ? Yii::app()->baseUrl.'/static/wap/js/' : Yii::app()->theme->baseUrl.'/static/wap/js/';
            $jsPath = $pathRoot.$fileName.'.js';
            Yii::app()->clientScript->registerScriptFile($jsPath, $pos);
        }
    }

    /**
     * 将js放在开头
     * @param  array  $fileNames 数组内为引入js的文件名，无需带.js格式后缀
     */
    public function registerHeadJs(array $fileNames)
    {
        $this->registerScriptFile($fileNames,true);
    }

    /**
     * 将js文件放在结尾
     * @param  array  $fileNames 数组内为引入js的文件名，无需带.js格式后缀
     */
    public function registerEndJs(array $fileNames)
    {
        $this->registerScriptFile($fileNames);
    }

    //----------------------------微信分享相关--------------------------
    /**
     * 获取wechat小物件
     * @return WeChat | null
     */
    public function beginWeChat()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")!==false && $this->weChat===null) {
            $this->weChat = $this->beginWidget('WeChat', [
                'appId' => SM::wechatConfig()->appid(),
                'appSecret' => SM::wechatConfig()->appSecret()
            ]);
        }
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
        if($wx = $this->beginWechat()) {
            if($imgUrl=='' && SM::wechatConfig()->shareImg()) {
                $imgUrl = ImageTools::fixImage(SM::wechatConfig()->shareImg());
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

    /**
     * 设置微信分享头图
     * @param string $imgUrl 微信分享头图链接
     */
    public function setWxShareImg($imgUrl)
    {
        $this->_wxShareImg = $imgUrl;
    }

    /**
     * 获取微信分享头图
     * @return string
     */
    public function getWxShareImg()
    {
        if($this->_wxShareImg=='' && SM::wechatConfig()->shareImg()) {
            $this->_wxShareImg = ImageTools::fixImage(SM::wechatConfig()->shareImg());
        }
        return $this->_wxShareImg;
    }

    /**
     * 设置微信分享标题
     * @param string $title 标题
     */
    public function setWxShareTitle($title)
    {
        $this->_wxShareTitle = $title;
    }

    /**
     * 获取微信分享标题
     * @return string
     */
    public function getWxShareTitle()
    {
        if($this->_wxShareTitle=='') {
            $this->_wxShareTitle = $this->pageTitle;
        }
        return $this->_wxShareTitle;
    }
}
