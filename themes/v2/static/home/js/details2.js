$(function () {

    //顶部轮播
    var swiper = new Swiper('.swiper-container', {
//        pagination: '.swiper-pagination',
        paginationClickable: true
    });
    var swiper1 = new Swiper('.swiper-container1', {
//        pagination: '.swiper-pagination',
        slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 30
    });

    // 地图
    $('document').ready(function () {
        mapRoundSearch('公交');
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

    // 更多信息
    $('.down').click(function () {
        // if ($('.more_mes').is(':hidden')) {
        //     $('.more_mes').slideDown();
        // } else {
        //     $('.more_mes').slideUp();
        // }
        $('.more_mes').slideToggle();
    });

});

function mapRoundSearch(type) {
    map.clearOverlays();
    var options = {
        // onSearchComplete: function (results) {
        // console.log(results._pois)
        // if (local.getStatus() == BMAP_STATUS_SUCCESS) {
        //     $('#list').empty();
        //     // 判断状态是否正确
        //     var list = '';
        //     for (var i = 0; i < results.getCurrentNumPois(); i++) {
        // console.log(results.getPoi(i));
        // var tpl = '';
        // tpl += '<div class="nearby_r"><img src="img/tubiao.png" alt=""><p>' + results.getPoi(i).title + '<span style="color: #a0a0a0"></span></p><p style="color: #a0a0a0">' + results.getPoi(i).address + '</p><div class="nearby_line"></div></div>';
        // tpl += '打开';
        // tpl += '</a>';
        // $('#list').append(tpl);
        // }
        // }
        // },
        renderOptions: {map: map}
    };
    var local = new BMap.LocalSearch(map, options);
    local.search(type);
    local.searchInBounds(type, map.getBounds());
}



