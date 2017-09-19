var cuslistapp=angular.module("cuslist",[]);
cuslistapp.controller("cuslistCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:'/api/plot/checkisZc'
	}).then(function successCallback(response){
		$scope.cuntomerlist=response.data.data;
	},function errorCallback(response){
		alert(response.data.msg);
		location.href="detail.html";
	});
	
});
function addCustomer(){
	location.href="addcustomer.html";
}
function turn(obj){
	var id=$(obj).attr("data-id");
	location.href="customerdetail.html?id="+id;
}