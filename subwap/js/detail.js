var hid = '';
$('#paramter').click(function(){
    location.href='/wap/plot/paramter?hid='+hid;
});   
$('#map').click(function(){
    location.href='/wap/plot/map?hid='+hid;
});   
$('#yongjin').click(function(){
    location.href='/wap/plot/pay?hid='+hid;
});   
$('#comment').click(function(){
    location.href='/wap/plot/comment?hid='+hid;
});   
$(document).ready(function(){
	if(GetQueryString('id')!='') {
		hid = GetQueryString('id');
	}
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}