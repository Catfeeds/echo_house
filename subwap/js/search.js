var tid = null;
$(document).ready(function() {
    //搜索历史
    $.get('/api/plot/getSearchCoo', function(data) {
            var data = data.data;
            for (var i=0;i<data.length;i++) {
                $('#search-history-ul').append('<li onclick="tolist(this)" data-id="'+data[i]+'">'+data[i]+'</li>');
            } 
        });
});
<<<<<<< HEAD
//搜索
$("#search-text").keyup(function () {
    $('#search-ul').empty();
    if ($('#search-text').val()!='') {
        $('#history-container').css('display','none');
        $('#search-container').css('display','block');
    } else {
        $('#search-container').css('display','none');
        $('#history-container').css('display','block');
    }     
        clearTimeout(tid);
        tid = setTimeout(function(){
            var kw= $('.list-search-frame-text').val();
    if(kw.length>=2) {
        $.get('/api/plot/ajaxSearch?kw='+kw, function(data) {
            var data = data.data;
            for (var i=0;i<data.length;i++) {
                house_name = data[i].title;
                house_id = data[i].id;
                $('#search-ul').append('<li onclick="todetail(this)" data-id="'+house_id+'">'+house_name+'</li>');
=======
var tid = null;
$("#search-text").keyup(function () {

        $('#search-history-ul').empty();

        clearTimeout(tid);
        tid = setTimeout(function(){
            var kw= $('.list-search-frame-text').val();
    if(kw.length>=4) {
        $.get('/api/plot/ajaxSearch?kw='+kw, function(data) {
            var data = data.data;
            for (var i=0;i<data.length;i++) {
                // alert(data[i].title);
                house_name = data[i].title;
                house_id = data[i].id;
                $('#search-history-ul').append('<li onclick="todetail(this)" data-id="'+house_id+'">'+house_name+'</li>');
>>>>>>> 3a07a4497492037c2565151b786b7a4832c1ae5e
            } 
        });
    }
        },500);         
    });
<<<<<<< HEAD
//清空历史搜索记录
function delCoo() {
    $.get('/api/plot/delSearchCoo', function(data) {
        $('#search-history-ul').empty();
    });
}
//跳转到详情页
=======
function searchit(){
    
        
}

// document.onkeydown=function(){   
//     $('#search-history-ul').empty();

//         var kw= $('.list-search-frame-text').val();
//         if(kw.length>=4) {
//             $.get('/api/plot/ajaxSearch?kw='+kw, function(data) {
//                 var data = data.data;
//                 for (var i=0;i<data.length;i++) {
//                     // alert(data[i].title);
//                     house_name = data[i].title;
//                     house_id = data[i].id;
//                     $('#search-history-ul').append('<li onclick="todetail(this)" data-id="'+house_id+'">'+house_name+'</li>');
//                 } 
//             });
//         }
             
        
        
// }
>>>>>>> 3a07a4497492037c2565151b786b7a4832c1ae5e
function todetail(obj) {
    location.href = 'detail.html?id='+$(obj).data('id');
}
//跳转到列表页
function tolist(obj) {
    location.href = 'list.html?kw='+$(obj).data('id');
}
//enter跳转列表页筛选
function checkfm() {
    if($('.list-search-frame-text').val()=='') {
        return false;
    } else {
        return true;
    }
}