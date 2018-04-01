$(function () {
    $('document').ready(function () {
        mapRoundSearch('公交');
    });
    // 图片切换
    var viewSwiper = new Swiper('.view .swiper-container', {
        onSlideChangeStart: function () {
            updateNavPosition()
        }
    });

    $('.view .arrow-left,.preview .arrow-left').on('click', function (e) {
        e.preventDefault();
        if (viewSwiper.activeIndex == 0) {
            viewSwiper.swipeTo(viewSwiper.slides.length - 1, 1000);
            return
        }
        viewSwiper.swipePrev()
    });
    $('.view .arrow-right,.preview .arrow-right').on('click', function (e) {
        e.preventDefault();
        if (viewSwiper.activeIndex == viewSwiper.slides.length - 1) {
            viewSwiper.swipeTo(0, 1000);
            return
        }
        viewSwiper.swipeNext()
    });
    var previewSwiper = new Swiper('.preview .swiper-container', {
        visibilityFullFit: true,
        slidesPerView: 'auto',
        onlyExternal: true,
        onSlideClick: function () {
            viewSwiper.swipeTo(previewSwiper.clickedSlideIndex)
        }
    });

    function updateNavPosition() {
        $('.preview .active-nav').removeClass('active-nav');
        var activeNav = $('.preview .swiper-slide').eq(viewSwiper.activeIndex).addClass('active-nav');
        if (!activeNav.hasClass('swiper-slide-visible')) {
            if (activeNav.index() > previewSwiper.activeIndex) {
                var thumbsPerNav = Math.floor(previewSwiper.width / activeNav.width()) - 1;
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
    });

    // 更多户型
    $('.down').click(function () {
        // if ($('.more_house').is(':hidden')) {
        //     $('.more_house').slideDown();
        //
        // } else {
        //     $('.more_house').slideUp();
        // }
        $('.more_house').slideToggle();
    });

    // 地图左
    $('.nearby li').click(function () {
        var index = $('.nearby li').index(this);//获取索引值
        $('.nearby_r').hide();
        $('.nearby_r').eq(index).show();
    });

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
    });
    $('.cha').click(function () {
        $(this).parents().parent('.tankuang').hide();
    })
});

function mapRoundSearch(type) {
    map.clearOverlays();
    var options = {
        onSearchComplete: function (results) {
            // console.log(results._pois)
            if (local.getStatus() == BMAP_STATUS_SUCCESS) {
                $('#list').empty();
                // // 判断状态是否正确
                var list = '';
                for (var i = 0; i < results.getCurrentNumPois(); i++) {
                    console.log(results.getPoi(i));
                    var tpl = '';
                    tpl += '<div class="nearby_r"><img src="img/tubiao.png" alt=""><p>' + results.getPoi(i).title + '<span style="color: #a0a0a0"></span></p><p style="color: #a0a0a0">' + results.getPoi(i).address + '</p><div class="nearby_line"></div></div>';
                    $('#list').append(tpl);
                }
            }
        },
        renderOptions: {map: map}
    };
    var local = new BMap.LocalSearch(map, options);
    local.search(type);
    local.searchInBounds(type, map.getBounds());
}


