//获取传过来的ID的函数
var hid = '';
var title='';
var phone='';
var areaid='';
var url='';
var our_uids = '';
var thisphone = '';
var is_user = false;
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
            is_user = true;
            our_uids = data.data.our_uids;
            thisphone = data.data.user.phone;
        }
         //底部按钮变化
        if (detail.is_contact_only==1) {
            $('.detail-buttom0').css('display','none');
            $('.detail-buttom1').css('display','block');
        }
        else if (detail.is_contact_only==2 || is_user==false){
            $('.detail-buttom0').css('display','none');
            $('.detail-buttom2').css('display','block');
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
        areaid = detail.areaid;
        sameArea();
        $('title').html(detail.title);
        $.get('/api/wx/zone?imgUrl='+detail.images[0]['url']+'&title='+detail.wx_share_title+'&link='+window.location.href+'&desc='+detail.sell_point.substring(0,30),function(data) {
            $('body').append(data);
        });
        $('#subit').attr('href','report.html?hid='+detail.id+'&title='+detail.title);
	    $('.detail-top-img-title').append(detail.title+'-'+detail.area+'-'+detail.street);
        area=detail.area;
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
        if (detail.news!=''&&detail.news!=undefined) {
            $('.detail-laststate-message').append(detail.news);
        }else{
            $('.detail-laststate-message').append('暂无');
            $('#laststate-img').css('display','none');
        }
    	if(detail.is_login == '1') {
            if(detail.pay.length<=1) {
                $('#fangannum').css('display','none');
            }
    		if(detail.pay.length>0){
                pay = detail.pay[0];
                content = pay['title']?(pay['title'] +'<br>'+ pay['content']):pay['content'];
                $('.detail-pricerules-message').append(content);
                if(detail.pay.length<=1) {
                    $('#fangannum').css('display','none');
                } else {
                    
                    $('#paynum').html(pay['num']);
                }
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
        		  $('#mainstyle ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
        	    else
                    $('#mainstyle ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room"></div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
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
       
    	// 插入查询电话
    	if(detail.phones.length > 0) {
    		for (var i = 0; i < detail.phones.length; i++) {
                var word = '';
                // console.log(detail.phones[i].indexOf(detail.phone));
    	    	if (detail.phone && detail.phones[i].indexOf(detail.phone)>-1) {
                    tmp  = detail.phones[i];
                    phone=detail.phone;
                    icon = "fuzeuser.png";
                    // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/fuzeuser.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                } else {
                    if(detail.owner_phone && detail.phones[i].indexOf(detail.owner_phone)>-1) {
                        icon = "fuzeuser.png";
                        word = '<div class="fbuser">发布人</div>'
                    } else {
                        icon = "user.png";
                    }
                    // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                }
                // debugger;
                $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/'+icon+'"><div class="consult-text">'+detail.phones[i]+word+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
            }
    	}
    	
    });
    // setInterval("console.log($('.detail-sailpoint-message').height())",5);
    // if ($('.detail-sailpoint-message').height()<3rem) {
    //     $('.maidian-on-off').css('display','none');
    // } 
});
function sameArea(){
    //同区域楼盘
    $.get('/api/plot/list?area='+areaid+'&limit=6',function(data) {
        samearea=data.data.list;
        if(samearea.length>0){
        $('.detail-samearea').css('display','block');
        for(var i=0;i<samearea.length;i++){
            if(samearea[i].size==''||samearea[i].size==undefined){
                samearea[i].size="--";
            }
            if (hid!=samearea[i].id) {
            if(samearea[i].price!=''&&samearea[i].price!=undefined)
              {$('#samearea ul').append('<li onclick="turnDetail('+samearea[i].id+')"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+samearea[i].image+'"></div><div class="detail-mainstyle-style">'+samearea[i].title+'</div><div class="detail-mainstyle-area">'+samearea[i].wylx+'</div><div class="detail-samearea-price">'+samearea[i].price+samearea[i].unit+'</div></li>');}
            else
                {$('#samearea ul').append('<li onclick="turnDetail('+samearea[i].id+')"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+samearea[i].image+'"></div><div class="detail-mainstyle-style">'+samearea[i].title+'</div><div class="detail-mainstyle-area">'+samearea[i].wylx+'</div><div class="detail-mainstyle-room"></div></li>');}
            }
        }
    }else{
        $('.detail-samearea').css('display','none');
    }
    });   
};
//同区域跳转
function turnDetail(obj){
    location.href="detail.html?id="+obj;
}
//申请成为对接人
function becomeDuijieren(){
    $.get('/api/plot/checkIsMarket?hid='+hid,function(data) {
        if(data.status=='success') {
            location.href="duijieren.html?hid="+hid+'&title='+title;
        } else {
            alert(data.msg);
        }
    })
    // location.href="duijieren.html?hid="+hid;
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
//举报
$('.detail-mail-container').click(function(){
    $('.tip-off').css('display','block');
});
$('.tip-off-shutdown').click(function(){
    $('.tip-off').css('display','none');
});
var reason='';
$('.tip-off-select-window li').click(function(){
    $('.tip-off-select-window li').css('color','#000000');
    $('.tip-off-select-window li img').addClass('select-hide');
    $(this).css('color','#00B7F0');
    $(this).children().removeClass('select-hide');
    if($(this).index()==4){
        $('.tip-off-detail').css('display','block');
        $('.tip-off-detail').focus();
        reason='';
        $('.tip-off-select-window ul').scrollTo(0,0);
    }else{
        $('.tip-off-detail').css('display','none');
        reason=$(this).find('div').html();
    }
    
});

$('.tip-off-tijiao').click(function(){
    reason=reason==''?$('.tip-off-detail').val():reason;
    $.post('/api/plot/addReport',{
        'hid':hid,
        'reason':reason
    },function(data){
        if (data.status=='success') {
            alert("举报成功");
        } else {
            alert(data.msg);
        }
    });
    $('.tip-off').css('display','none');
});



