var id='';
var app=angular.module('mysubscribe',[]);
app.controller('listCtrl',function($scope,$http){
//获取列表
	$http({
		method:'GET',
		url:'/api/plot/getSubscribeList'
	}).then(function successCallback(response){
		$scope.list=response.data.data;
	},function errorCallback(response){

	});
//取消订阅
	$scope.canclesubscribe=function(obj){
		var r=confirm("确定取消订阅吗？");
		if(r==true){
			id=$(obj.target).parent().attr('data-id');
			$(obj.target).parent().remove();
			$.get("/api/plot/delSubscribe?id="+id,function(data,status){
	            alert(data.msg);    
			});
		}
	}
});