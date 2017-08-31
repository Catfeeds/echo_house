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
	    $('.detail-top-img-title').append(detail.title);
	    $('.detail-head-price').append(detail.price,detail.unit);
	    $('.detail-head-location').append(detail.area,detail.street,detail.address);
    	$('.detail-laststate-message').append(detail.news);
    	if(detail.pay!=0&&detail.pay!=undefined){
    		$('.detail-pricerules-message').append(detail.pay);
    	}else{
    		$('.detail-pricerules').css('display','none');
    	}
    	//楼盘卖点
    	if (detail.sell_point!=''&&detail.sell_point!=undefined) {
    		$('.detail-sailpoint-message').append(detail.sell_point);
    	} else {
			$('.detail-sailpoint').css('display','none');
    	}
    	//插入主力户型
    	$('.detail-head-img').attr('src',detail.images[0].url)
    	for(var i=0;i<detail.hx.length;i++){
    		$('.detail-mainstyle-housecontainer ul').append('<li><div class="detail-mainstyle-img"><img style="width: 7.307rem;" src="'+detail.hx[i].image+'"></div><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-type">('+detail.hx[i].status+')</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-shu"> | </div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-peice">56万起</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
    	}
    	//判断能否编辑
    	if(detail.can_edit==0){
    		$('.detail-laststate-edit').css('display','none');
    	}else{
    		$('.detail-laststate-edit').css('display','block');
    	}
    	//插入查询电话
    	// for (var i = 0; i < detail.phone.length; i++) {
    	// 	$('.telephone ul')append('<li onclick="callConsult(this)"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phone[i]+'</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></li>');
    	// }
    });

});





//打电话函数
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