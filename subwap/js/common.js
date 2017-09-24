$(document).ready(function(){
	$.get('/api/index/getQfUid',function(data) {
		if(data.status=='error') {
			alert(data.msg);
		}
	});
	$('a[title="站长统计"]').css('display','none');
});