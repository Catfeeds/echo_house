var cuslistapp=angular.module("cuslist",[]);
cuslistapp.controller("cuslistCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:''
	}).then(function successCallback(response){

	},function errorCallback(response){

	});
});
function addCustomer(){
	location.href="addcustomer.html";
}