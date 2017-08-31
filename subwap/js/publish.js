var o = new Object();
o.model = '';
o.hid = '';
o.content = '';
$(document).ready(function() {
	if(GetQueryString('model')!=null) {
		o.model = GetQueryString('model');
	}
	if(GetQueryString('hid')!=null) {
		o.hid = GetQueryString('hid');
	}
	$('.publish-head-title').html(GetQueryString('title'));
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
$('.publish-submit').click(function() {
	o.content = $('.publish-write').val();
	if(o.content == '' ) {
		alert('字段不能为空');
	}else
		$.post('/api/plot/submit',o,function(data) {
			alert('保存成功');
			location.href = 'detail.html?id='+o.hid;
		});
});