var o = new Object();
function init() {
    o.page = 1;
    o.page_count = '';
}
//==============核心代码=============
var winH = $(window).height(); //页面可视区域高度
//获取传过来的ID的函数
var hid = '';
var aid = '';
var detail=new Object();

Array.prototype.contains = function ( needle ) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}
var scrollHandler = function() {
    // if($('.detailshow').is('aide')) {
    var pageH = $('.question-detail').height();
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
    aid=GetQueryString('aid');
    var params = '?aid=' + aid;
    if (obj.page != '' && obj.page != undefined) {
        params += '&page=' + obj.page;
    }
    $('.que-block').empty();
    $.get('/api/plot/getAnswerList'+params, function(data) {
        o.page_count = data.data.page_count;
        if (data.data.length == undefined) {
            var a=data.data;
            var detailHtml = '<div class="ques-wrap">' +
                '            <span class="icon icon-wen">问</span>' +
                '            <p class="ques-content">'+ a.ask_title +'</p>' +
                '        </div>' +
                '        <div class="que-ops">' +
                '            <span>'+ a.ask_username + '</span>' +
                '            <span>'+ a.ask_time + '</span>' +
                '        </div>';

            $('.que-block').append(detailHtml);
            $('.block-header').html('共有' + data.data.item_count +'个回答');
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
    init();
    ajaxAddList(o);
    $('.btn-wen').click(function(){
        location.href='/subwap/questionSubmit.html?hid='+hid;
    });
    $('.btn-da').click(function(){
        location.href='/subwap/answerSubmit.html?hid='+hid +'&aid='+aid;
    });
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
