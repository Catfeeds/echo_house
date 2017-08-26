//头部列表



//筛选列表


//商品列表
// var app = new Vue({
//     el: "#app-house",
//     data: {
//         itemList: [],
//     },
//     mounted: function() {
//         this.getData();
//     },
//     methods: {
//         getData: function() {
//             var self = this;
//             this.$http.get("http://127.0.0.1/test.json").then(function(res) {
//                 var datalist = res.body.data.list;
//                 for (var i = 0, len = datalist.length; i < len; i++) {
//                     var selData = datalist[i];
//                     var part = datalist[i].name;
//                     self.itemList.push(selData);

//                 }
//             })
//         }
//     }
// });

//背景颜色高度
$(document).ready(function() 
    { 
        var winHeight=($(window).height()-93)/18.75;
        $('.filter-filter-bg').css({"height":winHeight+"rem"});
    });
var n=0;
//打开filter1一级导航栏
$('.list-filter-area1').click(function(){
    if(n%2==0){
        $('#filter1').css({"display":"block"});
        $('.list-filter-area1').css({"color":"#00b7ee"});
        $('#slat1').attr('src','./img/slatup.png');
        n+=1;
    }else{
        $('#filter1').css({"display":"none"});
        $('.list-filter-area1').css({"color":"#000000"});
        $('#slat1').attr('src','./img/slatdown.png');
        n+=1;
    }
});
//打开filter1的二级导航栏
$('.filter-filter1-left ul li').click(function(){
    $('.filter-filter1-left ul li').removeClass('filter1-left-active');
    $(this).addClass('filter1-left-active');
    $('.filter-filter1-right').css({"display":"block"});
});
//请求filter1筛选后的页面
$('.filter-filter1-right ul li').click(function(){
    $('.filter-filter1-right ul li').removeClass('filter1-right-active');
    $(this).addClass('filter1-right-active');
});
//打开filter2导航栏
$('.list-filter-area2').click(function(){
    if(n%2==0){
        $('#filter2').css({"display":"block"});
        $('.list-filter-area2').css({"color":"#00b7ee"});
        $('#slat2').attr('src','./img/slatup.png');
        n+=1;
    }else{
        $('#filter2').css({"display":"none"});
        $('.list-filter-area2').css({"color":"#000000"});
        $('#slat2').attr('src','./img/slatdown.png');
        n+=1;
    }
});
$('.filter-filter2 ul li').click(function(){
    $('.filter-filter2 ul li').removeClass('filter2-active');
    $(this).addClass('filter2-active');
});
//打开filter3导航栏
$('.list-filter-area3').click(function(){
    if(n%2==0){
        $('#filter3').css({"display":"block"});
        $('.list-filter-area3').css({"color":"#00b7ee"});
        $('#slat3').attr('src','./img/slatup.png');
        n+=1;
    }else{
        $('#filter3').css({"display":"none"});
        $('.list-filter-area3').css({"color":"#000000"});
        $('#slat3').attr('src','./img/slatdown.png');
        n+=1;
    }
});
$('.filter-filter3 ul li').click(function(){
    $('.filter-filter3 ul li').removeClass('filter3-active');
    $(this).addClass('filter3-active');
});