var cuslistapp=angular.module("pay",[]);
cuslistapp.controller("payCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:''
	}).then(function successCallback(response){
		$scope.cuntomerlist=response.data.data.list;		
	},function errorCallback(response){

	});
});