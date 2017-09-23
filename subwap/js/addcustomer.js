$(document).ready(function() {
    $('#form').validate();
});
function sub(){
	var code = $('#cusname').val();
	$.get('/api/config/index',function(data) {});
	if(code!=''){
		$.get('/api/plot/checkSub?code='+code,function(data) {
			if(data.status=='error') {
				alert(data.msg);
			} else {
				alert('客户确认到访成功');
				location.href = 'customerdetail.html?id='+data.data;
			}
		});
	}
}
