var hid='';
var title='';
var com_phone='';
var a='';
$(document).ready(function() {	
	hid=GetQueryString('hid');
	title=GetQueryString('title');
	$('#distribution-buliding').html(title);
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.coo_words);
    });
    $.get('/api/plot/getPhones?hid='+hid,function(data) {
        a=data.data;
        for (var i = 0; i < a.length; i++) {
          $('.distribution-contacter').append('<option value="'+a[i].key+'">'+a[i].value+'</option>');
        }
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
		'com_phone':$('.distribution-contacter').val()
	},function(data,status){
		if (data.status == "success") {
                alert("申请成功，项目负责人将会尽快与您联系");
                location.href = "detail.html?id="+hid;
            } else {
                alert(data.msg);
            }
	});
}
