var app=angular.module("list",[]);
//顶部标签
app.controller('topCtrl', function($scope, $http) {
    $http({
        method: 'GET',
        url: '/api/tag/index?cate=wzlm'
    }).then(function successCallback(response) {
            var toplist='';
            $scope.toplist = response.data.data;
        }, function errorCallback(response) {
            // 请求失败执行代码
    });
  
});
//筛选列表
app.controller('filterCtrl', function($scope, $http) {
    $http({
        method: 'GET',
        url: '/api/tag/list?cate=plotFilter'
    }).then(function successCallback(response) {
            var filterlist='';
            $scope.filterlist = response.data.data;
        }, function errorCallback(response) {
            // 请求失败执行代码
    });
  
});
//初次加载列表
app.controller('listCtrl',function($scope,$http){
    $http({
        method:'GET',
        url:'/api/plot/list'
    }).then(function successCallback(response){
        var resdata=response.data.data.list;
        for (var i = 0; i < resdata.length; i++) {
            if (resdata[i].pay=='') {
                resdata[i].pay='暂无佣金方案';
            }           
        }
        var houselist='';
        $scope.houselist=response.data.data.list;
    },function errorCallback(response){

    });
});
