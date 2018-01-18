var hid = '';
var kf_id = '';
$(document).ready(function(){
	$.get("/api/config/index",function(data){
		if (data.status=='success') {
			kf_id = data.data.kf_id;
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
	if(GetQueryString('hid')!=undefined) {
		hid = GetQueryString('hid');
		$.get("/api/plot/getNameFromId?id="+hid,function(data){
			if(data.status=='success') {
				$('#title').html(data.data);
			}
		});
	}
	// if(GetQueryString('title')!=undefined) {
	// 	$('#title').html(GetQueryString('title'));
	// }
});

function subthis() {
	var qftype=new Object();
	// qftype.title='申请对接人费用';
	qftype.cover='';
	// qftype.num=1;

	qftype.title=$('#housename').val();
	qftype.num=$('#housenum').val();
	qftype.gold_cost=0;

	qftype.cash_cost=qftype.title=='1个月'?99:268;
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
		// 	alert(data.msg);		
		// } else {
			QFH5.createOrder(10001,item,0,additem,12,function(state,data){
				if(state!=1) {
					 alert(data.error);
					} else {
						order_id = data.order_id;
				        QFH5.jumpPayOrder(order_id,function(state,data){
						    if(state==1){
						    	alert('支付成功');
						    	$.post("/api/plot/addMakert", {
								        'hid': hid,
								        'num': $('#housenum').val(),
								        'title': $('#housename').val(),
								    },
								    function(data, status) {
								        if (data.status == "success") {
								            alert("申请成功！");
								            location.href = 'detail.html?id='+hid;
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
					}
		    });
		// }
	// });
			
    
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
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
function callkf() {
     QFH5.jumpTalk(kf_id,'','');
}