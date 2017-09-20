$(document).ready(function() {
    $('#form').validate();
});
function sub(){
	var code = $('#cusname').val();
	if(code!=''){
		$.get('/api/plot/checkSub?code='+code,function(data) {
			if(data.status=='error') {
				alert(data.msg);
			} else {
				alert('确认到访客户');
				location.href = 'customerlist.html';
			}
		});
	}
}
