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








// 客户：
// 姓名
// 手机号


// 中介信息：
// 姓名
// 手机号
// 所在公司