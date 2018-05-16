var kw='';
var status='';
var myapp=angular.module("perlist",[]);
var closeId;
myapp.controller('perlistCtrl',function($scope,$http) {
    $http({
        method: 'GET',
        url: '/api/index/getQfUid'
    }).then(function successCallback(response) {
        $http({
            method: 'GET',
            url: '/api/plot/list?uid=1'
        }).then(function successCallback(response) {
                if (response.data.status=='error') {
                    // alert(response.data.msg);
                    if(response.data.msg=='未登录') {
                        location.href='personaladd.html';
                    }
                    // location.href='list.html';
                }else{
                    $scope.houselist = response.data.data.list;
                    console.log($scope.houselist)
                     $('.number').html(response.data.data.refresh_num); 
                    // $('.count').find('b').html(response.data.data.num); 
                    if (response.data.data.num==0) {
                        // $('.customerlist').css('background-color','#f5f5f5');
                        $('.nomore').css('display','block');
                    }else{
                        // $('.customerlist').css('background-color','white');
                        $('.nomore').css('display','none');
                    }
                }
            }, function errorCallback(response) {

        });
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
                if (response.data.data.num==0) {
                $('.nomore').css('display','block');
            }else{
                $('.nomore').css('display','none');
            } 
            }, function errorCallback(response) {

        });
    }

    $scope.checkId=function(id){
        console.log(id)
        location.href='detail.html?id=' + id;
    }

    // 置顶
    $scope.payTop=function(e,id){
        e.stopPropagation();
        e.preventDefault();
        $http({
            method: 'GET',
            url: '/api/plot/checkCanTop?hid='+id,
        }).then(function successCallback(response) {
            if (response.data.status=='error') {
                alert(response.data.msg);
            }else{
                location.href='houseStick.html?hid=' + id;
            }

        });
     
    }

    // 刷新
    $scope.refresh=function(e,id){
        e.stopPropagation();
        e.preventDefault();
        $http({
            method: 'GET',
            url: '/api/plot/setRefresh?hid='+ id
        }).then(function successCallback(response) {
            if (response.data.status=='error') {
                if(response.data.msg=='您的刷新次数不够，请前往购买'){
                    var refreshNum = $('.number').html();
                    alert(response.data.msg);
                    location.href='houseRefresh.html?num=' + refreshNum;  
                }else{
                     alert(response.data.msg);
                }
               
            }else{
                alert('刷新成功');
                location.reload();
            }

        });
     
    }

     // 购买刷新
    $scope.payRefresh=function(e){
        e.stopPropagation();
        e.preventDefault();
        var refreshNum = $('.number').html();
        location.href='houseRefresh.html?num=' + refreshNum;     
    }

    // 编辑
    $scope.edit=function(e,id){
        e.stopPropagation();
        e.preventDefault();
        $.confirm("修改房源后需要重新审核，确定进入编辑页?", "确认编辑?", function() {
            location.href='personalEdit.html?id=' + id;
        }, function() {
            //取消操作
        });
    }

    // 下架弹框
    $scope.showClose=function(e,id){
        e.stopPropagation();
        e.preventDefault();
        $('.tip-off').css('display','block');
        closeId = id;
    }

     // 下架
    $scope.close=function(){
        reason=reason==''?$('.tip-off-detail').val():reason;
        $http({
            method: 'GET',
            url: '/api/plot/downPlot',
            params:{
                'type':2,
                'hid':closeId,
                'note':reason
            }
        }).then(function successCallback(response) {
            if (response.data.status=='error') {
                alert(response.data.msg);
            }else{
                alert('申请下架成功，等待后台人员审核!');
            }

        });
        $('.tip-off').css('display','none');  
     
    }


    // 上架
    $scope.addHouse=function(e,id){
        e.stopPropagation();
        e.preventDefault();
        reason=reason==''?$('.tip-off-detail').val():reason;
        $http({
            method: 'GET',
            url: '/api/plot/downPlot',
            params:{
                'type':1,
                'hid':id,
                'note':'上架'
            }
        }).then(function successCallback(response) {
            if (response.data.status=='error') {
                alert(response.data.msg);
            }else{
                alert('申请上架成功，等待后台人员审核!');
            }

        });
     
    }

    $('.tip-off-shutdown').click(function(){
        $('.tip-off').css('display','none');
    });
    var reason='';
    $('.tip-off-select-window li').click(function(){
        $('.tip-off-select-window li').css('color','#000000');
        $('.tip-off-select-window li img').addClass('select-hide');
        $(this).css('color','#00B7F0');
        $(this).children().removeClass('select-hide');
        if($(this).index()==4){
            $('.tip-off-detail').css('display','block');
            $('.tip-off-detail').focus();
            reason='';
            $('.tip-off-select-window ul').scrollTo(0,0);
        }else{
            $('.tip-off-detail').css('display','none');
            reason=$(this).find('div').html();
        }
        
    });

        
});