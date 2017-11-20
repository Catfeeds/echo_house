$(function () {


    // var nav = $(".nav"); //得到导航对象
    // var win = $(window); //得到窗口对象
    // var sc = $(document);//得到document文档对象。
    // win.scroll(function () {
    //     if (sc.scrollTop() > 200) {
    //         nav.addClass("fixnav");
    //         $('.navlist').fadeIn()
    //     } else {
    //         nav.removeClass("fixnav");
    //         $('.navlist').fadeOut()
    //     }
    // })
    $('document').ready(function () {
        mapRoundSearch('公交');
    });
    // 图片切换
    var viewSwiper = new Swiper('.view .swiper-container', {
        onSlideChangeStart: function () {
            updateNavPosition()
        }
    })

    $('.view .arrow-left,.preview .arrow-left').on('click', function (e) {
        e.preventDefault()
        if (viewSwiper.activeIndex == 0) {
            viewSwiper.swipeTo(viewSwiper.slides.length - 1, 1000);
            return
        }
        viewSwiper.swipePrev()
    })
    $('.view .arrow-right,.preview .arrow-right').on('click', function (e) {
        e.preventDefault()
        if (viewSwiper.activeIndex == viewSwiper.slides.length - 1) {
            viewSwiper.swipeTo(0, 1000);
            return
        }
        viewSwiper.swipeNext()
    })

    var previewSwiper = new Swiper('.preview .swiper-container', {
        visibilityFullFit: true,
        slidesPerView: 'auto',
        onlyExternal: true,
        onSlideClick: function () {
            viewSwiper.swipeTo(previewSwiper.clickedSlideIndex)
        }
    })

    function updateNavPosition() {
        $('.preview .active-nav').removeClass('active-nav')
        var activeNav = $('.preview .swiper-slide').eq(viewSwiper.activeIndex).addClass('active-nav')
        if (!activeNav.hasClass('swiper-slide-visible')) {
            if (activeNav.index() > previewSwiper.activeIndex) {
                var thumbsPerNav = Math.floor(previewSwiper.width / activeNav.width()) - 1
                previewSwiper.swipeTo(activeNav.index() - thumbsPerNav)
            } else {
                previewSwiper.swipeTo(activeNav.index())
            }
        }
    }


    // 隐藏导航栏
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 400) {
            $('.hide_nav').fadeIn()
        } else {
            $('.hide_nav').fadeOut()
        }
    })

    // 更多户型
    $('.down').click(function () {
        if ($('.more_house').is(':hidden')) {
            $('.more_house').slideDown();

        } else {
            $('.more_house').slideUp();
        }
    });


    // $('.more').click(function () {
    //     if ($('.down').is(':hidden')) {
    //         $('.up').show();
    //     } else {
    //         $('.down').hide();
    //     }
    // })
    // 地图
    //创建和初始化地图函数：
    //    function initMap(){
    //        createMap();//创建地图
    //        setMapEvent();//设置地图事件
    //        addMapControl();//向地图添加控件
    //    }
    //
    //    //创建地图函数：
    //    function createMap(){
    // var map = new BMap.Map("dituContent");//在百度地图容器中创建一个地图
    // var point = new BMap.Point(120.3189534644, 31.4967394989);//定义一个中心点坐标
    // map.centerAndZoom(point, 13);//设定地图的中心点和坐标并将地图显示在地图容器中
    // //        window.map = map;//将map变量存储在全局
    //    }
    //
    //    //地图事件设置函数：
    //    function setMapEvent(){
    // map.enableDragging();//启用地图拖拽事件，默认启用(可不写)
    // map.enableScrollWheelZoom();//启用地图滚轮放大缩小
    // map.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
    // map.enableKeyboard();//启用键盘上下左右键移动地图
    //    }
    //
    //    //地图控件添加函数：
    //    function addMapControl(){
    //向地图中添加缩放控件
    // var ctrl_nav = new BMap.NavigationControl({
    //     anchor: BMAP_ANCHOR_TOP_LEFT,
    //     type: BMAP_NAVIGATION_CONTROL_LARGE
    // });
    // map.addControl(ctrl_nav);
    // //向地图中添加缩略图控件
    // // var ctrl_ove = new BMap.OverviewMapControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, isOpen: 1});
    // // map.addControl(ctrl_ove);
    // //向地图中添加比例尺控件
    // var ctrl_sca = new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT});
    // map.addControl(ctrl_sca);
    //    }


    //    initMap();//创建和初始化地图


    // 地图左
    $('.nearby li').click(function () {
        var index = $('.nearby li').index(this);//获取索引值
        $('.nearby_r').hide();
        $('.nearby_r').eq(index).show();
    })

    // 点击显示二维码
    $('.back_btn_l').click(function () {
        if ($('.back_show').is(':hidden')) {
            $('.back_show').show();
        } else {
            $('.back_show').hide();
        }
    });
    // 返回顶部
    $('.back_top').click(function () {
        $('body,html').animate({scrollTop: 0}, 700);
    });


    // 弹框
    $('.open').click(function () {
        if ($('.tankuang').is(':hidden')) {
            $('.tankuang').show();
        } else {
            $('.tankuang').hide();
        }
    })
    $('.cha').click(function () {
        $(this).parents().parent('.tankuang').hide();
    })
})

function mapRoundSearch(type) {
    map.clearOverlays();
    // var local = new BMap.LocalSearch(map, {
    //     renderOptions:{map: map}
    // });
    var options = {
        onSearchComplete: function (results) {
            // console.log(results._pois)
            if (local.getStatus() == BMAP_STATUS_SUCCESS) {
                $('#list').empty();
                // // 判断状态是否正确
                var list = '';
                for (var i = 0; i < results.getCurrentNumPois(); i++) {
                    // if()
                    console.log(results.getPoi(i));
                    var tpl = '';
                    tpl += '<div class="nearby_r"><img style="width:20px;height:20px" src="../themes/v2/static/home/img/tubiao.png" alt=""><p>' + results.getPoi(i).title + '<span style="color: #a0a0a0"></span></p><p style="color: #a0a0a0">' + results.getPoi(i).address + '</p><div class="nearby_line"></div></div>';
                    // tpl += '打开';
                    // tpl += '</a>';
                    $('#list').append(tpl);
                }


                // new Vue({
                //     el:"#list",
                //     data:{
                //         items: s
                //     }
                // })
            }
        },
        renderOptions: {map: map}
    };
    var local = new BMap.LocalSearch(map, options);
    local.search(type);
    local.searchInBounds(type, map.getBounds());


}


