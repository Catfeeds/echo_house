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
            var $scope.filterlist = response.data.data;
            var $scope.filterlist1 = response.data.data[0].list;
            var $scope.filterlist2 = response.data.data[1].list;
            var $scope.filterlist3 = response.data.data[2].list;
            var $scope.filterlist4 = response.data.data[3].list;
        }, function errorCallback(response) {
            
    });  
});
//初次加载列表
app.controller('listCtrl',function($scope,$http){
    //判断是否登陆   
    $http({
        method:'GET',
        url:'/api/config/index'
    }).then(function successCallback(response){
        var is_user=false;
        is_user=response.data.data.is_user;
        $http({
            method:'GET',
            url:'/api/plot/list'
        }).then(function successCallback(response){
            var resdata=response.data.data.list;
            for (var i = 0; i < resdata.length; i++) {
                if (resdata[i].pay=='') {
                    resdata[i].pay='暂无佣金方案';
                }
                if (is_user==false){
                    resdata[i].pay='暂无权限查看';
                }          
            }
            var houselist='';
            $scope.houselist=response.data.data.list;
        },function errorCallback(response){

        });
    },function errorCallback(response){

    });
});
