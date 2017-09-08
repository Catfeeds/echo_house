//获取传过来的ID的函数
var hid = '';
var title='';
var phone='';
var url='';
var thisphone = '';
var detail=new Object();
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
$(document).ready(function(){
    var clipboard = new Clipboard('.copy-weixin');  
    $.get('/api/config/index',function(data) {
        if(data.data.is_user == true) {
            thisphone = data.data.user.phone;
        }
    });
	//获取ID
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
		hid = GetQueryString('id');
	}
    // if(GetQueryString('phone')!=''&&GetQueryString('phone')!=undefined) {
    //     phone = GetQueryString('phone');
    // }
	//获取数据
	$.get('/api/plot/info?id='+hid+'&phone='+phone, function(data) {
        detail = data.data;
        $('title').html(detail.title);
        if(detail.is_show_add==0||detail.is_show_add=='0') {
            $('#showadd').remove();
        }
        $.get('/api/wx/zone?imgUrl='+detail.images[0]['url']+'&title='+detail.wx_share_title+'&link='+window.location.href+'&desc='+detail.sell_point,function(data) {
            $('body').append(data);
        });
        $('#subit').attr('href','report.html?hid='+detail.id+'&title='+detail.title);
	    $('.detail-top-img-title').append(detail.title+'-'+detail.area+'-'+detail.street);
        title=detail.title;
	    $('.detail-head-price').append(detail.price,detail.unit);
	    // 顶部价格下面的标签
        if (detail.tags.length<1) {
            $('.head-price-tags').css('display','none');
        }
        for (var i = 0; i < detail.tags.length; i++) {
            if (i%3==1) {
                // $('#showadd').css('display','none');
                $('.head-price-tags ul').append('<li class="color1">'+detail.tags[i]+'</li>'); 
            }else if(i%3==2){
                // $('#showadd').css('display','none');
                $('.head-price-tags ul').append('<li class="color2">'+detail.tags[i]+'</li>'); 
            }else{
                $('.head-price-tags ul').append('<li class="color3">'+detail.tags[i]+'</li>');  
            }
        }
        $('#maptext').append(detail.address);
        $('#zdtext').append(detail.zd_company.name);
        $('#zd').attr('data-id',detail.zd_company.id);
        $('#zd').attr('data-name',detail.zd_company.name);
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
    	if(detail.hx!=''&&detail.hx!=undefined){
            $('.detail-mainstyle').css('display','block');
        	for(var i=0;i<detail.hx.length;i++){
        		if(detail.hx[i].size==''||detail.hx[i].size==undefined){
        			detail.hx[i].size="--";
        		}
                if(detail.hx[i].bedroom>0)
        		  $('.detail-mainstyle-housecontainer ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
        	    else
                    $('.detail-mainstyle-housecontainer ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room"></div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
            }
        }else{
            $('.detail-mainstyle').css('display','none');
        }
    	//判断能否编辑
    	if(detail.can_edit==0){
    		$('.detail-laststate-edit').css('display','none');
    	}else{
    		$('.detail-laststate-edit').css('display','block');
    	}
        $('.mzsm').html(detail.mzsm);
    	//顶部图片  
        if(detail.images!=''&&detail.images!=undefined){  	
        	for (var i = 0; i < detail.images.length; i++) {
                // $('.detail-head-img-examplepic').html(detail.images[i].type);
        		$('.swiper-wrapper').append('<div class="swiper-slide"><a href="'+detail.images[i].url+'"><img data-type="'+detail.images[i].type+'" class="detail-head-img" src="'+detail.images[i].url+'"></a></div>');
        	}
        }
    	var swiper = new Swiper('.detail-head-img-container',{
		    loop: true,
            onSlideChangeEnd:function() {
                $('.detail-head-img-examplepic').html($('.swiper-slide-active').find('img').data('type'));
            }
		  });
        //底部按钮变化
        if (detail.is_contact_only==1) {
            $('.detail-buttom0').css('display','none');
            $('.detail-buttom1').css('display','block');
        }
        if (detail.is_contact_only==2){
            $('.detail-buttom0').css('display','none');
            $('.detail-buttom2').css('display','block');
        }
    	// 插入查询电话
    	if(detail.phones.length > 0) {
    		for (var i = 0; i < detail.phones.length; i++) {
    	    	if (detail.phones[i].indexOf(detail.phone)>-1) {
                    tmp  = detail.phones[i];
                    phone=detail.phone;
                    $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/fuzeuser.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                } else {
                    $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                }
            }
    	}
    	
    });
    // setInterval("console.log($('.detail-sailpoint-message').height())",5);
    // if ($('.detail-sailpoint-message').height()<3rem) {
    //     $('.maidian-on-off').css('display','none');
    // } 

});


//申请成为对接人
// var qftype=new Object();
// qftype.title='申请对接人费用';
// qftype.cover='';
// qftype.num=1;
// qftype.gold_cost=0;
// qftype.cash_cost=0.1;
// var qfarray=new Array();
// qfarray[0]=qftype;
// var item=JSON.stringify(qfarray);
// var address=new Object();
// address.name='';
// address.mobile='';
// address.address='';
function becomeDuijieren(){
    // QFH5.createOrder(10001,item,0,address,12,function(state,data){
    //     alert(state);
    // });




	$.post("/api/plot/addMakert", {
            'hid': hid
        },
        function(data, status) {
            if (data.status == "success") {
                alert("申请成功！");
            } else {
                alert(data.msg);
            }
        }
    );
}

//分享页面
function share(){
    url=window.location.href+'_'+thisphone+'&id='+hid;
    location.href='qrcode.html?url='+url;
}


//展开折叠
$('.maidian-on-off').click(function(){
    if ($('.maidian-on-off').is('.off')) {
        $('.maidian-on-off').removeClass('off');
        $('.maidian-on-off').addClass('on');
        $('.detail-sailpoint-message').css('max-height','100rem');
        $('.maidian-on-off').empty();
        $('.maidian-on-off').append('收起');
    } else {
        $('.maidian-on-off').removeClass('on');
        $('.maidian-on-off').addClass('off');
        $('.detail-sailpoint-message').css('max-height','3rem');
        $('.maidian-on-off').empty();
        $('.maidian-on-off').append('展开更多');
    }
});



//点击跳转
$('#paramter').click(function(){
    location.href='/wap/plot/paramter?hid='+hid;
});   
$('#map').click(function(){
    $.get('/api/config/getP?lat='+detail.map_lat+'&lng='+detail.map_lng,function(data) {
        location.href='https://map.baidu.com/mobile/webapp/place/detail/qt=inf&uid='+data.data+'/vt=map';
    });
    // 
});   
$('#yongjin').click(function(){
    location.href='/wap/plot/pay?hid='+hid;
});   
$('#comment').click(function(){
    location.href='/wap/plot/comment?hid='+hid;
});
$('.detail-button-distribution').click(function(){
    location.href='distribution.html?hid='+hid+'&title='+title;
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
$('.detail-buttom1').click(function(){
    if ($('.telephone-consult').is('.hide')) {
        $('.telephone-consult').removeClass('hide');
        $('.tel-bg').removeClass('hide');
    } else {
        $('.telephone-consult').addClass('hide');
        $('.tel-bg').addClass('hide');
    }
});
$('.tel-bg').click(function(){
    $('.telephone-consult').addClass('hide');
    $('.tel-bg').addClass('hide');
});

function copyUrl2() {
    alert('已成功复制手机号，请至微信搜索添加');
}

function show_zd_list(obj) {
    location.href = 'list.html?zd_company='+$(obj).data('id')+'&company='+$(obj).data('name');
}


