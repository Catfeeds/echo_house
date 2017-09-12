var qftype=new Object();
qftype.title='申请对接人费用';
qftype.cover='';
qftype.num=1;
qftype.gold_cost=0;
qftype.cash_cost=0.1;
var qfarray=new Array();
qfarray[0]=qftype;
var itemO=JSON.stringify(qfarray);
var address=new Object();
var item=JSON.stringify(itemO);
address.name='';
address.mobile='';
address.address='';
$(document).ready(function(){
	$.get("",function(data){
		if (data.status=='success') {
			for (var i = 0; i < data.data.length; i++) {
				$('#housename').append('<option value="'++'">'++'</option>');
			}
			for (var i = 0; i < data.data.length; i++) {
				$('#housenum').append('<option value="'++'">'++'</option>');
			}
			if () {
				$('.report-attention-text').html();
			}else{
				$('.report-attention').css('display','none');
			}		
		}
	});
});
$('.submit-submit').click(function(){
	qftype.title=$('#housename').val();
	qftype.num=$('#housenum').val();
	QFH5.createOrder(10001,item,0,address,12,function(state,data){
        $.post("",{

        },function(data,status){
        	'':;
        	'':;
        });
    });
});