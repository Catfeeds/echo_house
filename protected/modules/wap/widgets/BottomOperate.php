<?php
/**
 * wap底部通话浮窗
 * 适用：v2版
 * @author weibaqiu
 * @version 2016-06-13
 */
class BottomOperate extends CWidget
{
    public $plot;

    public $url1;
    public $url2;
    public $url3;

    /**
     * 蛋疼。前端设计了两种样式，一种是ul的，另一种是div的
     * 该属性填写'div'或'ul'即可
     * @var string
     */
    public $style = 'div';

    public function run()
    {
        if($this->url1===null && $this->plot!=null && $this->plot->sale_tel) {
            $this->url1 = 'tel:'.$this->plot->formatSaleTel;
        }
        if($this->url1===null) {
            $this->url1 = 'javascript:alert("暂无号码")';
        }

        //2016.05.25问了刚哥，在线咨询放网站页面右侧的QQ，取第一个就行
        if($this->url2===null&&$qqs = SM::globalConfig()->siteQq()) {
            $qq = '';
            foreach($qqs['type'] as $k=>$v) {
                if($v=='qq' && $qqs['number'][$k]) {
                    $this->url2 = 'mqqwpa://im/chat?chat_type=wpa&uin='.$qqs['number'][$k].'&version=1&src_type=web';
                }
            }
        }
        if($this->url2===null) $this->url2 = 'javascript:alert("暂无");';

        if($this->url3===null) {
            $this->url3 = $this->owner->createUrl('/wap/adviser/index');
        }

        $this->render('BottomOperate');
    }
}
