var cuslistapp=angular.module("cuslist",[]);
cuslistapp.controller("cuslistCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:'/api/plot/checkIsZc'
	}).then(function successCallback(response){
		if(response.data.status=='error') {
			alert('暂无权限，请联系客服开通');
		}else
			$scope.cuntomerlist=response.data.data;
		
	},function errorCallback(response){
	});
	$scope.turn=function(obj){
		location.href="customerdetail.html?id="+obj;
	}
	$scope.addCustomer=function(){
		location.href="addcustomer.html";
	}
});