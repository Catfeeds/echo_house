var num = 0;
var day = 30;
var hid = GetQueryString('hid');

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
$(document).ready(function() {
    
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.add_vip_words);
        if(data.data.is_user==false) {
            $('.phonenum').html('请先登录');
            alert('请先登录');
        } else {
            $.get('/api/plot/getOldExpire',function(data) {
                if(data.status=='success') {
                    num = data.data;
                }
            });
            var user = data.data.user;
            if(data.data.user_image!='')
                $('.head-img').attr('src',data.data.user_image);
            $('.phonenum').html(user.name);
            // console.log(user.vip_expire*1000);
            // console.log(Date.parse(new Date()));
            if(user.vip_expire*1000>Date.parse(new Date())) {
                $('.status').html('您是会员账户，到期时间为：'+formatDateTime(user.vip_expire));
            }
            
        }
    });
    findprices($('#recom'));
});
function formatDateTime(timeStamp) {   
    var date = new Date();  
    date.setTime(timeStamp * 1000);  
    var y = date.getFullYear();      
    var m = date.getMonth() + 1;      
    m = m < 10 ? ('0' + m) : m;      
    var d = date.getDate();      
    d = d < 10 ? ('0' + d) : d;      
    var h = date.getHours();    
    h = h < 10 ? ('0' + h) : h;    
    var minute = date.getMinutes();    
    var second = date.getSeconds();    
    minute = minute < 10 ? ('0' + minute) : minute;      
    second = second < 10 ? ('0' + second) : second;     
    return y + '-' + m + '-' + d;      
}; 
function findprices (obj) {
    $('.vipli').attr('class','tag vipli');
    $(obj).attr('class','active vipli');
    var nownum = $(obj).find('.nowp').html();
    
    // if(num>0){
    //     $('#note').html(nownum+'-'+num);
    //     if(nownum.indexOf(',')>-1) {
    //         $('#finp').html(1099-num);
    //     }else
    //         $('#finp').html($(obj).find('.nowp').html()-num);
    // } else {
        if(nownum.indexOf(',')>-1) {
            $('#finp').html('1099');
        } else {
            $('#finp').html($(obj).find('.nowp').html());
            day =  $(obj).find('.tittle').html();
        }
    // }
}
$('.gotopay').click(function () {
    var qftype=new Object();
    // qftype.title='申请对接人费用';
    qftype.cover='';
    // qftype.num=1;

    qftype.title='置顶支付';
    qftype.num=1;
    qftype.gold_cost=0;

    qftype.cash_cost=$('#finp').html();
    var qfarray=new Array();
    qfarray[0]=qftype;
    var address=new Object();
    address.name='';
    address.mobile='';
    address.address='';
    var item=JSON.stringify(qfarray);
    var additem=JSON.stringify(address);
    var order_id='';
    // $.get("/api/plot/checkMarket?hid="+hid,function(data){
        // if (data.status=='error') {
        //  alert(data.msg);        
        // } else {
            QFH5.createOrder(10007,item,0,additem,12,function(state,data){
                order_id = data.order_id;
                QFH5.jumpPayOrder(order_id,function(state,data){
                    if(state==1){
                        alert('支付成功');
                        $.get("/api/plot/setTop", {
                                'hid': hid,
                                'days': day,
                            },
                            function(data, status) {
                                if (data.status == "success") {
                                    alert("操作成功！");
                                    // location.href = 'my';
                                    history.back();
                                } else {
                                    alert(data.msg);
                                }
                            }
                        );
                        //支付成功
                    }else{
                        //支付失败、用户取消支付
                        alert(data.error);//data.error  string
                    }
                });
            });
        // }
    // });
            
    
});