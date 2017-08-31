//获取传过来的ID的函数
var hid = '';
var detail=new Object();
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
$(document).ready(function(){
	//获取ID
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
		hid = GetQueryString('id');
	}
	//获取数据
	$.get('/api/plot/info?id='+hid, function(data) {
        detail = data.data;
	    $('.detail-top-img-title').append(detail.title+'-'+detail.area+'-'+detail.street);
	    $('.detail-head-price').append(detail.price,detail.unit);
	    $('.detail-head-location').append(detail.address);
	    $('.detail-daikanrules-message').append(detail.dk_rule?detail.dk_rule:'暂无');
		$('.detail-laststate-message').append(detail.news?detail.news:'暂无');
    	if(detail.is_login == '1') {
    		if(detail.pay.length>0){
	    		pay = detail.pay[0];
	    		content = pay['title']?(pay['title'] +'<br>'+ pay['content']):pay['content'];
	    		$('.detail-pricerules-message').append(content);
	    		$('#paynum').html(pay['num']);
	    	}else{
	    		$('.detail-pricerules-message').append('暂无');
	    		$('#paynum').html('0');
	    	}
    	} else {
    		$('.detail-pricerules').css('display','none');
    	}
	    	
    	//楼盘卖点
    	if (detail.sell_point!=''&&detail.sell_point!=undefined) {
    		$('.detail-sailpoint-message').append(detail.sell_point);
    	} else {
			$('.detail-sailpoint').css('display','none');
    	}
    	//插入主力户型
    	
    	for(var i=0;i<detail.hx.length;i++){
    		if(detail.hx[i].size==''||detail.hx[i].size==undefined){
    			detail.hx[i].size="--";
    		}
    		$('.detail-mainstyle-housecontainer ul').append('<li><div class="detail-mainstyle-img"><img style="width: 7.307rem;" src="'+detail.hx[i].image+'"></div><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
    	}
    	//判断能否编辑
    	if(detail.can_edit==0){
    		$('.detail-laststate-edit').css('display','none');
    	}else{
    		$('.detail-laststate-edit').css('display','block');
    	}
    	//顶部图片    	
    	for (var i = 0; i < detail.images.length; i++) {
    		$('.swiper-wrapper').append('<div class="swiper-slide"><img class="detail-head-img" src="'+detail.images[i].url+'"></div>');
    	}
    	var swiper = new Swiper('.detail-head-img-container',{
		    loop: true
		  });
    	//插入查询电话
    	// for (var i = 0; i < detail.phone.length; i++) {
    	// 	$('.telephone ul')append('<li onclick="callConsult(this)"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phone[i]+'</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></li>');
    	// }
    });

});


//打电话
function becomeDuijieren(){
	$.post("/api/plot/addMakert", {
            'hid': hid
        },
        function(data, status) {
            if (data.status == "success") {
                alert("申请成功！");
                location.href = "login.html";
            } else {
                alert("申请失败！");
            }
        }
    );
}
function callConsult(Obj){

}










//点击跳转
$('#paramter').click(function(){
    location.href='/wap/plot/paramter?hid='+hid;
});   
$('#map').click(function(){
    location.href='/wap/plot/map?hid='+hid;
});   
$('#yongjin').click(function(){
    location.href='/wap/plot/pay?hid='+hid;
});   
$('#comment').click(function(){
    location.href='/wap/plot/comment?hid='+hid;
});
$('.detail-laststate-edit').click(function(){
    location.href='publish.html?model='+$(this).data('model')+'&title='+$('.detail-top-img-title').html()+'&hid='+GetQueryString('id');
});
$('.detail-button-phone').click(function(){
	if ($('.telephone-consult').is('.hide')) {
		$('.telephone-consult').removeClass('hide');
		$('.tel-bg').removeClass('hide');
	} else {
		$('.telephone-consult').addClass('hide');
		$('.tel-bg').addClass('hide');
	}
});