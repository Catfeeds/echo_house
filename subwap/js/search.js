$('.delete-img').click(function(){
    $('#search-history-ul').empty();
});
$('.delete-text').click(function(){
    $('#search-history-ul').empty();
});
//search请求
document.onkeydown=function(){
    if (event.keyCode == 13){ 
        var obj_search= $('.list-search-frame-text').val();
        if(obj_search != '') {
            location.href='list.html?kw='+obj_search;
        }   
    }
}

document.onkeydown=function(){   
        $('#search-history-ul').empty();
        var kw= $('.list-search-frame-text').val();
         $.get('/api/plot/ajaxSearch?kw='+kw, function(data) {
            var data = data.data;
         	for (var i=0;i<data.length;i++) {
                // alert(data[i].title);
	        	house_name = data[i].title;
	        	house_id = data[i].id;
	        	$('#search-history-ul').append('<li onclick="todetail(this)" data-id="'+house_id+'">'+house_name+'</li>');
        	} 
    	});
        
}
function todetail(obj) {
    location.href = 'detail.html?id='+$(obj).data('id');
}
function tolist(obj) {
    location.href = 'list.html?kw='+$(obj).data('id');
}

function checkfm() {
    if($('.list-search-frame-text').val()=='') {
        return false;
    } else {
        return true;
    }
}
$(document).ready(function() {
    $.get('/api/plot/getSearchCoo', function(data) {
            var data = data.data;
            for (var i=0;i<data.length;i++) {
                $('#search-history-ul').append('<li onclick="tolist(this)" data-id="'+data[i]+'">'+data[i]+'</li>');
            } 
        });
});
function delCoo() {
    $.get('/api/plot/delSearchCoo', function(data) {
        $('#search-history-ul').empty();
    });
}