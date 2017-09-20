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
	$scope.search=function(){
		var search=$('.list-search-frame-text').val();
		$http({
			method:'POST',
			url:'',
			data:$.param({search:search})  ,
			headers:{'Content-Type': 'application/x-www-form-urlencoded'}, 
		}).then(function successCallback(response){
			if(response.data.status=='success'){
				alert("提交成功！");
				location.reload();
			}
		},function errorCallback(response){

		});
	}
	$scope.turn=function(obj){
		location.href="customerdetail.html?id="+obj;
	}
	$scope.addCustomer=function(){
		location.href="addcustomer.html";
	}
});