var hid='';
var remark=false;
$(document).ready(function(){
	if(GetQueryString('id')!=''&&GetQueryString('id')!=undefined){
		hid=GetQueryString('id');
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
		$scope.notelist=response.data.data.list;
		var status = response.data.data.status;
		for (var i = 0; i < $('.processli').length; i++) {
			var a = $('.processli')[i];
			$(a).attr('class','processli processli-active');
			if($(a).html()==status) {
				break;
			}
		}
		if(response.data.data.is_del=='1'){
			$('.remark').css('display','block');
		}else{
			$('.del').css('display','none');
		}
	},function errorCallback(response){

	});	
	$scope.postmsg=function(){
		var note=$('.remark-textarea').val();
		var status=$('.processli-active').length-1;
		$http({
			method:'POST',
			url:'/api/plot/addSubPro',
			data:$.param({note:note,status:status,sid:hid})  ,
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
	if ($(obj).prev().prev().hasClass('processli-active')) {
		$(obj).removeClass('processli-active');
		$(obj).addClass('processli-active');
		$('.process ul li').removeAttr("onclick");
		if($(obj).hasClass('processli')){
			$('.remark').css('display','block');
		}	
	}
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}