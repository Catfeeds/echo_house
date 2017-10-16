var subwappath = './../../../../../../subwap';
$('#upandown').click(function() {
    if($('#upandown').hasClass('up')){
        $('#upandown').attr('src',subwappath+'/img/down.png');
        $('#upandown').removeClass('up');
        $('#upandown').addClass('down');
    }else{
        $('#upandown').attr('src',subwappath+'/img/up.png');
        $('#upandown').removeClass('down');
        $('#upandown').addClass('up');
    }
    $(".panel").slideToggle("slow");
});