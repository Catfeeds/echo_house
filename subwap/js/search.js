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

// //cookie
// function getCookie(c_name){
//     if (document.cookie.length>0){
//         c_start=document.cookie.indexOf(c_name + "=")
//     if (c_start!=-1){ 
//         c_start=c_start + c_name.length+1 
//         c_end=document.cookie.indexOf(";",c_start)
//     if (c_end==-1) c_end=document.cookie.length
//         return unescape(document.cookie.substring(c_start,c_end))
//     } 
//   }
// return ""
// }

// function setCookie(c_name,value,expiredays){
//     var exdate=new Date()
//     exdate.setDate(exdate.getDate()+expiredays)
//     document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
// }

// function checkCookie()
//     {
//     username=getCookie('username')
//     if (username!=null && username!="")
//       {alert('Welcome again '+username+'!')}
//     else 
//       {
//       username=prompt('Please enter your name:',"")
//       if (username!=null && username!="")
//         {
//         setCookie('username',username,365)
//         }
//       }
// }


document.onkeydown=function(){   
        $('#search-history-ul').remove();
        var kw= $('.list-search-frame-text').val();
         $.get('/api/plot/ajaxSearch?kw='+kw, function(data) {
         	for (var i=0;i<data.length;i++) {
	        	house_name = data.title;
	        	house_id = data.id;
	        	$('#search-history-ul').append('<li data-id="'+house_id+'">'+house_name+'</li>');
        	} 
    	});
        
}