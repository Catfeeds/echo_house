var loginimg = '';
$(document).ready(function(){
	$.get('/api/config/index',function(data){
		if(data.data.login_img != undefined) {
			loginimg = data.data.login_img;
		}
	});
});