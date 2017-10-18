var hid='';
var app=angular.module('mycollection',[]);
app.controller('listCtrl',function($scope,$http){
//获取列表
	$http({
		method:'GET',
		url:'/api/plot/list?save=1'
	}).then(function successCallback(response){
		$scope.list=response.data.data.list;
		if (response.data.data!='') {
			$scope.num=response.data.data.list.length;
		} else {
			$scope.num=0;
		}	
	},function errorCallback(response){

	});
//搜索
	$scope.search=function(){
		var search=$('.list-search-frame-text').val();
		$http({
			method:'GET',
			url:'/api/plot/checkIsZc?kw='+search,
		}).then(function successCallback(response){
			if(response.data.status=='success'){
				if (response.data.data!='') {
					$scope.num=response.data.data.list.length;
				} else {
					$scope.num=0;
				}
				$scope.list=response.data.data.list;	
			}
			$('.list-search-frame-text').val('');
			$('.list-search-frame-text').attr('placeholder',search?search:"请输入项目名称");
		},function errorCallback(response){
			
		});
	}
//取消收藏
	$scope.notsave=function($event){	
		var r=confirm("确定取消收藏吗？");
		if(r==true){
			$($event.target).parent().remove();
			hid=$($event.target).attr('data-id');
			$scope.num--;
			$.get("/api/plot/addSave?hid="+hid,function(data,status){
	            alert(data.msg);      
			});
		}
	}
});