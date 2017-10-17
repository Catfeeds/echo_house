$('#collect').click(function() {
	if ($('#collect').hasClass('save')) {
		$('#collect').removeClass('save');
		$('#collect').addClass('notsave');
		$('#collect').attr('src','./img/notsave.png');
	} else {
		$('#collect').removeClass('notsave');
		$('#collect').addClass('save');
		$('#collect').attr('src','./img/save.png');
	}
});