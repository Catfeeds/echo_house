var cuslistapp=angular.module("cuslist",[]);
cuslistapp.controller("cuslistCtrl",function($scope,$http) {
	$http({
		method:'GET',
		url:'/api/index/getQfUid'
	}).then(function successCallback(response){
		$http({
			method:'GET',
			url:'/api/plot/checkIsSale'
		}).then(function successCallback(response){
			if(response.data.status=='error') {
				alert('暂无权限，请联系管理员开通');
				history.back();
			}else{
				$('.count b').html(response.data.data.num);
				if (response.data.data.num==0) {
					$('.customerlist').css('background-color','#f5f5f5');
	            $('.nomore').css('display','block');
	        }else{
	            $('.nomore').css('display','none');
	        }
				$scope.cuntomerlist=response.data.data.list;
			}
			
		},function errorCallback(response){

		});
	},function errorCallback(response){

	});
		
	$scope.search=function(){
		var search1=$('.list-search-frame-text').val();
		var status1=$('.statusselect').val();
		$http({
			method:'GET',
			url:'/api/plot/checkIsZc?kw='+search1+'&status='+status1,
		}).then(function successCallback(response){
			if(response.data.status=='success'){
				$('.count b').html(response.data.data.num);
				if (response.data.data.num==0) {
	            $('.nomore').css('display','block');
	        }else{
	            $('.nomore').css('display','none');
	        }
				$('#ul1').css('display','none');
				$('#ul2').css('display','block');
				$scope.searchlist=response.data.data.list;	
			}
			$('.list-search-frame-text').val('');
			$('.list-search-frame-text').attr('placeholder',search1?search1:"请输入客户姓名/手机号");
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