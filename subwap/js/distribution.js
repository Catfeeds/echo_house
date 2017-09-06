var hid='';
var title='';
var com_phone='';
$(document).ready(function() {	
	hid=GetQueryString('hid');
	title=GetQueryString('title');
	com_phone=GetQueryString('phone');
	$('#distribution-buliding').html(title);
	$('#distribution-contacter').html(com_phone);
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.coo_words);
    });
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
function shenQing(){
	$.post("/api/plot/addCo",{
		'hid':hid,
		'com_phone':com_phone
	},function(data,status){
		if (data.status == "success") {
                alert("申请成功！");
                location.href = "detail.html?id="+hid;
            } else {
                alert("申请失败！");
            }
	});
}
