$('#upandown').click(function() {
	if($('#upandown').hasClass('up')){
		$('#upandown').attr('src','./img/down.png');
		$('#upandown').removeClass('up');
		$('#upandown').addClass('down');
	}else{
		$('#upandown').attr('src','./img/up.png');
		$('#upandown').removeClass('down');
		$('#upandown').addClass('up');
	}
	$(".panel").slideToggle("slow");
});