var hid='';
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
		params:{'id':hid}
	}).then(function successCallback(response){
		$scope.cusmessage=response.data.data;
		$scope.notelist=response.data.data.list;
	},function errorCallback(response){

	});
});
capp.controller("postCtrl",function($scope,$http) {
	var note=$('.remark-textarea').val();
	$http({
		method:'POST',
		url:'/api/plot/addSubPro',
		data:{note:note,status:20,sid:hid},
	}).then(function successCallback(response){
		$scope.cusmessage=response.data.data;
		$scope.notelist=response.data.data.list;
	},function errorCallback(response){

	});
});
function processOne(obj){
	$(obj).removeClass('processli-active');
	$(obj).addClass('processli-active');
}
function processTwo(obj){
	if ($(obj).prev().prev().hasClass('processli-active')) {
		$(obj).removeClass('processli-active');
		$(obj).addClass('processli-active');
	}
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}