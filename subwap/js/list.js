var o = new Object();
function init() {
    o.toptag = '';
    o.area = '';
    o.street = '';
    o.aveprice = '';
    o.sfprice = '';
    o.sort = '';
    o.wylx = '';
    o.kw = '';
    o.company = '';
    o.zxzt = '';
    o.page = '';
    o.page_count = '';
    o.num = '';
}
var filter = new Object();
var is_jy = 0;
var is_user = false;
//==============核心代码=============  
var winH = $(window).height(); //页面可视区域高度   
//获取传过来的ID的函数
var hid = '';
var title='';
var phone='';
var areaid='';
var streetid='';
var url='';
var our_uids = '';
var thisphone = '';
var thisurl = '';
// var is_user = false;
var listheight='';
var detail=new Object();

var topimglist = new Array;
var hximglist = new Array;
Array.prototype.contains = function ( needle ) {
  for (i in this) {
    if (this[i] == needle) return true;
  }
  return false;
}
var scrollHandler = function() {
    // if($('.detailshow').is('hide')) {
        var pageH = $('.listshow').height();
        var scrollT = $(window).scrollTop(); //滚动条top   
        var aa = (pageH - winH - scrollT) / winH;
        if (aa < 0.02) { //0.02是个参数  
            if (o.page < o.page_count) {
                o.page++;
                
                ajaxAddList(o);
            }
        }
    // }
}
//定义鼠标滚动事件  
$(window).scroll(scrollHandler);
//==============核心代码=============  

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1
            c_end = document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return ""
}

$(window).on("popstate",function(e){
    console.log(history.state);
    // alert(history.state.url);
    if(history.state!=null&&history.state.url=='list') {

        $('.detailshow').addClass('hide');
        $('.listshow').removeClass('hide');
        // history.pushState({url:'list'},'',thisurl);
        setTimeout(function() {
            window.scrollTo(0,listheight);
        },50);
    }
        
});
function checkId(obj) {

    if(is_user == false) {

        // $(obj).attr('href','#');
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
                    }else
                        location.href = 'register.html?phone='+data.data.phone;
                }
            });
            
        // $(obj).attr('href','login.html')
        // location.href = '';
    } else {
        listheight=$(window).scrollTop();
        $('.listshow').addClass('hide');
        $('.detailshow').removeClass('hide');
        history.pushState({url:'detail'},'',$(obj).data('href').replace('#',''));
        if($(obj).data('id')!=hid) {
            
            showdetail($(obj).data('id'));
        } else {
            window.scrollTo(0,0);
        }
        // $(obj).attr('href','#');
            
    }
}

function setCookie(name,value) {
    $.cookie(name, value , { path: '/', expires: 10 });
}
function getCookie(name) {
   return $.cookie(name);
}

//同区域跳转 
function turnDetail(obj){
    // history.pushState({url:'detail','id':hid},'','detail.html?id='+hid);
    history.pushState({url:'detail','id':hid},'','detail.html?id='+obj);
    showdetail(obj);
    // location.href="detail.html?id="+obj;
}
$(document).ready(function() {
    init();
    thisurl = '';
    var toptag = '';
    ajaxGetTop();
    ajaxGetFilter(); 
    $('#priceul').append('<li class="filter2-active" id="price0" onclick="setPrice(this)">不限<div class="line" style="left:-1.33rem"></div></li>');
    $('#FirstPayul').append('<li class="filter3-active" id="FirstPay0" onclick="setFirstPay(this)">不限<div class="line" style="left:-1.33rem"></div></li>');
    $('#filter4-list').append('<li id="filter4-title0"></li>');
    if(getCookie('house_lat')=='') {
        QFH5.getLocation(function(state,data){
          if(state==1){
            //获取成功
            var latitude = data.latitude;
            var longitude = data.longitude;
            setCookie('house_lat',latitude);
            setCookie('house_lng',longitude);
            // var address = data.address;
          }else{
            //获取失败
            alert(data.error);//data.error: string
          }
        });
    }
    $.get('/api/index/getQfUid', function(data) {
        
        if(data.status == 'success') {
                $.get('/api/plot/getHasCoo', function(data) {
                if(data.status == 'error') {
                    getLocation();
                }
            });
            
        }
        $.get('/api/config/index',function(data) {
            is_user = data.data.is_user;      
            is_jy = data.data.is_jy;
            if (GetQueryString('area') != null) {
                o.area = GetQueryString('area');
                $('#areaul').append('<li onclick="setArea(this)" id="area0" data-id="0">不限</li>');
                setArea($('#areaul li[data-id="'+o.area+'"]')[0]); 
                
            } else {    
                $('#areaul').append('<li onclick="setArea(this)" id="area0" data-id="0" class="filter1-left-active">不限</li>');
                setArea($('#areaul li')[0]); 
            } 
            if(is_user==1) {
                QFH5.getUserInfo(function(state,data){
                  if(state==1){
                    $('.headimg').attr('src',data.face);
                    //登陆状态，有数据
                    // var uid = data.uid;//用户UID int
                    // var username = data.username;//用户名称 string
                    // var deviceid = data.deviceid;//用户设备唯一ID并MD5加密 string
                    // var phone = data.phone;//用户绑定的手机号，没有绑定手机号给空字符串
                    var face = data.face;//用户头像地址 string
                    //替换用户头像
                    $('.list-headimg1').css('display','none');
                    $('.list-headimg2').css('display','block');
                    $('.headimg2').attr('src',face);
                  }else{
                    //未登录
                    alert(data.error);//data.error string
                  }
                })
            }
        });    
        if (GetQueryString('kw') != null) {
            o.kw = GetQueryString('kw');
            // thisurl = 'list.html?kw='+GetQueryString('kw');
            // history.pushState({url:'list'},'',thisurl);
            showkw();
        }else if (GetQueryString('zd_company') != null) {
            o.company = GetQueryString('zd_company');
            html = ' &nbsp;' + GetQueryString('company') + ' x&nbsp; ';
            $('#companytag').html(html);
            $("title").html(GetQueryString('company')+'-多盘联动-诚邀分销'); 
            // thisurl = 'list.html?zd_company='+GetQueryString('zd_company');
            // history.pushState({url:'list'},'',thisurl);
        }
        var winHeight = ($(window).height() - 93) / 18.75;
        $('.filter-filter-bg').css({ "height": winHeight + "rem" });
        history.replaceState({url:'list'},'',thisurl);
    });
        
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
//请求商品列表
function ajaxGetList(obj) {
    // 出现加载中
    $('.loaddiv').css('display','block');
    var params = '?t=1';
    $('#ul1').empty();
    if (obj.toptag != '' && obj.toptag != 'undefined') {
        params += '&toptag=' + obj.toptag;
    }
    if (obj.area != '' && obj.area != 'undefined') {
        params += '&area=' + obj.area;
    }
    if (obj.street != '' && obj.street != 'undefined') {
        params += '&street=' + obj.street;
    }
    if (obj.aveprice != '' && obj.aveprice != undefined) {
        params += '&aveprice=' + obj.aveprice;
    }
    if (obj.sfprice != '' && obj.sfprice != undefined) {
        params += '&sfprice=' + obj.sfprice;
    }
    if (obj.sort != '' && obj.sort != undefined) {
        params += '&sort=' + obj.sort;
    }
    if (obj.wylx != '' && obj.wylx != undefined) {
        params += '&wylx=' + obj.wylx;
    }
    if (obj.zxzt != '' && obj.zxzt != undefined) {
        params += '&zxzt=' + obj.zxzt;
    }
    if (obj.kw != '' && obj.kw != undefined) {
        params += '&kw=' + obj.kw;
    }
    if (obj.company != '' && obj.company != undefined) {
        params += '&company=' + obj.company;
    }
    // if(obj.page != '' && obj.page != undefined) {
    //     params += '&page='+obj.page;
    // }

    $.get('/api/plot/list' + params, function(data) {
        var html = '';
        $('#num').html('0');
        o.page = data.data.page;
        o.page_count = data.data.page_count;
        o.num = data.data.num;
        
        if (data.data.length == undefined) {
            var list = data.data.list;
            for (var i = 0; i < list.length; i++) {
                item = list[i];
                company = '';
                companyid = '';
                if (item.zd_company.name != undefined) {
                    company = item.zd_company.name;
                    companyid = item.zd_company.id;
                }
                if(item.sort>0){
                    if (is_user==true) {
                        var payword = item.pay==''?'暂无佣金方案':item.pay;
                        if (item.distance != '') {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    } else {
                        if (item.distance != '') {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    }
                }else{
                    if (is_user==true) {
                        var payword = item.pay==''?'暂无佣金方案':item.pay;
                        if (item.distance != '') {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    } else {
                        if (item.distance != '') {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    }
                }
                

            }
        }
        $('#ul1').empty();
        $('#ul1').append(html);
        $('#num').html(o.num);
        // 加载中消失
        $('.loaddiv').css('display','none');
    });
}

function ajaxAddList(obj) {
    // 出现加载中
    $('.loaddiv').css('display','block');
    var params = '?t=1';
    // $('#ul1').empty();
    if (obj.toptag != '' && obj.toptag != 'undefined') {
        params += '&toptag=' + obj.toptag;
    }
    if (obj.area != '' && obj.area != 'undefined') {
        params += '&area=' + obj.area;
    }
    if (obj.street != '' && obj.street != 'undefined') {
        params += '&street=' + obj.street;
    }
    if (obj.aveprice != '' && obj.aveprice != undefined) {
        params += '&aveprice=' + obj.aveprice;
    }
    if (obj.sfprice != '' && obj.sfprice != undefined) {
        params += '&sfprice=' + obj.sfprice;
    }
    if (obj.sort != '' && obj.sort != undefined) {
        params += '&sort=' + obj.sort;
    }
    if (obj.wylx != '' && obj.wylx != undefined) {
        params += '&wylx=' + obj.wylx;
    }
    if (obj.kw != '' && obj.kw != undefined) {
        params += '&kw=' + obj.kw;
    }
    if (obj.company != '' && obj.company != undefined) {
        params += '&company=' + obj.company;
    }
    if (obj.page != '' && obj.page != undefined) {
        params += '&page=' + obj.page;
    }

    $.get('/api/plot/list' + params, function(data) {
        var html = '';
        o.page = data.data.page;
        o.page_count = data.data.page_count;
        o.num = data.data.num;
        if (data.data.length == undefined) {
            var list = data.data.list;
            for (var i = 0; i < list.length; i++) {
                item = list[i];
                company = '';
                companyid = '';
                if (item.zd_company.name != undefined) {
                    company = item.zd_company.name;
                    companyid = item.zd_company.id;
                }
                if (item.sort>0) {
                    if (is_user==true) {
                        var payword = item.pay==''?'暂无佣金方案':item.pay;                
                        if (item.distance != '') {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    } else {
                        if (item.distance != '') {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-housej" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-jing">顶</div><div class="house-text-headj">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    }
                } else {
                    if (is_user==true) {
                        var payword = item.pay==''?'暂无佣金方案':item.pay;                
                        if (item.distance != '') {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">' + payword + '</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    } else {
                        if (item.distance != '') {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">' + item.distance + 'km</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        } else {
                            html += '<li class="list-house" style="list-style-type: none;"><div class="line"></div><a data-href="detail.html?id='+item.id+'" data-id="'+item.id+'" onclick="checkId(this)"><img class="house-img" src="' + item.image + '"/><div class="house-text-head">' + item.title + '</div></a><div class="house-text-plot_name-2"> ' + item.area + ' ' + item.street + '</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">暂无权限查看</div><div class="house-text-price">' + item.price + '' + item.unit + '</div><div class="house-text-company" onclick="setCompany(this)" data-id="' + companyid + '">' + company + '</div></li>';
                        }
                    } 
                }
                

            }
        }
        $('#ul1').append(html);
        $('#num').html(o.num);
        // 加载中消失
        $('.loaddiv').css('display','none');
    });
}

function changeState() {
    // body...
}

//公司列表
function setCompany(obj) {
    var turl = 'list.html?zd_company='+$(obj).data('id')+'&company='+$(obj).html();
    history.replaceState({url:'list'},'',turl);
    o.company = GetQueryString('zd_company');
    html = ' &nbsp;' + GetQueryString('company') + ' x&nbsp; ';
    $('#companytag').html(html);
    $("title").html(GetQueryString('company')+'-多盘联动-诚邀分销'); 

    ajaxGetList(o);
    // changeState();
    // location.href = 'list.html?zd_company='+$(obj).data('id')+'&company='+$(obj).html();
}
//头部文字
function getTopId(obj) {
    $('.list-head-item').attr('class', 'list-head-item list-head-text');
    $(obj).parent().attr('class', 'list-head-item list-head-text list-head-item-active');

    var id = $(obj).parent().data('id');
    if (id != undefined)
        o.toptag = id;
    else
        o.toptag = '';
    ajaxGetList(o);
    // location.href = '/subwap/list.html?toptag='+id;
    // alert(id);
}

function ajaxGetTop() {
    $.get('/api/tag/index?cate=wzlm', function(data) {
        var html = '';
        if (data.data.length > 0) {
            var list = data.data;
            for (var i = 0; i < list.length; i++) {
                item = list[i];
                html += '<li class="list-head-item list-head-text" data-id="' + item.id + '"><a onclick="getTopId(this)">' + item.name + '</a></li>';
            }
        }
        $('#alltop').after(html);
    });
}
//筛选栏文字
function ajaxGetFilter() {
    $.get('/api/tag/list?cate=plotFilter', function(data) {
        filter = data.data;
        var html = '';
        if (data.data.length > 0) {
            var list = data.data;
            for (var i = 0; i < list.length; i++) {
                item = list[i];
                html += '<li class="list-filter-area list-filter-text" data-id="' + item.id + '"><a onclick="getFilterId(this)">' + item.name + '</a><img class="list-filter-slat" src="./img/slatdown.png" /></li>';
            }            
        }
        $('#filter-lead').after(html);
        //显示初始选中地区
        var arealist = data.data[0].list;
        for (var j = 0; j < arealist.length; j++) {
            if (GetQueryString('area')!=null) {
                if (GetQueryString('area')==arealist[j].id) {
                    $('.list-filter li:eq(0) a').html(arealist[j].name);
                }
            } 
        }
    });

}

//filter筛选栏点击展开消失
function getFilterId(obj) {
    if ($(obj).parent().is('.list-filter-area-active')) {
        $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
        $('.list-filter-slat').attr('src', './img/slatdown.png');
    } else {
        $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
        $('.list-filter-slat').attr('src', './img/slatdown.png');
        $(obj).parent().attr('class', 'list-filter-area list-filter-text list-filter-area-active');
        $(obj).next().attr('src', './img/slatup.png');
        getAreas(o);
        getPrice(o);
        getFirstPay(o);
        getFilterTitle(o);
    }
    // location.href = '/subwap/list.html?toptag='+id;
    // alert(id);
    if ($('.list-filter li:eq(0)').is('.list-filter-area-active')) {
        $('#filter1').css({ "display": "block" });
    } else {
        $('#filter1').css({ "display": "none" });
    }
    if ($('.list-filter li:eq(1)').is('.list-filter-area-active')) {
        $('#filter2').css({ "display": "block" });
    } else {
        $('#filter2').css({ "display": "none" });
    }
    if ($('.list-filter li:eq(2)').is('.list-filter-area-active')) {
        $('#filter3').css({ "display": "block" });
    } else {
        $('#filter3').css({ "display": "none" });
    }
    if ($('.list-filter li:eq(3)').is('.list-filter-area-active')) {
        $('#filter4').css({ "display": "block" });
    } else {
        $('#filter4').css({ "display": "none" });
    }
}
//显示列表1
function getAreas(obj) {
    if ($('#areaul').find('li').length == 1) {
        var html = '';    
        var list = filter[0].list;
        if (list.length > 0) {
            for (var j = 0; j < list.length; j++) {
                if (GetQueryString('area')!=null) {
                    if (GetQueryString('area')==list[j].id) {
                        html += '<li onclick="showStreet(this)" class="filter1-left-active" data-id="' + list[j].id + '">' + list[j].name + '</li>';
                    }else{
                        html += '<li onclick="showStreet(this)" data-id="' + list[j].id + '">' + list[j].name + '</li>';
                    }
                } else {
                    html += '<li onclick="showStreet(this)" data-id="' + list[j].id + '">' + list[j].name + '</li>';
                }
            }
        }             
        $('#area0').after(html);
        if (obj.area != '' && obj.area != 'undefined') {
            $('#obj.area').addClass('filter1-left-active');
        }
    }
}

function showStreet(obj) {
    $('.filter-filter1-left li').removeClass('filter1-left-active');
    $(obj).addClass('filter1-left-active');
    $('#streetul').empty();
    $('#streetul').append('<li id="street0" class="filter1-right-active" onclick="setArea(this)" data-type="area" data-id="' + $(obj).data('id') + '" data-name="' + $(obj).text() + '">不限<div class="line"></div></li>');
    $('.filter-filter1-right').css('display', 'block');
    var areaid = $(obj).data('id');
    var arealist = filter[0];
    var html = '';
    var list = arealist.list;
    if (list.length > 0) {
        for (var i = 0; i < list.length; i++) {
            if (areaid == list[i].id) {
                var streets = list[i].childAreas;
                if (streets.length > 0) {
                    for (var j = 0; j < streets.length; j++) {
                        html += '<li onclick="setArea(this)" data-type="street" data-id="' + streets[j].id + '">' + streets[j].name + '<div class="line"></div></li>';
                    }
                }
                break;
            }
        }
    }
    $('#street0').after(html);
}

function setArea(obj) {
    if ($(obj).attr('id') == 'area0') {
        $('#areaul li').removeClass('filter1-left-active');
        $(obj).addClass('filter1-left-active');
        $('.filter-filter1-right').css('display', 'none');
    } else {
        $('.filter-filter1-right li').removeClass('filter1-right-active');
        $(obj).addClass('filter1-right-active');
    }
    $('#filter1').css('display', 'none');
    $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src', './img/slatdown.png');
    // if($(obj).data('type')=='area') {
    //     if ($(obj).attr('id')=='area0') {
    //     o.area = '';
    //     } else {
    //     o.area = $(obj).data('id');
    //     }
    // } else {
    //     if ($(obj).attr('id')=='street0') {
    //         o.street='';
    //     } else {
    //         o.street = $(obj).data('id');
    //     }

    // }
    if ($(obj).attr('id') == 'area0') {
        o.area = '';
        o.street = '';
        $('.list-filter li:eq(0) a').html("区域");
    } else if ($(obj).attr('id') == 'street0') {
        o.street = '';
        o.area = $(obj).data('id');
        if ($(obj).attr("data-name")!=''&&$(obj).attr("data-name")!=undefined) {
            $('.list-filter li:eq(0) a').html($(obj).attr("data-name"));
        } 
    } else {
        o.street = $(obj).data('id');
        if($(obj).data('type')=='street')
            o.area = '';
        if ($(obj).text()!=''&&$(obj).text()!=undefined) {
            $('.list-filter li:eq(0) a').html($(obj).text());
        } 
    }
    ajaxGetList(o);
}
//显示列表2
function getPrice(obj) {
    if ($('#priceul').find('li').length == 1) {
        var html = '';
        for (var i = 0; i < filter.length; i++) {
            var item = filter[i];
            if (item.name == '均价') {
                var price = item.list;
                if (price.length > 0) {
                    for (var k = 0; k < price.length; k++) {
                        html += '<li onclick="setPrice(this)" data-id="' + price[k].id + '">' + price[k].name + '<div class="line" style="left:-1.33rem"></div></li>';
                    }
                }
                break;
            }
        }
        $('#price0').after(html);
    }
}

function setPrice(obj) {
    $('.filter-filter2 li').removeClass('filter2-active');
    $(obj).addClass('filter2-active');
    $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src', './img/slatdown.png');
    $('#filter2').css('display', 'none');
    o.aveprice = $(obj).data('id');
    $('.list-filter li:eq(1) a').html($(obj).text().substring(0,4));
    ajaxGetList(o);
}
//显示列表3
function getFirstPay(obj) {
    if ($('#FirstPayul').find('li').length == 1) {
        var html = '';
        for (var i = 0; i < filter.length; i++) {
            var item = filter[i];
            if (item.name == '首付') {
                var pay = item.list;
                if (pay.length > 0) {
                    for (var k = 0; k < pay.length; k++) {
                        html += '<li onclick="setFirstPay(this)" data-id="' + pay[k].id + '">' + pay[k].name + '<div class="line" style="left:-1.33rem"></div></li>';
                    }
                }
                break;
            }
        }
        $('#FirstPay0').after(html);
    }
}

function setFirstPay(obj) {
    $('.filter-filter3 li').removeClass('filter3-active');
    $(obj).addClass('filter3-active');
    $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src', './img/slatdown.png');
    $('#filter3').css('display', 'none');
    o.sfprice = $(obj).data('id');
    $('.list-filter li:eq(2) a').html($(obj).text().substring(0,4));
    ajaxGetList(o);
}
//显示列表4
function getFilterTitle(obj) {
    if ($('#filter4-list').find('li').length == 1) {
        var html = '';
        for (var i = 0; i < filter.length; i++) {
            var item = filter[i];
            if (item.name == '更多') {
                var list = item.list;
                if (list.length > 0) {
                    for (var a = 0; a < list.length; a++) {
                        var secondlist = list[a].list;
                        var innerhtml = '';
                        if (secondlist.length > 0) {
                            for (var b = 0; b < secondlist.length; b++) {
                                innerhtml += '<li data-type="'+list[a].filed+'" onclick="setFilterItem(this)" data-id="' + secondlist[b].id + '">' + secondlist[b].name + '</li>';
                            }
                        }
                        html += '<li data-id="' + list[a].id + '" class="filter4-item"><div class="filter4-item-head"><strong>' + list[a].name + '</strong></div><div class="filter4-item-item"><ul class="clearfloat"><div id="filter4-item0"><li class="filter-filter4-button-active" data-id="0" data-type="'+list[a].filed+'" onclick="setFilterItem(this)">不限</li>' + innerhtml + '</div></ul></div></li>';
                    }
                }
                break;
            }
        }
        $('#filter4-title0').after(html);
    }
}

function setFilterItem(obj) {
    $(obj).parent().children().removeClass('filter-filter4-button-active');
    $(obj).addClass('filter-filter4-button-active');
    if ($(obj).data('type') == 'sort') {
        o.sort = $(obj).data('id');
    } else if($(obj).data('type') == 'wylx'){
        o.wylx = $(obj).data('id');
    } else if($(obj).data('type') == 'zxzt'){
        o.zxzt = $(obj).data('id');
    }


}
$('#reset').click(function() {
    $('#filter4-list').find('li').removeClass('filter-filter4-button-active');
    o.wylx = '';
    o.zxzt = '';
    o.sort = '';
});
$('#ensure').click(function() {
    $('.list-filter-area').attr('class', 'list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src', './img/slatdown.png');
    $('#filter4').css('display', 'none');
    ajaxGetList(o);
});

function delCom() {
    location.href = '/subwap/list.html';
}

function showkw() {
    html = ' &nbsp;搜索条件：&nbsp;' + o.kw + ' x&nbsp; ';
    $('#companytag').html(html);
}



function cleardetail() {
    $('.wxscript').empty();
    $('.detail-top-img-title').empty();
    $('.detail-head-price').empty();
    $('.head-price-tags ul').empty();
    $('#maptext').empty();
    $('#zdtext').empty();
    $('.detail-laststate-message').empty();
    $('.detail-pricerules-message').empty();
    $('.detail-sailpoint-message').empty();
    $('.detail-daikanrules-message').empty();
    $('#mainstyle ul').empty();
    $('.swiper-wrapper').empty();
    $('.telephone-consult li').not('#showadd').remove();
    $('#samearea ul').empty();
    topimglist = [];
    hximglist = [];
}

function showdetail(id) {
    cleardetail();
    window.scrollTo(0,0);
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
    // if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
        hid = id;
    // }
    // if(GetQueryString('phone')!=''&&GetQueryString('phone')!=undefined) {
    //     phone = GetQueryString('phone');
    // }
    //获取数据
    $.get('/api/plot/info?id='+hid+'&phone='+phone, function(data) {
            detail = data.data;
            areaid = detail.areaid;
            streetid = detail.streetid;
            sameArea();
            $('title').html(detail.title);
             //底部按钮变化
            if (detail.is_contact_only==1) {
                $('.detail-buttom0').css('display','none');
                $('.detail-buttom1').css('display','block');
            }
            else if (detail.is_contact_only==2 || is_user==false){
                $('.detail-buttom0').css('display','none');
                $('.detail-buttom2').css('display','block');
            }
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
                for (var i = 0; i < detail.hx.length; i++) {
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
                    topimglist.push(detail.images[i].url);
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
                        icon = "fbusernew.png";
                        // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/fuzeuser.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                    } else {
                        console.log(detail.ff_phones.contains(detail.phonesnum[i]));
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
                    $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/'+icon+'"><div class="consult-text">'+detail.phones[i]+word+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
                }
            }
            
        });
    // setInterval("console.log($('.detail-sailpoint-message').height())",5);
    // if ($('.detail-sailpoint-message').height()<3rem) {
    //     $('.maidian-on-off').css('display','none');
    // } 
}

function showQfImgTop(i) {
    QFH5.viewImages(i,topimglist);
}
function showQfImgHx(i) {
    QFH5.viewImages(i,hximglist);
}

//将屏幕滑回最上端
$('.list-stick').click(function(){
    window.scrollTo(0,0);
});

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
                QFH5.jumpUser(data.data.uid);
            }
        });
}

// function GetQueryString(name) {
//     var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
//     var r = window.location.search.substr(1).match(reg);
//     if (r != null) return unescape(decodeURI(r[2]));
//     return null;
// }
// $(document).ready(function(){
//     var clipboard = new Clipboard('.copy-weixin');  
//     $.get('/api/config/index',function(data) {
//         if(data.data.is_user == true) {
//             is_user = true;
//             our_uids = data.data.our_uids;
//             thisphone = data.data.user.phone;
//         }
//          //底部按钮变化
//         if (detail.is_contact_only==1) {
//             $('.detail-buttom0').css('display','none');
//             $('.detail-buttom1').css('display','block');
//         }
//         else if (detail.is_contact_only==2 || is_user==false){
//             $('.detail-buttom0').css('display','none');
//             $('.detail-buttom2').css('display','block');
//         }
        
//     });
//     //获取ID
//     if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined) {
//         hid = GetQueryString('id');
//     }
//     // if(GetQueryString('phone')!=''&&GetQueryString('phone')!=undefined) {
//     //     phone = GetQueryString('phone');
//     // }
//     //获取数据
//     $.get('/api/plot/info?id='+hid+'&phone='+phone, function(data) {
//         detail = data.data;
//         areaid = detail.areaid;
//         streetid = detail.streetid;
//         sameArea();
//         $('title').html(detail.title);
//         $.get('/api/wx/zone?imgUrl='+detail.images[0]['url']+'&title='+detail.wx_share_title+'&link='+window.location.href+'&desc='+detail.sell_point.substring(0,30),function(data) {
//             $('body').append(data);
//         });
//         $('#subit').attr('href','report.html?hid='+detail.id+'&title='+detail.title);
//         $('.detail-top-img-title').append(detail.title+'-'+detail.area+'-'+detail.street);
//         area=detail.area;
//         title=detail.title;
//         $('.detail-head-price').append(detail.price,detail.unit);
//         // 顶部价格下面的标签
//         if (detail.tags.length<1) {
//             $('.head-price-tags').css('display','none');
//         }
//         for (var i = 0; i < detail.tags.length; i++) {
//             if (i%3==1) {
//                 // $('#showadd').css('display','none');
//                 $('.head-price-tags ul').append('<li class="color1">'+detail.tags[i]+'</li>'); 
//             }else if(i%3==2){
//                 // $('#showadd').css('display','none');
//                 $('.head-price-tags ul').append('<li class="color2">'+detail.tags[i]+'</li>'); 
//             }else{
//                 $('.head-price-tags ul').append('<li class="color3">'+detail.tags[i]+'</li>');  
//             }
//         }
//         $('#maptext').append(detail.address);
//         $('#zdtext').append(detail.zd_company.name);
//         $('#zd').attr('data-id',detail.zd_company.id);
//         $('#zd').attr('data-name',detail.zd_company.name);
//         $('.detail-daikanrules-message').append(detail.dk_rule?detail.dk_rule:'暂无');
//         if (detail.news!=''&&detail.news!=undefined) {
//             $('.detail-laststate-message').append(detail.news);
//         }else{
//             $('.detail-laststate-message').append('暂无');
//             $('#laststate-img').css('display','none');
//         }
//         if(detail.is_login == '1') {
//             if(detail.pay.length<=1) {
//                 $('#fangannum').css('display','none');
//             }
//             if(detail.pay.length>0){
//                 pay = detail.pay[0];
//                 content = pay['title']?(pay['title'] +'<br>'+ pay['content']):pay['content'];
//                 $('.detail-pricerules-message').append(content);
//                 if(detail.pay.length<=1) {
//                     $('#fangannum').css('display','none');
//                 } else {
                    
//                     $('#paynum').html(pay['num']);
//                 }
//             }else{
//                 $('.detail-pricerules-message').append('暂无');
//                 $('#paynum').html('0');
//             }
//         } else {
//             $('.detail-pricerules').css('display','none');
//         }
            
//         //楼盘卖点
//         if (detail.sell_point!=''&&detail.sell_point!=undefined) {
//             $('.detail-sailpoint-message').append(detail.sell_point);
//         } else {
//             $('.detail-sailpoint').css('display','none');
//         }
//         //插入主力户型
//         if(detail.hx!=''&&detail.hx!=undefined){
//             $('.detail-mainstyle').css('display','block');
//             for(var i=0;i<detail.hx.length;i++){
//                 if(detail.hx[i].size==''||detail.hx[i].size==undefined){
//                     detail.hx[i].size="--";
//                 }
//                 if(detail.hx[i].bedroom>0)
//                   $('#mainstyle ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room">'+detail.hx[i].bedroom+'房'+detail.hx[i].livingroom+'厅'+detail.hx[i].bathroom+'卫</div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
//                 else
//                     $('#mainstyle ul').append('<li><a href="'+detail.hx[i].image+'"><div class="detail-mainstyle-img"><img style="width: 7.307rem;height: 5.547rem;" src="'+detail.hx[i].image+'"></div></a><div class="detail-mainstyle-style">'+detail.hx[i].title+'</div><div class="detail-mainstyle-area">'+detail.hx[i].size+'㎡</div><div class="detail-mainstyle-room"></div><div class="detail-mainstyle-status">'+detail.hx[i].sale_status+'</div></li>');
//             }
//         }else{
//             $('.detail-mainstyle').css('display','none');
//         }
//         //判断能否编辑
//         if(detail.can_edit==0){
//             $('.detail-laststate-edit').css('display','none');
//         }else{
//             $('.detail-laststate-edit').css('display','block');
//         }
//         $('.mzsm').html(detail.mzsm);
//         //顶部图片  
//         if(detail.images!=''&&detail.images!=undefined){    
//             for (var i = 0; i < detail.images.length; i++) {
//                 // $('.detail-head-img-examplepic').html(detail.images[i].type);
//                 $('.swiper-wrapper').append('<div class="swiper-slide"><a href="'+detail.images[i].url+'"><img data-type="'+detail.images[i].type+'" class="detail-head-img" src="'+detail.images[i].url+'"></a></div>');
//             }
//         }
//         var swiper = new Swiper('.detail-head-img-container',{
//             loop: true,
//             onSlideChangeEnd:function() {
//                 $('.detail-head-img-examplepic').html($('.swiper-slide-active').find('img').data('type'));
//             }
//           });
       
//         // 插入查询电话
//         if(detail.phones.length > 0) {
//             for (var i = 0; i < detail.phones.length; i++) {
//                 var word = '';
//                 // console.log(detail.phones[i].indexOf(detail.phone));
//                 if (detail.phone && detail.phones[i].indexOf(detail.phone)>-1) {
//                     tmp  = detail.phones[i];
//                     phone=detail.phone;
//                     icon = "fuzeuser.png";
//                     // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/fuzeuser.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
//                 } else {
//                     if(detail.owner_phone && detail.phones[i].indexOf(detail.owner_phone)>-1) {
//                         icon = "fuzeuser.png";
//                         word = '<div class="fbuser">发布人</div>'
//                     } else {
//                         icon = "user.png";
//                     }
//                     // $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/user.png"><div class="consult-text">'+detail.phones[i]+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
//                 }
//                 // debugger;
//                 $('.telephone-consult ul').append('<li><a href="tel:'+detail.phones[i]+'"><div class="telephone-place"><img class="consult-user-img" src="./img/'+icon+'"><div class="consult-text">'+detail.phones[i]+word+'</div><div onclick="copyUrl2()" data-clipboard-text="'+detail.phonesnum[i]+'" class="copy-weixin">复制微信号</div><img class="consult-tel-img" src="./img/tel-green.png"></div><div class="line"></div></a></li>');
//             }
//         }
        
//     });
//     // setInterval("console.log($('.detail-sailpoint-message').height())",5);
//     // if ($('.detail-sailpoint-message').height()<3rem) {
//     //     $('.maidian-on-off').css('display','none');
//     // } 
// });
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
//点击出现付费规则
$('.fufei-detail').click(function() {
    $('.rules-bg').css('display','block');
});
//点击付费说明消失
$('.shutoff-img').click(function() {
    $('.rules-bg').css('display','none');
});