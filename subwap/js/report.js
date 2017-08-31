var o = new Object();
o.hid = '';
o.name = '';
o.time = '';
o.sex = '';
o.note = '';
o.is_only_sub = '';
$(document).ready(function() {
	if(GetQueryString('hid')!=null) {
		o.hid = GetQueryString('hid');
	}
	$('#plottitle').html(GetQueryString('title'));
});
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}