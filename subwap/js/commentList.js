var o = new Object();
function init() {
    o.page = 1;
    o.page_count = '';
    o.num = '';
}
//==============核心代码=============
var winH = $(window).height(); //页面可视区域高度
//获取传过来的ID的函数
var hid = '';
var detail=new Object();

Array.prototype.contains = function ( needle ) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}
var scrollHandler = function() {
    // if($('.detailshow').is('hide')) {
    var pageH = $('.comment-list').height();
    var scrollT = $(window).scrollTop(); //滚动条top
    var aa = (pageH - winH - scrollT) / winH;
    if (aa < 0.02) { //0.02是个参数
        if (o.page < o.page_count) {
            o.page++;
            ajaxAddList(o);
        }
    }
    // }
}
//定义鼠标滚动事件
$(window).scroll(scrollHandler);
//==============核心代码=============


function ajaxAddList(obj) {
    // 出现加载中
    $('.loaddiv').css('display','block');
    hid=GetQueryString('hid');
    var params = '?hid=' + hid;
    if (obj.page != '' && obj.page != undefined) {
        params += '&page=' + obj.page;
    }

    $.get('/api/plot/getDpList'+params, function(data) {
        o.page_count = data.data.page_count;
        o.num = data.data.num;
        if (data.data.length == undefined) {
            var a=data.data.list;
            var askHtml = '';
            for(var i = 0; i < a.length; i++){
                var aksEle =  '<div class="comment-message">'+
                    '<img src="' + a[i].image +'" />'+
                    '<div class="comment-info">'+
                    '<span class="username">'+ a[i].name +'</span>'+
                    '<div class="usercontent">'+ a[i].note +'</div>'+
                    '<span class="time">'+ a[i].time +'</span>'+
                    '</div>'+
                    '</div>';
                askHtml = askHtml + aksEle;
            }
            $('.comment-container').append(askHtml);
        }
        $('#num').html(o.num);
        // 加载中消失
        $('.loaddiv').css('display','none');
    });
}


$(document).ready(function() {
    init()
    ajaxAddList(o);
    $('.que-footer').click(function(){
        location.href='/subwap/commentSubmit.html?hid='+hid;
    });
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
