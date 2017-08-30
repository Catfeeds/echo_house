var o = new Object();

function init(){
    o.toptag = '';
    o.area = '';
    o.street = '';
    o.aveprice='';
    o.sfprice='';
    o.sort='';
    o.wylx='';
    o.kw = '';
    o.company='';
    o.page='';
    o.page_count = '';
    o.num = '';
}
var filter = new Object();

//==============核心代码=============  
var winH = $(window).height(); //页面可视区域高度   

var scrollHandler = function () {  
    var pageH = $(document.body).height();  
    var scrollT = $(window).scrollTop(); //滚动条top   
    var aa = (pageH - winH - scrollT) / winH;  
    if (aa < 0.01) {//0.02是个参数  
        if(o.page<o.page_count) {
            o.page++;
            ajaxAddList(o);  
        }
    }  
}  
//定义鼠标滚动事件  
$(window).scroll(scrollHandler);  
//==============核心代码=============  

function checkCookie() {
    // body...
}

// var Api = function(){
//         //百度地图API
//         // 创建 Geolocation 对象实例
//         var geolocation = new BMap.Geolocation();
//         // getCurrentPosition 方法用于返回用户当前位置
//         geolocation.getCurrentPosition(function(r) {
//             if(this.getStatus() === 0) {
//                 console.log(r.point);  // {lat: 34.2777999, lng: 108.95309828}
//             }
//             else {
//                 alert('定位失败，原因：' + this.getStatus());
//             }        
//         },{enableHighAccuracy: true})
//     }

//     //cookie
//     function getCookie(c_name){
//         if (document.cookie.length>0){
//             c_start=document.cookie.indexOf(c_name + "=")
//         if (c_start!=-1){ 
//             c_start=c_start + c_name.length+1 
//             c_end=document.cookie.indexOf(";",c_start)
//         if (c_end==-1) c_end=document.cookie.length
//             return unescape(document.cookie.substring(c_start,c_end))
//         } 
//       }
//     return ""
//     }

//     function setCookie(c_name,value,expiredays){
//         var exdate=new Date()
//         exdate.setDate(exdate.getDate()+expiredays)
//         document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
//     }
        
//     function checkCookie()
//         {
//         house_lng=getCookie('house_lng')
//         house_lat=getCookie('house_lat')
//         if (house_lng!=null && house_lng!="")
//           {
//             ajaxGetList(o);
//           }
//         else
//           {
//           Api();
//           if (house_lng!=null && house_lng!="")
//             {
//             setCookie('house_lng',r.point[0],1)
//             setCookie('house_lat',r.point[1],1)
//             }
//           }
//     }

$(document).ready(function(){
    init();
    var toptag = '';
    $('#areaul').append('<li onclick="setArea(this)" id="area0" data-id="0">不限</li>');
    $('#priceul').append('<li id="price0" onclick="setPrice(this)">不限<div class="line" style="left:-1.33rem"></div></li>');
    $('#FirstPayul').append('<li id="FirstPay0" onclick="setFirstPay(this)">不限<div class="line" style="left:-1.33rem"></div></li>');
    $('#filter4-list').append('<li id="filter4-title0"></li>');
    getLocation();
    if(GetQueryString('kw')!=null)
        o.kw = GetQueryString('kw');
    ajaxGetTop();
    ajaxGetFilter();
    ajaxGetList(o);
    var winHeight=($(window).height()-93)/18.75;
    $('.filter-filter-bg').css({"height":winHeight+"rem"});
    $.get('/api/tag/list?cate=plotFilter', function(data) {
        filter = data.data;
    });
});

function getLocation(){
    $.get('http://api.map.baidu.com/location/ip?ak=IEOQ5TmNBGPoIMQLqLyQDe193OjYYgNH',function(data) {
        console.log(data);
    });
    // if (navigator.geolocation){
    //     navigator.geolocation.getCurrentPosition(showPosition);
    // }else{
    //     alert("Geolocation is not supported by this browser.");
    // }
}
function showPosition(position){
    console.log("Latitude: " + position.coords.latitude + "<br />Longitude: " + position.coords.longitude);
}

function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(decodeURI(r[2])); return null;
}
//请求商品列表
function ajaxGetList(obj) {
    var params = '?t=1';
    $('#ul1').empty();
    if(obj.toptag != '' && obj.toptag != 'undefined') {
        params += '&toptag='+obj.toptag;
    }
    if(obj.area != '' && obj.area != 'undefined') {
        params += '&area='+obj.area;
    }
    if(obj.street != '' && obj.street != 'undefined') {
        params += '&street='+obj.street;
    }
    if(obj.aveprice != '' && obj.aveprice != undefined) {
        params += '&aveprice='+obj.aveprice;
    }
    if(obj.sfprice != '' && obj.sfprice != undefined) {
        params += '&sfprice='+obj.sfprice;
    }
    if(obj.sort != '' && obj.sort != undefined) {
        params += '&sort='+obj.sort;
    }
    if(obj.wylx != '' && obj.wylx != undefined) {
        params += '&wylx='+obj.wylx;
    }
    if(obj.kw != '' && obj.kw != undefined) {
        params += '&kw='+obj.kw;
    }
    if(obj.company != '' && obj.company != undefined) {
        params += '&company='+obj.company;
    }
    // if(obj.page != '' && obj.page != undefined) {
    //     params += '&page='+obj.page;
    // }

    $.get('/api/plot/list'+params, function(data) {
            var html = '';
            o.page = data.data.page;
            o.page_count = data.data.page_count;
            o.num = data.data.num;
            if(data.data.length == undefined) {
                var list = data.data.list;
                for (var i = 0; i < list.length; i++) {
                    item = list[i];
                    company = '';
                    companyid='';
                    if(item.zd_company.name != undefined) {
                        company = item.zd_company.name;
                        companyid = item.zd_company.id;
                    }
                    if(item.pay!=''){
                        if(item.distance!=''){
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name-2"> '+item.area+' '+item.street+'</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">'+item.pay+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">'+item.distance+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }else{
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name-2"> '+item.area+' '+item.street+'</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">'+item.pay+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }                      
                    }else{
                        if(item.distance!=''){
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name"> '+item.area+' '+item.street+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">'+item.distance+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }else{
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name"> '+item.area+' '+item.street+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }
                    }
                    
                }
            }
            $('#ul1').append(html);
            $('#num').html(o.num);
        });
}
function ajaxAddList(obj) {
    var params = '?t=1';
    // $('#ul1').empty();
    if(obj.toptag != '' && obj.toptag != 'undefined') {
        params += '&toptag='+obj.toptag;
    }
    if(obj.area != '' && obj.area != 'undefined') {
        params += '&area='+obj.area;
    }
    if(obj.street != '' && obj.street != 'undefined') {
        params += '&street='+obj.street;
    }
    if(obj.aveprice != '' && obj.aveprice != undefined) {
        params += '&aveprice='+obj.aveprice;
    }
    if(obj.sfprice != '' && obj.sfprice != undefined) {
        params += '&sfprice='+obj.sfprice;
    }
    if(obj.sort != '' && obj.sort != undefined) {
        params += '&sort='+obj.sort;
    }
    if(obj.wylx != '' && obj.wylx != undefined) {
        params += '&wylx='+obj.wylx;
    }
    if(obj.kw != '' && obj.kw != undefined) {
        params += '&kw='+obj.kw;
    }
    if(obj.company != '' && obj.company != undefined) {
        params += '&company='+obj.company;
    }
    if(obj.page != '' && obj.page != undefined) {
        params += '&page='+obj.page;
    }

    $.get('/api/plot/list'+params, function(data) {
            var html = '';
            o.page = data.data.page;
            o.page_count = data.data.page_count;
            o.num = data.data.num;
            if(data.data.length == undefined) {
                var list = data.data.list;
                for (var i = 0; i < list.length; i++) {
                    item = list[i];
                    company = '';
                    companyid='';
                    if(item.zd_company.name != undefined) {
                        company = item.zd_company.name;
                        companyid = item.zd_company.id;
                    }
                    if(item.pay!=''){
                        if(item.distance!=''){
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name-2"> '+item.area+' '+item.street+'</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">'+item.pay+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">'+item.distance+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }else{
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name-2"> '+item.area+' '+item.street+'</div><div class="house-text-pay-yong">佣</div><div class="house-text-pay">'+item.pay+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }                      
                    }else{
                        if(item.distance!=''){
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name"> '+item.area+' '+item.street+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><img class="distance-img" src="./img/icon-distance.png"><div class="list-distance">'+item.distance+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }else{
                            html += '<li style="list-style-type: none;"><div class="line"></div><a class="list-house"><img class="house-img" src="'+item.image+'"/><div class="house-text-head">'+item.title+'</div><div class="house-text-plot_name"> '+item.area+' '+item.street+'</div><div class="house-text-price">'+item.price+''+item.unit+'</div><div class="house-text-company" onclick="setCompany(this)" data-id="'+companyid+'">'+company+'</div></a></li>';
                        }
                    }
                    
                }
            }
            $('#ul1').append(html);
            $('#num').html(o.num);
        });
}

//公司列表
function setCompany(obj){
    init();
    o.company = $(obj).data('id');
    html = ' &nbsp;'+$(obj).html()+' x&nbsp; ';
    $('#companytag').html(html);
    ajaxGetList(o);
}
//头部文字
function getTopId(obj) {
    $('.list-head-item').attr('class','list-head-item list-head-text');
    $(obj).parent().attr('class','list-head-item list-head-text list-head-item-active');

   var id = $(obj).parent().data('id');
   if(id != undefined)
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
            if(data.data.length > 0) {
                var list = data.data;
                for (var i = 0; i < list.length; i++) {
                    item = list[i];
                    html += '<li class="list-head-item list-head-text" data-id="'+item.id+'"><a onclick="getTopId(this)">'+item.name+'</a></li>';
                }
            }
            $('#alltop').after(html);
        });
}
//筛选栏文字
function ajaxGetFilter() {
    $.get('/api/tag/list?cate=plotFilter', function(data) {
            var html = '';
            if(data.data.length > 0) {
                var list = data.data;
                for (var i = 0; i < list.length; i++) {
                    item = list[i];
                    html += '<li class="list-filter-area list-filter-text" data-id="'+item.id+'"><a onclick="getFilterId(this)">'+item.name+'</a><img class="list-filter-slat" src="./img/slatdown.png" /></li>';
                }
            }
            $('#filter-lead').after(html);
        });
}

//filter筛选栏点击展开消失
function getFilterId(obj) {
    if($(obj).parent().is('.list-filter-area-active')){
        $('.list-filter-area').attr('class','list-filter-area list-filter-text');
        $('.list-filter-slat').attr('src','./img/slatdown.png');
    }else{
        $('.list-filter-area').attr('class','list-filter-area list-filter-text');
        $('.list-filter-slat').attr('src','./img/slatdown.png');
        $(obj).parent().attr('class','list-filter-area list-filter-text list-filter-area-active');
        $(obj).next().attr('src','./img/slatup.png');
        getAreas(o);
        getPrice(o);
        getFirstPay(o);
        getFilterTitle(o);
    }
   // location.href = '/subwap/list.html?toptag='+id;
   // alert(id);
   if($('.list-filter li:eq(0)').is('.list-filter-area-active')){
        $('#filter1').css({"display":"block"});
    }else{
        $('#filter1').css({"display":"none"});
    }
    if($('.list-filter li:eq(1)').is('.list-filter-area-active')){
        $('#filter2').css({"display":"block"});
    }else{
        $('#filter2').css({"display":"none"});
    } 
    if($('.list-filter li:eq(2)').is('.list-filter-area-active')){
        $('#filter3').css({"display":"block"});
    }else{
        $('#filter3').css({"display":"none"});
    } 
    if($('.list-filter li:eq(3)').is('.list-filter-area-active')){
        $('#filter4').css({"display":"block"});
    }else{
        $('#filter4').css({"display":"none"});
    }
}

//显示列表1
function getAreas(obj) {
    if ($('#areaul').find('li').length==1) {
    var html = '';
    for (var i = 0; i < filter.length; i++) {
        var item = filter[i];
        if(item.name=='区域') {
            var list = item.list;
            if(list.length > 0) {
                for (var j = 0; j < list.length; j++) {
                    html += '<li onclick="showStreet(this)" data-id="'+list[j].id+'">'+list[j].name+'</li>';
                }
            }
            break;
        }
    }
    $('#area0').after(html);
    if(obj.area != '' && obj.area != 'undefined') {
        $('#obj.area').addClass('filter1-left-active');
    }
}
}

function showStreet(obj) {
    $('.filter-filter1-left li').removeClass('filter1-left-active');
    $(obj).addClass('filter1-left-active');

    $('#streetul').empty();
    $('#streetul').append('<li id="street0" onclick="setArea(this)" data-type="area" data-id="'+$(obj).data('id')+'">不限<div class="line"></div></li>');
    $('.filter-filter1-right').css('display','block');
    var areaid = $(obj).data('id');
    var arealist = filter[0];
    var html = '';
    var list = arealist.list;
    if(list.length>0) {
        for (var i = 0; i < list.length; i++) {
            if(areaid == list[i].id) {
                var streets = list[i].childAreas;
                if(streets.length>0) {
                    for (var j = 0; j < streets.length; j++) {
                        html += '<li onclick="setArea(this)" data-type="street" data-id="'+streets[j].id+'">'+streets[j].name+'<div class="line"></div></li>';
                    }
                }
                break;
            }
        }
    }
    $('#street0').after(html);
}
function setArea(obj) {
    if ($(obj).attr('id')=='area0') {
        $('#areaul li').removeClass('filter1-left-active');
        $(obj).addClass('filter1-left-active');
        $('.filter-filter1-right').css('display','none');
    } else {
        $('.filter-filter1-right li').removeClass('filter1-right-active');
        $(obj).addClass('filter1-right-active');
    }
    $('#filter1').css('display','none');
    $('.list-filter-area').attr('class','list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src','./img/slatdown.png');
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

    if ($(obj).attr('id')=='area0') {
        o.area = '';
        o.street = '';
    } else if($(obj).attr('id')=='street0') {
        o.street='';
        o.area = $(obj).data('id');
    } else {
        o.street = $(obj).data('id');
    }
    ajaxGetList(o);
}
//显示列表2
function getPrice(obj) {
    if ($('#priceul').find('li').length==1) {
    var html = '';
    for (var i = 0; i < filter.length; i++) {
        var item = filter[i];
        if(item.name=='均价') {
            var price = item.list;
            if(price.length > 0) {
                for (var k = 0; k < price.length; k++) {
                    html += '<li onclick="setPrice(this)" data-id="'+price[k].id+'">'+price[k].name+'<div class="line" style="left:-1.33rem"></div></li>';
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
    $('.list-filter-area').attr('class','list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src','./img/slatdown.png');
    $('#filter2').css('display','none');
    o.aveprice = $(obj).data('id'); 
    ajaxGetList(o);
}
//显示列表3
function getFirstPay(obj) {
    if ($('#FirstPayul').find('li').length==1) {
    var html = '';
    for (var i = 0; i < filter.length; i++) {
        var item = filter[i];
        if(item.name=='首付') {
            var pay = item.list;
            if(pay.length > 0) {
                for (var k = 0; k < pay.length; k++) {
                    html += '<li onclick="setFirstPay(this)" data-id="'+pay[k].id+'">'+pay[k].name+'<div class="line" style="left:-1.33rem"></div></li>';
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
    $('.list-filter-area').attr('class','list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src','./img/slatdown.png');
    $('#filter3').css('display','none');
    o.sfprice = $(obj).data('id'); 
    ajaxGetList(o);
}
//显示列表4
function getFilterTitle(obj) {
    if ($('#filter4-list').find('li').length==1) {
        var html = '';
        for (var i = 0; i < filter.length; i++) {
            var item = filter[i];
            if(item.name=='更多') {
                var list = item.list;
                if(list.length > 0) {
                    for (var a = 0; a < list.length; a++) {
                        var secondlist = list[a].list;
                        var innerhtml = '';
                        if(secondlist.length > 0) {
                            for (var b = 0; b < secondlist.length; b++) {
                                innerhtml += '<li onclick="setFilterItem(this)" data-id="'+secondlist[b].id+'">'+secondlist[b].name+'</li>';
                            }
                        }
                        html += '<li data-id="'+list[a].id+'" class="filter4-item"><div class="filter4-item-head"><strong>'+list[a].name+'</strong></div><div class="filter4-item-item"><ul class="clearfloat"><div id="filter4-item0"><li onclick="setFilterItem(this)">不限</li>'+innerhtml+'</div></ul></div></li>';
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
    if ($(obj).data('id')<3) { 
        o.sort = $(obj).data('id'); 
    } else {
        o.wylx = $(obj).data('id'); 
    }
    
}
$('#reset').click(function(){
    $('#filter4-list').find('li').removeClass('filter-filter4-button-active');
});
$('#ensure').click(function(){
    $('.list-filter-area').attr('class','list-filter-area list-filter-text');
    $('.list-filter-slat').attr('src','./img/slatdown.png');
    $('#filter4').css('display','none');
    ajaxGetList(o);
});
function delCom() {
    $('#companytag').empty();
    o.company = '';
    ajaxGetList(o);
}