$(document).ready(function() {
    $('#form').validate();
});
function sub(){
	var code = $('#cusname').val();
	$.get('/api/plot/checkSub?code='+code,function(data) {
		if(data.status=='error') {
			alert(data.msg);
		} else {
			alert('添加成功');
			location.href = 'addcustomer.html';
		}
	});
}
