$(document).ready(function(){
	$.get("/api/config/index",function(data){
		if (data.status=='success') {
			// for (var i = 0; i < data.data.length; i++) {
			// 	$('#housename').append('<option value="'++'">'++'</option>');
			// }
			if (data.data.add_market_words!=''&&data.data.add_market_words!=undefined) {
				$('.report-attention-text').html(data.data.add_market_words);
			}else{
				$('.report-attention').css('display','none');
			}		
		}
	});
});
var qftype=new Object();
qftype.title='申请对接人费用';
qftype.cover='';
qftype.num=1;
qftype.gold_cost=0;
qftype.cash_cost=0.1;
var qfarray=new Array();
qfarray[0]=qftype;
var address=new Object();
address.name='';
address.mobile='';
address.address='';
qftype.title=$('#housename').val();
qftype.num=$('#housenum').val();
var item=JSON.stringify(qfarray);
var additem=JSON.stringify(address);
var order_id='';
function subthis() {
	QFH5.createOrder(10001,item,0,additem,12,function(state,data){
        order_id = data.order_id;
        QFH5.jumpPayOrder(order_id,function(state,data){
		    if(state==1){
		    	alert('支付成功');
		        //支付成功
		    }else{
		        //支付失败、用户取消支付
		        alert(data.error);//data.error  string
		    }
		});
    });
    
}
// $('.submit-submit').click(function(){
// 	alert(1);
// 	QFH5.createOrder(10001,item,0,additem,12,function(state,data){
// 		alert(state);
//         order_id = data.order_id;
//     });
//     QFH5.jumpPayOrder(order_id,function(state,data){
// 		    if(state==1){
// 		    	alert('支付成功');
// 		        //支付成功
// 		    }else{
// 		        //支付失败、用户取消支付
// 		        alert(data.error);//data.error  string
// 		    }
// 		});
// });