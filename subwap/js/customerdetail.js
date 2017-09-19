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
		url:''
	}).then(function successCallback(response){

	},function errorCallback(response){

	});
});
capp.controller("stateCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:''
	}).then(function successCallback(response){

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