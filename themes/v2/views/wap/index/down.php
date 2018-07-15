
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>经纪圈 - 中国房产经纪人专属社区</title>
        <meta name="description" content="经纪圈APP是中国房产经纪人的社区 涵盖行业求职招聘、行业新闻、经纪学堂、同行交友、新房分销、分销系统管理等" />
        <meta name="keywords" content="房产中介，房产经纪人，经纪圈，经纪圈app，经纪圈新房通" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <meta content="telephone=no" name="format-detection">
        <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl?>/static/wap/css/swiper.min.css" />
        <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl?>/static/wap/css/phone.css">
    </head>

    <body>
        <div class="main">
            <div class="header-banner">
                <div class="h-main">
                    <p class="h-logo">
                        <span><img src="<?=Yii::app()->theme->baseUrl?>/static/wap/img/XMQ_icon@2x.png"></span>
                        <label>经纪圈</label>
                    </p>
                    <p class="h-font">中国房地产经纪人专属社区</p>
                    <a class="hi-button b-r-4 js-button"><span></span></a>
                </div>
            </div>
            <div class="h-content">
                <div class="h-txt">
                    <p>经纪圈APP是房地产经纪人的专属社区</p>
                    <p>已有30,0000+经纪人加入</p>
                    <p>更多精彩请进入经纪圈APP</p>
                    <p>点击右上方【关于经纪圈新房通】进入下载页面</p>
                </div>
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="<?=Yii::app()->theme->baseUrl?>/static/wap/img/qrcode1.png"></div>
                    </div>
                    <!-- <div class="swiper-button-next"></div> -->
                    <!-- <div class="swiper-button-prev"></div> -->
                </div>
            </div>
        </div>
        <div class="footer in_footer">
            <div class="nav">
                <span>版本所有&copy;经纪圈&nbsp;上海贺诺网络科技有限公司</span>
                <p>
                    <a class="beian" href="https://www.miitbeian.gov.cn/" target="_blank">沪ICP备16037646号</a>
                </p>
            </div>
        </div>

        <script type="text/javascript" src="<?=Yii::app()->theme->baseUrl?>/static/wap/js/zepto.min.js"></script>
        <script type="text/javascript" src="<?=Yii::app()->theme->baseUrl?>/static/wap/js/swiper.min.js"></script>
        <script type="text/javascript">
            $(function() {
                //自适应设置根节点
                $.fn.fontFlex = function(min, max, mid) {
                    var $this = this;
                    $(window).resize(function() {
                        var size = window.innerWidth / mid;
                        if (size < min) {
                            size = min;
                        }
                        if (size > max) {
                            size = max;
                        }
                        $this.css('font-size', size + 'px');
                    }).trigger('resize');
                };
                $("html").fontFlex(12, 20, 50);
                //swiper切换
                var mySwiper = new Swiper('.swiper', {
                    autoplay: 3000,
                    prevButton: '.swiper-button-prev',
                    nextButton: '.swiper-button-next',
                    slidesPerView: 1,
                    effect: 'fade',
                    paginationClickable: true,
                    loop: true
                });
                //判断设备
                if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
                    // $(".js-button").find("span").addClass("add-ios");
                    $(".js-button").find("span").html("更多精彩请下载APP");
                } else if (/(Android)/i.test(navigator.userAgent)) {
                    // $(".js-button").find("span").addClass("add-android");
                    $(".js-button").find("span").html("更多精彩请下载APP");
                } else {
                    window.location.href = "index.htm";
                }
            });
        </script>
    </body>

</html>