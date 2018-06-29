//获取传过来的ID的函数
var hid = '';
var title='';
var phone='';
var nowphone = '';
var areaid='';
var streetid='';
var is_jy = 0;
var url='';
var our_uids = '';
var thisphone = '';
var is_user = false;
var detail=new Object();
var topimglist = new Array;
var hximglist = new Array;
Array.prototype.contains = function ( needle ) {
  for (i in this) {
    if (this[i] == needle) return true;
  }
  return false;
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
function checkUser() {
    $.get('/api/index/getQfUid',function(data) {
                if(data.status=='error') {
                    if(data.msg=='请绑定经纪圈手机号') {
                         QFH5.jumpBindMobile(function(state,data){//即使用户已绑定手机也会显示此界面，此时是修改绑定，调用前请先判断是否已绑定
                          if(state==1){
                              //绑定成功
                              location.href = 'list.html';
                          }
                      });
                    } else {
                        if(isWeiXin()) {
                            alert('请下载经纪圈APP查看项目详情');
                            location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.zj58.forum';
                        }else
                            alert('登录成功后请关闭本页面重新进入');
                        QFH5.jumpLogin(function(state,data){
                          //未登陆状态跳登陆会刷新页面，无回调
                          //已登陆状态跳登陆会回调通知已登录
                          //用户取消登陆无回调
                          if(state==2){
                              alert("您已登陆");
                          }
                      })
                    }
                        
                } else {
                    if(is_jy==1) {
                        alert('您的账户未通过审核或已禁用，请联系客服');
                    }else {
                        alert('请前往认证账号');
                        location.href = 'register.html?phone='+data.data.phone;
                    }
                }
            });
}
$(document).ready(function(){
    // 获取屏幕的高-遮罩层
    var bodyHeight = $(document.body).height();
    $('.background').css({'height':bodyHeight +300});
    var clipboard = new Clipboard('.copy-weixin');  
    // var clipboard1 = new Clipboard('.fuzhiwenan');  
    // 复制文案
    // data-clipboard-text="'+detail.phonesnum[i]+'" clas
    if(typeof QFH5 != "undefined") {
         QFH5.getUserInfo(function(state,data){
           if(state==1){
                nowphone = data.phone;
              } 
        });
    }
       
    $('.detail-laststate-time').empty();
    $('.detail-laststate-num').empty();
    $('#laststate-img').css('display','none');
    $('#comment-img').css('display','none');

    $.get('/api/config/index',function(data) {
        is_jy = data.data.is_jy;
        if(data.data.is_user == true) {
            is_user = true;
            our_uids = data.data.our_uids;
            thisphone = data.data.user.phone;
        }$.get('/api/plot/info?id='+hid+'&phone='+phone, function(data) {
            detail = data.data;
            areaid = detail.areaid;
            streetid = detail.streetid;
            sameArea();
            $('title').html(detail.title);
            if(typeof QFH5 != "undefined")
                QFH5.setTitle(detail.title);
            if(detail.is_alert==1) {
                $('.detail-top-img-alert').css('display','block');
            }
            if(detail.is_unshow==1||detail.is_unshow=="1") {
                $('.detail-buttom0').css('display','none');
                $('.detail-top-img-alert1').html('该项目已'+detail.sale_status);
                $('.detail-head-status').html('['+detail.sale_status+']');
                $('.detail-top-img-alert1').css('display','block');
            }
            if(detail.is_save==1) {
                $('#save').attr('css','save');
                $('#save').attr('src','./img/save.png');
                $('.detail-button-save-text').html('已关注');
            } else {
                $('#save').attr('css','notsave');
                $('#save').attr('src','./img/notsave.png');
                $('.detail-button-save-text').html('关注');
            }
            //底部按钮变化
            if (detail.is_contact_only==1) {
                $('.detail-buttom0').css('display','none');
                $('.detail-buttom1').css('display','block');
            }
            else if (detail.is_contact_only==2){
                $('.detail-buttom0').css('display','none');
                $('.detail-buttom2').css('display','block');
            }
            $.get('/api/wx/zone?imgUrl='+detail.images[0]['url']+'&title='+detail.wx_share_title+'&link='+window.location.href+'&desc='+detail.sell_point.substring(0,30),function(data) {
                $('body').append(data);
            });
            $('.detail-top-img-title').append(detail.title);
            $('.detail-top-img-address').append('[ '+detail.street+' ]');
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
                $('.detail-laststate-time').append(detail.news_time);
                $('.detail-laststate-num').append('(' + detail.new_num + ')');
                $('#laststate-img').css('display','block');
            }else{
                $('.detail-laststate-message').append('暂无');
            }
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
            // if(detail.is_login == '1') {
            //     if(detail.pay.length<=1) {
            //         $('#fangannum').css('display','none');
            //     }
            //     if(detail.pay.length>0){
            //         pay = detail.pay[0];
            //         content = pay['title']?(pay['title'] +'<br>'+ pay['content']):pay['content'];
            //         $('.detail-pricerules-message').append(content);
            //         if(detail.pay.length<=1) {
            //             $('#fangannum').css('display','none');
            //         } else {
                        
            //             $('#paynum').html(pay['num']);
            //         }
            //     }else{
            //         $('.detail-pricerules-message').append('暂无');
            //         $('#paynum').html('0');
            //     }
            // } else {
            //     $('.detail-pricerules').css('display','none');
            // }
                
            //楼盘卖点
            if (detail.sell_point!=''&&detail.sell_point!=undefined) {
                $('.detail-sailpoint-message').append(detail.sell_point);
            } else {
                $('.detail-sailpoint').css('display','none');
            }
            $('.fuzhiwenan').attr('data-clipboard-text',detail.sell_point_des);
            //插入主力户型
            if(detail.hx!=''&&detail.hx!=undefined){    
                for (var i = 0; i < detail.hx.length; i++) {
                    var tmpi = detail.hx[i].image;
                    if(tmpi.indexOf('?')>-1) {
                        var tmparr = tmpi.split('?');
                        hximglist.push(tmparr[0]+'?imageslim');
                    } else
                        hximglist.push(detail.hx[i].image);
                }
            }
            if(detail.hx!=''&&detail.hx!=undefined){
                $('.detail-mainstyle').css('display','block');
                for(var i=0;i<detail.hx.length;i++){
                    if(detail.hx[i].size==''||detail.hx[i].size==undefined){
                        detail.hx[i].size="--";
                    }
                    if(detail.hx[i].bedroom>0)
                      $('#mainstyle ul').append('<li><a onclick="showQfImgHx('+i+')"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
                    else
                        $('#mainstyle ul').append('<li><a onclick="showQfImgHx('+i+')"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room"></div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
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
                    topimglist.push(detail.images[i].content);
                }
            }
            if(detail.images!=''&&detail.images!=undefined){    
                for (var i = 0; i < detail.images.length; i++) {
                    // $('.detail-head-img-examplepic').html(detail.images[i].type);
                    $('.swiper-wrapper').append('<div class="swiper-slide"><a onclick="showQfImgTop('+i+')"><img data-type="'+detail.images[i].type+'" class="detail-head-img" src="'+detail.images[i].url+'"></a></div>');
                }
            }
            
            var swiper = new Swiper('.detail-head-img-container',{
                loop: true,
                onSlideChangeEnd:function() {
                    $('.detail-head-img-index').html($('.swiper-slide-active').data('swiper-slide-index')+1 +'/' + detail.images.length);
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
                        icon = "fbusernew.png";
                        // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/fuzeuser.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                    } else {
                        // console.log(detail.ff_phones.contains(detail.phonesnum[i]));
                        if(detail.owner_phone && detail.phones[i].indexOf(detail.owner_phone)>-1) {
                            icon = "fbusernew.png";
                            word = '<div class="fbuser">发布人</div>'
                        } else if(detail.ff_phones.length>0 && detail.ff_phones.contains(detail.phonesnum[i])) {
                            icon = "ffusernew.png";
                        } else {
                            icon = "usernew.png";
                        }
                        // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                    }
                    // debugger;
                    $('.telephone-consult ul').append('<li ><a onclick="makeCall(this)" href="tel:'+detail.phonesnum[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/'+icon+'"><div class="consult-text">'+detail.phones[i]+word+'</div></a>'+(detail.qfuidsarr[i]!=""?'<div onclick="setChat(this)" data-text="'+detail.qfuidsarr[i]+'" class="copy-weixin">在线聊天</div>':'')+'<a onclick="makeCall(this)" href="tel:'+detail.phonesnum[i]+'"><img class="consult-tel-img" src="./img/tel-green.png"></div></a><div class="line"></div></li>');
                }
            }

            // 用户点评
            if(detail.dps.length > 0){
                var dpsHtml = '';
                for(var i = 0; i < detail.dps.length; i++){
                    var dpsEle = '<div class="detail-comment-message">' +
                        '            <img src="' + detail.dps[i].image + '" />' +
                        '            <div class="detail-comment-info">' +
                        '                <span class="username">'+ detail.dps[i].name +'</span>' +
                        '                <div class="usercontent">' + detail.dps[i].note +
                        '                </div>' +
                        '            </div>' +
                        '        </div>';
                    dpsHtml = dpsHtml + dpsEle;
                }
                $('.detail-comment-message-container').append(dpsHtml);
                $('#comment-img').css('display','block');
                $('.detail-comment-num').html('(' + detail.dp_num + ')')
            }else{
                $('.detail-comment-message-container').append('<div class="detail-dp-message">暂无</div>');
            }

            // 用户问答
            if(detail.asks.length > 0){
                var askHtml = '';
                for(var i = 0; i < detail.asks.length; i++){
                    var aksEle = '<div class="detail-question-info">' +
                        '            <span class="icon icon-wen">问</span>' +
                        '            <span class="detail-question-info-title" >'+ detail.asks[i].title +'</span>'+
                        '            <span class="detail-question-info-num">' +
                        '                <span>'+ detail.asks[i].answers_count +'</span>个回答' +
                        '            </span>' +
                        '        </div>';
                    if(detail.asks[i].first_answer.note){
                        var ansEle =
                            '<div class="detail-question-info">' +
                            '            <span class="icon icon-da">答</span>' +
                            '            <span>'+ detail.asks[i].first_answer.note +'</span>' +
                            '        </div>';
                    }else{
                        var ansEle = '';
                    }
                    askHtml = askHtml + aksEle + ansEle;
                }
                $('.detail-question-container').append(askHtml);
                $('#question-img').css('display','block');
                $('.detail-question-num').html('(' + detail.ask_num + ')')
            }else{
                $('.detail-question-container').append('<div class="detail-ask-message">暂无</div>');
            }
            var WxMiniProgram = {
              'wxUserName':'gh_e96ba07a8511',//小程序原始id
              'wxPath':'pages/house_detail/house_detail?id='+detail.id, //要打开的小程序页面路径
              'title':detail.title,//分享小程序的标题
              'imageUrl':detail.images[0].url,//分享小程序的封面图
              'url': '',
              'share_model': 0 //0:正式版；1：开发版；2：体验版
            };
            console.log(WxMiniProgram);
            if(typeof QFH5 != 'undefined') {
                // 设置分享信息
                 QFH5.setShareInfo(detail.title,detail.images[0].url,'test',window.location.host+'/subwap/detail-client.html?id='+detail.id+'&p='+nowphone,function(state,data){
                      //回调是所有分享操作的回调，无论从右上角菜单发起或openShareDialog或openShare发起，分享完后一定执行此回调
                      if(state==1){
                          //分享成功
                          alert(data.type);//分享平台
                      }else{
                          //分享失败
                          alert(data.error);//失败原因
                      }
                  },3,'',JSON.stringify(WxMiniProgram));
                 // QFH5.setShareInfo(detail.title,detail.images[0].url,'test',window.location.host+'/subwap/detail-client.html?id='+detail.id+'&p='+nowphone,function(state,data){
                 //      //回调是所有分享操作的回调，无论从右上角菜单发起或openShareDialog或openShare发起，分享完后一定执行此回调
                 //      if(state==1){
                 //          //分享成功
                 //          alert(data.type);//分享平台
                 //      }else{
                 //          //分享失败
                 //          alert(data.error);//失败原因
                 //      }
                 //  });
            }
            
        });

        
    });
	//获取ID
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
		hid = GetQueryString('id');
	}
    // if(GetQueryString('phone')!=''&&GetQueryString('phone')!=undefined) {
    //     phone = GetQueryString('phone');
    // }
	//获取数据
    	
    // setInterval("console.log($('.detail-sailpoint-message').height())",5);
    // if ($('.detail-sailpoint-message').height()<3rem) {
    //     $('.maidian-on-off').css('display','none');
    // } 
});
function showQfImgTop(i) {
    QFH5.viewImages(i,topimglist);
}
function showQfImgHx(i) {
    QFH5.viewImages(i,hximglist);
}
function sameArea(){
    //同区域楼盘
    $.get('/api/plot/list?street='+streetid+'&limit=6',function(data) {
        samearea=data.data.list;
        // console.log(samearea);
        if(samearea.length>1){
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
    if(is_user==true) {
        $.get('/api/plot/checkIsVip',function(data) {
            if(data.status=='success') {
                $.post('/api/plot/addMakertNew',{'hid':hid},function(data) {
                    if(data.status=='success') {
                        alert('操作成功');
                        location.reload();
                    } else {
                        alert(data.msg);
                    }
                });
            } else {
                alert(data.msg);
                location.href="duijierennew.html";
            }
        })
    } else {
        checkUser();
        
    // location.href="duijieren.html?hid="+hid;
    }
}
//分享页面
function share(){
    QFH5.openShareDialog();
}
function toUser() {
    $.get('/api/index/getQfUid',function(data) {
            if(data.status=='error') {
                alert('登录成功后请关闭本页面重新进入');
                QFH5.jumpLogin(function(state,data){
                  //未登陆状态跳登陆会刷新页面，无回调
                  //已登陆状态跳登陆会回调通知已登录
                  //用户取消登陆无回调
                  if(state==2){
                  alert("您已登陆");
                  }
              })
            } else {
                location.href = '/my';
            }
        });
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
        $('.detail-sailpoint-message').css('max-height','5.2rem');
        $('.maidian-on-off').empty();
        $('.maidian-on-off').append('展开更多');
    }
});



//点击跳转
// $('#paramter').click(function(){
//     location.href='/wap/plot/paramter?hid='+hid;
// });   
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
    if(is_user==true)
        location.href='distribution.html?hid='+hid+'&title='+title;
    else
        checkUser();
});
$('.detail-laststate-edit').click(function(e){
    e.stopPropagation();
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
// 点评
$('#comment-title').click(function(){
    location.href='/subwap/commentList.html?hid='+hid;
});
$('.detail-comment .remark').click(function(){
    location.href='/subwap/commentSubmit.html?hid='+hid;
});
// 提问
$('#question-title').click(function(){
    location.href='/subwap/questionList.html?hid='+hid;
});
$('.detail-question .remark').click(function(){
    location.href='/subwap/questionSubmit.html?hid='+hid;
});




function copyUrl2() {
    alert('已成功复制手机号，请至微信搜索添加');
}
$(window).on("popstate",function(e){
    if(history.state!=null && history.state.url=='list') {
        location.href = 'list.html';
    }  
});

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
    if(is_user==true) {
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
    } else {
        checkUser();
    }
        
});
//点击出现付费规则
$('.fufei-detail').click(function() {
    $('.rules-bg').css('display','block');
});
//点击付费说明消失
$('.shutoff-img').click(function() {
    $('.rules-bg').css('display','none');
});
//关注
$('#save').click(function() {
    $.get('/api/plot/addSave?hid='+hid,function(data) {
        if(data.status=='success') {
            if ($('#save').hasClass('notsave')) {
                $('#save').removeClass('notsave');
                $('#save').addClass('save');
                $('.detail-button-save-text').html('已关注');
                $('#save').attr('src','./img/save.png');
            } else {
                $('#save').removeClass('save');
                $('#save').addClass('notsave');
                $('.detail-button-save-text').html('关注');
                $('#save').attr('src','./img/notsave.png');
            }
        }
        // alert(data.msg);
    });
});

// 显示更多快捷图标
$('.list-more').click(function() {
    $('.list-more').hide();
    $('.list-more-show').show();
    $('.background').show();

});
// 隐藏更多快捷图标
$('.close-img').click(function() {
    $('.list-more').show();
    $('.list-more-show').hide();
    $('.background').hide();

});

$('.list-back-img').click(function(){
    location.href='/subwap/list.html';
});
$('.home-img').click(function(){
    location.href='/subwap/list.html';
});
$('#subit').click(function(){
    if(is_user==true){
        location.href='report.html?hid='+detail.id+'&title='+detail.title;
    }else{
        checkUser();
    }
});

function setChat(obj) {
    if($(obj).data('text')) {
        QFH5.jumpTalk($(obj).data('text'),'','');
    }
}

function makeCall(obj) {
    $.get('/api/plot/callPhone?hid='+hid+'&key='+$(obj).attr('href')+'&fxphone='+nowphone,function(data) {
        
    });
}