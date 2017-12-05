var sid='';
var remark=false;
var id = '';
$(document).ready(function(){
			
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined){
		sid=GetQueryString('id');
	}
});
var capp=angular.module("cApp",[]);
capp.controller("customerCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:'/api/plot/getSubInfo',
		params:{'id':GetQueryString('id')}
	}).then(function successCallback(response){
		$scope.cusmessage=response.data.data;
		// response.data.data.phone = '11';
		if(response.data.data.phone.indexOf('*')>-1){
			$('#phone').css('display','none');
		}else{
			$('#phone').attr('href','tel:'+response.data.data.phone);
		}
		// if(response.data.data.can_edit<1) {
		// 	$('.process ul li').removeAttr("onclick");
		// }
		if(response.data.data.sale=='') {
			$.get('/api/plot/getAcsales',function(data) {
				if(data.status=='success'&&data.data.length>0) {
					var list = data.data;
					var html = '';
					html += '<option value="0">请选择案场销售</option>';
					for (var i = 0; i < list.length; i++) {
						html += '<option value="'+list[i].id+'">'+list[i].name+'</option>';
					}
					$('.report-contacter').append(html);
				}
			});
		} else {
			$('.report-contacter').remove();
			$('.report-smalltag-title').after('<div style="border:none;top:0.5rem" class="report-contacter">'+response.data.data.sale+'</div>');
		}
		$scope.notelist=response.data.data.list;
		var status = response.data.data.status;
		if (status!='退定') {
			for (var i = 0; i < $('.process li').length; i++) {
				var a = $('.process li')[i];
				$(a).attr('class','processli-active');
				if($(a).html()==status) {
					break;
				}
			}
		}else{
			$('#tuidi').removeClass('tuiding');
			$('#tuidi').addClass('tuiding-active');
			$('.process ul li').removeAttr("onclick");
			$('#tuidi').removeAttr("onclick");	
		}	
		if(response.data.data.is_del=='1'){
			$('.remark').css('display','block');
		}else{
			$('.del').css('display','none');
		}
	},function errorCallback(response){

	});	
	//提交备注
	$scope.postmsg=function(){
		var note=$('.remark-textarea').val();
		var status='';
		if($('#tuidi').hasClass('tuiding-active')){
			status=6;
		}else if($('#genjin').hasClass('genjin-active')) {
			status=7;
		}else {
			status=id;
		}	
		$http({
			method:'POST',
			url:'/api/plot/addSubPro',
			data:$.param({note:note,status:status,sid:sid})  ,
			headers:{'Content-Type': 'application/x-www-form-urlencoded'}, 
		}).then(function successCallback(response){
			if(response.data.status=='success'){
				alert("提交成功！");
				location.reload();
			}
		},function errorCallback(response){

		});
	}
});
function process(obj){
	// if ($(obj).prev().prev().hasClass('processli-active')) {
		if($(obj).hasClass('processli')){
			$('.remark').css('display','block');
		}
		$(obj).removeClass('processli');
		$(obj).addClass('processli-active');
		// $('.process ul li').removeAttr("onclick");
		$('#tuidi').removeAttr("onclick");	
		$(obj).nextAll('li').attr('class','processli');
		$('.genjin').attr('class','genjin');
		$('.tuiding').attr('class','tuiding');
		id = $(obj).data('id');
	// }
}
function tuiding(){
	$('.remark').css('display','block');
	$('#tuidi').removeClass('tuiding');
	$('#tuidi').addClass('tuiding-active');
	$('#tuidi').removeAttr("onclick");	
	$('.process ul li').removeAttr("onclick");
}
function genjin(){
	$('.remark').css('display','block');
	$('#genjin').removeClass('genjin');
	$('#genjin').addClass('genjin-active');
	$('#genjin').removeAttr("onclick");	
	$('.process ul li').removeAttr("onclick");
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
function setSale() {
	$.get('/api/plot/addSale?sid='+sid+'&sale_uid='+$('.report-contacter').val(),function(data) {
		if(data.status=='success') {
			alert('成功指定案场销售');
			location.reload();
		}
	});
}
function spread(obj) {
	if ($(obj).hasClass('state-text-off')) {
		$(obj).removeClass('state-text-off');
		$(obj).addClass('state-text-on');
	} else {
		$(obj).removeClass('state-text-on');
		$(obj).addClass('state-text-off');
	}
}