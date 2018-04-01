var o = new Object();
function init() {
    o.toptag = '';
    o.area = '';
    o.street = '';
    o.aveprice = '';
    o.sfprice = '';
    o.sort = '';
    o.wylx = '';
    o.kw = '';
    o.company = '';
    o.zxzt = '';
    o.page = '';
    o.page_count = '';
    o.num = '';
    o.minprice = '';
    o.maxprice = '';
}
//==============核心代码=============
var winH = $(window).height(); //页面可视区域高度
//获取传过来的ID的函数
var aid = '';
var title='';
var phone='';
var areaid='';
var streetid='';
var url='';
var our_uids = '';
var thisphone = '';
var thisurl = '';
// var is_user = false;
var listheight='';
var detail=new Object();

var topimglist = new Array;
var hximglist = new Array;
Array.prototype.contains = function ( needle ) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}
var scrollHandler = function() {
    // if($('.detailshow').is('aide')) {
    var pageH = $('.comment-list').height();
    var scrollT = $(window).scrollTop(); //滚动条top
    var aa = (pageH - winH - scrollT) / winH;
    console.log(aa)
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
    var params = '?t=1';
    aid=GetQueryString('aid');
    if (obj.toptag != '' && obj.toptag != 'undefined') {
        params += '&toptag=' + obj.toptag;
    }
    if (obj.area != '' && obj.area != 'undefined') {
        params += '&area=' + obj.area;
    }
    if (obj.street != '' && obj.street != 'undefined') {
        params += '&street=' + obj.street;
    }
    if (obj.aveprice != '' && obj.aveprice != undefined) {
        params += '&aveprice=' + obj.aveprice;
    }
    if (obj.sfprice != '' && obj.sfprice != undefined) {
        params += '&sfprice=' + obj.sfprice;
    }
    if (obj.sort != '' && obj.sort != undefined) {
        params += '&sort=' + obj.sort;
    }
    if (obj.wylx != '' && obj.wylx != undefined) {
        params += '&wylx=' + obj.wylx;
    }
    if (obj.kw != '' && obj.kw != undefined) {
        params += '&kw=' + obj.kw;
    }
    if (obj.company != '' && obj.company != undefined) {
        params += '&company=' + obj.company;
    }
    if (obj.page != '' && obj.page != undefined) {
        params += '&page=' + obj.page;
    }
    if (obj.maxprice != '' && obj.maxprice != undefined) {
        params += '&maxprice=' + obj.maxprice;
    }
    if (obj.minprice != '' && obj.minprice != undefined) {
        params += '&minprice=' + obj.minprice;
    }

    $.get('/api/plot/getAnswerList?aid='+aid, function(data) {
        o.page = data.data.page;
        o.page_count = data.data.page_count;
        o.num = data.data.num;
        if (data.data.length == undefined) {
            var a=data.data;
            console.log(a)
            var detailHtml = '<div class="ques-wrap">' +
                '            <span class="icon icon-wen">问</span>' +
                '            <p class="ques-content">'+ a.ask_title +'</p>' +
                '        </div>' +
                '        <div class="que-ops">' +
                '            <span>'+ a.ask_username + '</span>' +
                '            <span>'+ a.ask_time + '</span>' +
                '        </div>';

            $('.que-block').append(detailHtml);
            $('.block-header').html('共有' + data.data.page_count +'个回答');
            var Html = '';
            for(var i = 0; i < a.list.length; i++){
                var Ele =  ' <div class="answ-one">' +
                    '                <div class="user-info">' +
                    '                    <img class="user-portrait" src="'+ a.list[i].image +'">' +
                    '                    <span class="user-name">'+ a.list[i].name +'</span>' +
                    '                </div>' +
                    '                <div class="answ-content">' + a.list[i].note +'</div>' +
                    '                <div class="creat-time">' + a.list[i].time +'</div>' +
                    '                <div class="g-border-bottom"></div>' +
                    '            </div>';
                Html = Html + Ele;
            }
            $('.block-content').append(Html);
        }
        $('#num').html(o.num);
        // 加载中消失
        $('.loaddiv').css('display','none');
    });
}


$(document).ready(function() {
    ajaxAddList(o);
    $('.btn-wen').click(function(){
        location.href='/subwap/questionSubmit.html?aid='+aid;
    });
    $('.btn-da').click(function(){
        location.href='/subwap/answerSubmit.html?aid='+aid;
    });
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
