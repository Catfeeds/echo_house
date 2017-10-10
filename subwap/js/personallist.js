var kw='';
var status='';
var myapp=angular.module("perlist",[]);
myapp.controller('perlistCtrl',function($scope,$http) {
	$http({
        method: 'GET',
        url: '/api/plot/list?uid=1'
    }).then(function successCallback(response) {
        	if (response.data.status=='error') {
        		alert(response.data.msg);
        		location.href='list.html';
        	}else{
                $scope.houselist = response.data.data.list;
                $('.count').find('b').html(response.data.data.num); 
        	}
        }, function errorCallback(response) {

    });
    $scope.search=function() {
        $('.count').find('b').html('0'); 
        kw=$('.list-search-frame-text').val();
        status=$('.statusselect').val();
        $http({
            method: 'GET',
            url: '/api/plot/list?uid=1?kw='+kw+'&status='+status,
        }).then(function successCallback(response) {    
                $scope.houselist = response.data.data.list;
                $('.count').find('b').html(response.data.data.num); 
            }, function errorCallback(response) {

        });
    }
        
});