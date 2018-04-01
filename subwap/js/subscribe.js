var area='';
var street='';
var choose1='';
var choose2='';
var minprice='';
var maxprice='';
var changeornot=0;
var app=angular.module('subscribe',[]);
app.controller('subscribeCtrl',function($scope,$http){
//获取列表
  $http({
    method:'GET',
    url:'/api/tag/publishtags'
  }).then(function successCallback(response){
    $scope.filterlist0 = response.data.data[0].list;
    $scope.filterlist1 = response.data.data[1].list;
    $scope.provincelist = response.data.data[3].list;
  },function errorCallback(response){

  });
//二级菜单
  $scope.getprovince=function(){
    for (var i = 0; i < $scope.provincelist.length; i++) {
      if ($scope.provincelist[i].name==$('#province').val()) {
        $scope.citylist=$scope.provincelist[i].childAreas;
        area=$scope.provincelist[i].id;
      }
    }
    changeornot=1;
  }
  $scope.getcity=function(){
    for (var i = 0; i < $scope.citylist.length; i++) {
      if ($scope.citylist[i].name==$('#city').val()) {
        street=$scope.citylist[i].id;
      }
    }
  }
//点击按钮
  $scope.choose1=function(obj){
    $(obj.target).parent().children().removeClass('filter-filter4-button-active');
    $(obj.target).addClass('filter-filter4-button-active');
    choose1=$(obj.target).attr('data-id');   
    if (choose1!=0) {
      changeornot=1;
    }
  }
  $scope.choose2=function(obj){
    $(obj.target).parent().children().removeClass('filter-filter4-button-active');
    $(obj.target).addClass('filter-filter4-button-active');
    choose2=$(obj.target).attr('data-id');
    if (choose2!=0) {
      changeornot=1;
    }
  }
//提交
  $scope.postmsg=function(){
    if (changeornot=1) {
      minprice = $('#amount1').val();
      maxprice = $('#amount2').val();
      $http({
          method: 'POST',
          url: '/api/plot/addSubscribe',
          data: $.param({
              'SubscribeExt[area]': area,
              'SubscribeExt[street]': street,
              'SubscribeExt[minprice]': minprice,
              'SubscribeExt[maxprice]': maxprice,
              'SubscribeExt[wylx]': choose1,
              'SubscribeExt[zxzt]': choose2,
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      }).then(function successCallback(response) {
          if (response.data.status == 'success') {
              alert(response.data.msg);
              location.href="mysubscribe.html";
          } else {
              alert(response.data.msg);
          }
      }, function errorCallback(response) {

      });
    } else {
      alert('请至少进行一种筛选！');
    }   
  }
});
//JQuery UI Slider
$(function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 25,
      values: [ 0, 25 ],
      slide: function( event, ui ) {
        $( ".buxian" ).html(ui.values[ 0 ]+'k-'+ui.values[ 1 ]+'k');
        $( "#amount1" ).val(ui.values[ 0 ]);
        $( "#amount2" ).val(ui.values[ 1 ] );
        changeornot=1;
      }
    });
    $( "#amount1" ).val($( "#slider-range" ).slider( "values", 0 ));
    $( "#amount2" ).val($( "#slider-range" ).slider( "values", 1 ));
  });
//重置
function reset(){
  location.reload();
  }
