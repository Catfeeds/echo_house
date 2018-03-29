var hid='';

$(document).ready(function() {
	hid=GetQueryString('hid');
	console.log(hid)
    // 点评列表
    $.get('/api/plot/getDpList?hid='+hid,function(data) {
        var a=data.data.list;
        console.log(a)
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
    });
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
