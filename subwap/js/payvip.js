$(document).ready(function() {
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.add_vip_words);
        if(data.data.is_user==false) {
            $('.phonenum').html('请先登录');
            alert('请先登录');
        } else {
            var user = data.data.user;
            if(data.data.user_image!='')
                $('.head-img').attr('src',data.data.user_image);
            $('.phonenum').html(user.name+user.phone);
            if(user.vip_expire*100000>Date.parse(new Date())) {
                $('.status').html('您是会员账户');
            }
            
        }
    });
});
function findprices (obj) {
    $('#finp').html($(obj).find('.nowp').html());
}
$('.gotopay').click(function () {
    var qftype=new Object();
    // qftype.title='申请对接人费用';
    qftype.cover='';
    // qftype.num=1;

    qftype.title='成为会员支付';
    qftype.num=1;
    qftype.gold_cost=0;

    qftype.cash_cost=$('#finp').html()=='699'?0.1:0.2;
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
            QFH5.createOrder(10005,item,0,additem,12,function(state,data){
                order_id = data.order_id;
                QFH5.jumpPayOrder(order_id,function(state,data){
                    if(state==1){
                        alert('支付成功');
                        $.post("/api/plot/setVip", {
                                'num': 1,
                                'title': $('#finp').html()?1:2,
                            },
                            function(data, status) {
                                if (data.status == "success") {
                                    alert("操作成功！");
                                    location.href = 'my';
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