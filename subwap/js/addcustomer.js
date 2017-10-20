$(document).ready(function() {
	$.get('/api/plot/getAcsales',function(data) {
		if(data.status=='success'&&data.data.length>0) {
			var list = data.data;
			var html = '';
			html += '<option value="0">请选择案场销售</option>';
			for (var i = 0; i < list.length; i++) {
				html += '<option value="'+list[i].id+'">'+list[i].name+'</option>';
			}
			$('.report-contacter').append(html);
		}
	});
    $('#form').validate();
});
function sub(){
	var code = $('#cusname').val();
	if(code!=''){
		$.get('/api/plot/checkSub?code='+code+'&acxs='+$('.report-contacter').val(),function(data) {
			if(data.status=='error') {
				alert(data.msg);
			} else {
				alert('客户确认到访成功');
				location.href = 'customerdetail.html?id='+data.data;
			}
		});
	}
}