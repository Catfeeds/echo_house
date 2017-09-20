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
		}else{
			status=$('.processli-active').length-1;
		}	
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
		if($(obj).hasClass('processli')){
			$('.remark').css('display','block');
		}
		$(obj).removeClass('processli');
		$(obj).addClass('processli-active');
		$('.process ul li').removeAttr("onclick");
		$('#tuidi').removeAttr("onclick");	
	}
}
function tuiding(){
	$('.remark').css('display','block');
	$('#tuidi').removeClass('tuiding');
	$('#tuidi').addClass('tuiding-active');
	$('#tuidi').removeAttr("onclick");	
	$('.process ul li').removeAttr("onclick");
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}