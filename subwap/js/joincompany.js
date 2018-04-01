$(document).ready(function() {
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.regis_words);
    });
    // $('.container-big').css('display','none');
});
function sub(){
	var code = $('#cusname').val();
	if(code!=''){
		$.get('/api/plot/joinCompany?code='+code,function(data) {
			if(data.status=='error') {
				alert(data.msg);
			} else {
				alert(data.msg);
				location.href = '/wap/my/index';
				history.back();
			}
		});
	} else {
		alert('门店码不能为空');
	}
}
