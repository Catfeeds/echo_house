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
var hid = '';
var title='';
var phone='';
var arehid='';
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
    // if($('.detailshow').is('hide')) {
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
    hid=GetQueryString('hid');
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

    $.get('/api/plot/getAskList?hid='+hid, function(data) {
        o.page = data.data.page;
        o.page_count = data.data.page_count;
        o.num = data.data.num;
        if (data.data.length == undefined) {
            var a=data.data.list;
            console.log(a)
            var Html = '';
            for(var i = 0; i < a.length; i++){
                var Ele =  '<a class="question-info" href="/subwap/questionDetail.html?aid='+a[i].id +'">' +
                    '            <div class="question-info-wen">' +
                    '                <span class="icon icon-wen">问</span>' +
                    '                <span>'+ a[i].title +'</span>' +
                    '            </div>' +
                    '            <div class="question-info-da">' +
                    '                <span class="icon icon-da">答</span>' +
                    '                <span>'+ a[i].first_answer.note +'</span>' +
                    '            </div>' +
                    '            <div class="que-ops">' +
                    '                <span class="answ-entry">' +
                    '                    查看'+ a[i].answers_count +'个回答' +
                    '                    <img class="lookup-img" src="./img/).png">' +
                    '                </span>' +
                    '                <span class="time">'+ a[i].time +'</span>' +
                    '            </div>' +
                    '        </a>';
                Html = Html + Ele;
            }
            $('.question-container').append(Html);
        }
        $('#num').html(o.num);
        // 加载中消失
        $('.loaddiv').css('display','none');
    });
}


$(document).ready(function() {
    ajaxAddList(o);
    $('.que-footer').click(function(){
        location.href='/subwap/questionSubmit.html?hid='+hid;
    });
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
