var myapp=angular.module("perlist",[]);
myapp.controller('perlistCtrl',function($scope,$http) {
	$http({
        method: 'GET',
        url: '/api/plot/list'
    }).then(function successCallback(response) {
            $scope.houselist = response.data.data.list;
        }, function errorCallback(response) {

    });
});