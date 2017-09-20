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
		$scope.notelist=response.data.data.list;
		var status = response.data.data.status;
		for (var i = 0; i < $('.processli').length; i++) {
			var a = $('.processli')[i];
			$(a).attr('css','processli-active');
			if($(a).html()==status) {
				break;
			}
		}
	},function errorCallback(response){

	});	
	$scope.postmsg=function(){
		var note=$('.remark-textarea').val();
		var status=$('.processli-active').length-1;
		$http({
			method:'POST',
			url:'/api/plot/addSubPro',
			data:{note:note,status:status,sid:hid},
		}).then(function successCallback(response){
			if(response.status=='success'){
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
		$('.remark-textarea').empty();
		$('.remark').css('display','block');
	}
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}