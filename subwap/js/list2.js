var filter='';
var filterleft='';
$(document).ready(function(){  
    $('.filter-filter-bg').css({ "height": $(window).height()-$('.list-head-container').height() + "px" });
});
var app=angular.module("list",[]);
app.controller('filterCtrl', function($scope, $http) {
    //顶部文字
    $http({
        method: 'GET',
        url: '/api/tag/index?cate=wzlm'
    }).then(function successCallback(response) {
            $scope.toplist = response.data.data;
        }, function errorCallback(response) {

    });
    //筛选列表
    $http({
        method: 'GET',
        url: '/api/tag/list?cate=plotFilter'
    }).then(function successCallback(response) {
            $scope.filterlist = response.data.data;
            filter = response.data.data;
            $scope.filterlist1_left = response.data.data[0].list;
            filterleft = response.data.data[0].list;
            $scope.filterlist2 = response.data.data[1].list;
            $scope.filterlist3 = response.data.data[2].list;
            $scope.filterlist4 = response.data.data[3].list;
        }, function errorCallback(response) {
            
    });
    //显示filter1的area
    $scope.show_Area=false;
    $scope.show_Street=false;
    $scope.showArea=function(obj){
        switch(obj){
            case 0:
            $scope.show_Area=!$scope.show_Area;
            $scope.show_Price=false;
            $scope.show_Firstpay=false;
            $scope.show_More=false;
            break;
            case 1:
            $scope.show_Price=!$scope.show_Price;
            $scope.show_Area=false;
            $scope.show_Firstpay=false;
            $scope.show_More=false;
            break;
            case 2:
            $scope.show_Firstpay=!$scope.show_Firstpay;
            $scope.show_Area=false;
            $scope.show_Price=false;
            $scope.show_More=false;
            break;
            case 3:
            $scope.show_More=!$scope.show_More;
            $scope.show_Area=false;
            $scope.show_Price=false;
            $scope.show_Firstpay=false;
            break;
        }    
    }
    $scope.showStreet=function(obj){
        console.log(obj);
        for (var i = 0; i < filterleft.length; i++) {
            if (filterleft[i].name==obj) {
                $scope.filterlist1_right=filterleft[i].childAreas;
            }
        }
        $scope.show_Street=!$scope.show_Street;
    }   
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
            $scope.num=response.data.data.num;
            var resdata=response.data.data.list;
            for (var i = 0; i < resdata.length; i++) {
                if (resdata[i].pay=='') {
                    resdata[i].pay='暂无佣金方案';
                }
                if (is_user==false){
                    resdata[i].pay='暂无权限查看';
                }          
            }
            $scope.houselist=response.data.data.list;
        },function errorCallback(response){

        });
    },function errorCallback(response){

    });
});