//头部列表



//筛选列表


//商品列表
var app = new Vue({
    el: "#app-house",
    data: {
        itemList: [],
    },
    mounted: function() {
        this.getData();
    },
    methods: {
        getData: function() {
            var self = this;
            this.$http.get("http://house.com/api/plot/list").then(function(res) {
                var datalist = res.body.data.list;
                for (var i = 0, len = datalist.length; i < len; i++) {
                    var selData = datalist[i];
                    var part = datalist[i].name;
                    self.itemList.push(selData);

                }
            })
        }
    }
});
//背景颜色高度
$(document).ready(function() 
    { 
        var winHeight=($(window).height()-93)/18.75;
        $('.filter-filter-bg').css({"height":winHeight+"rem"});
    });
//head导航选中样式
$('#app-head ul li').click(function(){
    $('#app-head ul li').removeClass('list-head-item-active');
    $(this).addClass('list-head-item-active');
});
//筛选导航样式改变、打开filter1一级导航栏
$('.list-filter li').click(function(){
    if ($(this).is('.list-filter-area-active')) {
        $(this).removeClass('list-filter-area-active');
        $(this).find('img').attr('src','./img/slatdown.png');
    }else{
        $('.list-filter li').removeClass('list-filter-area-active');
        $('.list-filter-slat').attr('src','./img/slatdown.png'); 
        $(this).addClass('list-filter-area-active');
        $(this).find('img').attr('src','./img/slatup.png');
    }
    if($('.list-filter li:eq(0)').is('.list-filter-area-active')){
        $('#filter1').css({"display":"block"});
    }else{
        $('#filter1').css({"display":"none"});
    }
    if($('.list-filter li:eq(1)').is('.list-filter-area-active')){
        $('#filter2').css({"display":"block"});
    }else{
        $('#filter2').css({"display":"none"});
    } 
    if($('.list-filter li:eq(2)').is('.list-filter-area-active')){
        $('#filter3').css({"display":"block"});
    }else{
        $('#filter3').css({"display":"none"});
    } 
    if($('.list-filter li:eq(3)').is('.list-filter-area-active')){
        $('#filter4').css({"display":"block"});
    }else{
        $('#filter4').css({"display":"none"});
    }   
});
//打开filter1的二级导航栏
$('.filter-filter1-left ul li').click(function(){
    $('.filter-filter1-left ul li').removeClass('filter1-left-active');
    $(this).addClass('filter1-left-active');
    $('.filter-filter1-right').css({"display":"block"});
});
//点击不限右边消失
$('.filter-filter1-left ul li').first().click(function(){
    $('.filter-filter1-right').css({"display":"none"});
});
//请求filter1筛选后的页面
$('.filter-filter1-right ul li').click(function(){
    $('.filter-filter1-right ul li').removeClass('filter1-right-active');
    $(this).addClass('filter1-right-active');
});
//filter2的子目录点击
$('.filter-filter2 ul li').click(function(){
    $('.filter-filter2 ul li').removeClass('filter2-active');
    $(this).addClass('filter2-active');
});
//filter3的子目录点击
$('.filter-filter3 ul li').click(function(){
    $('.filter-filter3 ul li').removeClass('filter3-active');
    $(this).addClass('filter3-active');
});
//filter4的子目录点击
$('.filter-filter4 ul li div ul li').click(function(){
    $(this).parent().children().removeClass('filter-filter4-button-active');
    $(this).addClass('filter-filter4-button-active');
});
//确定和重置按钮
$('.filter-filter4-buttom-button').click(function(){
    $('.filter-filter4-buttom-button').removeClass('filter-filter4-button-active');
    $(this).addClass('filter-filter4-button-active');
});
//search页面
$('.list-head-img').click(function(){
    $('.list-search').css({"display":"block"});
});
$('.list-search-cancle').click(function(){
    $('.list-search').css({"display":"none"});
});