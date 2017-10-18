var hid='';
var app=angular.module('mysubscribe',[]);
app.controller('listCtrl',function($scope,$http){
//获取列表
	$http({
		method:'GET',
		url:'/api/plot/list?save=1'
	}).then(function successCallback(response){
		$scope.list=response.data.data.list;
	},function errorCallback(response){

	});
//取消订阅
	$scope.canclesubscribe=function(obj){
		var r=confirm("确定取消收藏吗？");
		if(r==true){
			var hid=$(obj.target).attr('data-id');
			$(obj.target).parent().remove();
			$.get("/api/plot/addSave?hid="+hid,function(data,status){
	            alert(data.msg);      
			});
		}
	}
});