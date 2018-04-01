var o = new Object();
function init() {
    o.page = 1;
    o.page_count = '';
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
    var pageH = $('.question-list').height();
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

    $.get('/api/plot/getAskList'+params, function(data) {
        o.page_count = data.data.page_count;
        if (data.data.length == undefined) {
            var a=data.data.list;
            var Html = '';
            for(var i = 0; i < a.length; i++){
                if(a[i].first_answer.note){
                    var ansEle =  '</div>' +
                        '            <div class="question-info-da">' +
                        '                <span class="icon icon-da">答</span>' +
                        '                <span>'+ a[i].first_answer.note +'</span>' +
                        '            </div>'+
                        '           <div class="que-ops">' +
                        '                <span class="answ-entry">' +
                        '                    查看'+ a[i].answers_count +'个回答' +
                        '                    <img class="lookup-img" src="./img/).png">' +
                        '                </span>' +
                        '                <span class="time">'+ a[i].first_answer.time +'</span>' +
                        '            </div>' ;
                }else{
                    var ansEle = '<div class="question-info-da">正在为您寻找答案</div>'+
                    '           <div class="que-ops">' +
                    '                <span class="answ-entry">我要回答问题'+
                    '                    <img class="lookup-img" src="./img/).png">' +
                    '                </span>' +
                    '            </div>' ;
                }
                var Ele =  '<a class="question-info" href="/subwap/questionDetail.html?hid='+ hid + '&aid='+a[i].id +'">' +
                    '            <div class="question-info-wen">' +
                    '                <span class="icon icon-wen">问</span>' +
                    '                <span>'+ a[i].title +'</span>' +
                    '            </div>' + ansEle +
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
    init();
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
