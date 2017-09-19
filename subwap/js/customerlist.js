var cuslistapp=angular.module("cuslist",[]);
cuslistapp.controller("cuslistCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:'/api/plot/checkIsZc'
	}).then(function successCallback(response){
		$scope.cuntomerlist=response.data.data;
	},function errorCallback(response){
		alert(response.data.msg);
		location.href="list.html";
	});
	$scope.turn=function(obj){
		location.href="customerdetail.html?id="+obj;
	}
	$scope.addCustomer=function(){
		location.href="addcustomer.html";
	}
});