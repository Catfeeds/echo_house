/**
 * 二手房发布页面交互所用js，两个交互功能：
 * 1. 房源类型切换时 对应子类型、对应房源特色和对应房源配置的切换
 * 2. 选择已有小区时，出现对应楼盘图片，可以对其进行选择、删除，并显示在二手房图片区域
 * @author  steven.allen
 * @date 2016-08-18
 */

function callback(data){
    if($('.image-div').length >= 28) {
        alert('最多选择28张图片');
    } else {
        // 指定区域出现图片
        var html = "";
        image_html = "<div class='image-div' style='width: 150px;display:inline-table;height:180px'><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;margin-left: 94px;'><i class='fa fa-trash'></i></a><img src='"+data.msg.url+"' style='width: 120px;height: 90px'><input name='image_des[]' type='text' style='width: 120px'></input><input type='hidden' class='trans_img' name='images[]' value='"+data.msg.pic+"'></input></div>";
        $('.images-place').append(image_html);
    }
}
//设定封面，前四行是显示，后面两行给表单控件赋值
function setFm(obj)
{
    $('.fm-btn').css('display','none');
    $('.fm').removeAttr('name');
    $('.fm').removeAttr('value');
    $(obj).parent().find('.fm-btn').css('display','block');
    $(obj).parent().find('.fm').attr('name','fm');
    $(obj).parent().find('.fm').attr('value',$(obj).parent().find('.trans_img').val());
}
//删除图片
function del_img(obj)
{
    //将已选择的图片重设为可以选择
    img = $(obj).parent().find('img').attr('src');
    $('.xqtp').find('img[src="'+img+'"]').parent().find('.ch_img').html('<a onclick="ch_img(this)" >点击选择</a>');
    $(obj).parent().remove();

}
//找到小区的30张图片
$('#ResoldEsfExt_hid,#ResoldQgExt_hid').change(function(){
    var hid = $('#ResoldEsfExt_hid,#ResoldQgExt_hid').val();
    var arr = [];
    $.ajax({
        'url' : $('#getImgs').val(),
        'type' : 'get',
        'data' : {'hid':hid},
        'dataType' : 'json',
        'success' : function( data ) {
            if( data.msg = 'success' ){
                var i = 0;
                obj = data.data;
                //小区地址
                $('#ResoldEsfExt_address').val(data.addr);
                //先移除现有的楼盘图片，后增加
                $('.xqtp').children().remove();
                //图片区域
                for(i = 0;i<obj.length;i++)
                {
                    html = '<div class="tp_info" style="display:inline-table;width: 120px;height: 120px;"><img style="width: 120px;height: 90px;" src="'+obj[i]['image']+'"><div class="ch_img" style="width: 100px;margin-left: 30px"><a onclick="ch_img(this)" >点击选择</a></div><input type="hidden" class="ori_image" value="'+obj[i]['ori_image']+'"></input></div>';
                    $('.xqtp').append(html);
                }
                }
            }
    });
    
});
//将选择的图片显示在添加图片区域，已选的图片不能再次选择
function ch_img(obj){
    if($('.image-div').length >= 28) {
        alert('最多选择28张图片');
    } else {
       img = $(obj).parent().parent().find('img').attr('src');
        ori_img = $(obj).parent().parent().find('.ori_image').val();
        image_html = "<div class='image-div' style='width: 150px;display:inline-table;height:180px'><a href='javascript::void(0)' class='btn green btn-xs fm-btn' style='position: absolute;display:none'>已设置封面</a><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;margin-left: 94px;'><i class='fa fa-trash'></i></a><img src='"+img+"' style='width: 120px;height: 90px'><input name='image_des[]' type='text' style='width: 80px'></input><a class='btn btn-xs green' onclick='setFm(this)' style='width: 40px;'>封面</a><input type='hidden' class='trans_img' name='images[]' value='"+ori_img+"'></input><input type='hidden' class='fm'></input></div>";
        $('.images-place').append(image_html);

        $(obj).replaceWith('<span style="color:gray">已选择</span>'); 
    }
        
}

//监听小区图片div，若出现的节点图片与已选图片一致，则避免重复选择
$(".xqtp").bind('DOMNodeInserted', function(e) {  
     $(".images-place img").each(function(index, element) {    
        if($(e.target).find('img').attr('src')==$(element).attr('src'))
        {
            $(e.target).find('.ch_img').html('<span style="color:gray">已选择</span>');
        }
    });
});  

//子类型div显示
$(document).ready(function(){
    //初始化楼盘图片显示
    var hid = $('#ResoldEsfExt_hid,#ResoldQgExt_hid').val();
    if(hid != '0'){
        $.ajax({
        'url' : $('#getImgs').val(),
        'type' : 'get',
        'data' : {'hid':hid},
        'dataType' : 'json',
        'success' : function( data ) {
            if( data.msg = 'success' ){
                var i = 0;
                obj = data.data;
                //小区地址
                $('#ResoldEsfExt_address').val(data.addr);
                //图片区域
                for(i = 0;i<obj.length;i++)
                {
                    html = '<div class="tp_info" style="display:inline-table;width: 120px;height: 120px;"><img style="width: 120px;height: 90px;" src="'+obj[i]['image']+'"><div class="ch_img" style="width: 100px;margin-left: 30px"><a onclick="ch_img(this)" >点击选择</a></div><input type="hidden" class="ori_image" value="'+obj[i]['ori_image']+'"></input></div>';
                    $('.xqtp').append(html);
                }
                }
            }
        });
    }


    //初始化房源子类型，根据房源类型的值确定显示与否
    var type = $('#ResoldEsfExt_category,#ResoldQgExt_category').find('.checked').find('.radio-inline').val();
    switch (type) {
        case '1':
           $('.zz').css('display','block');
           $('.zzpt').css('display','block');
           $('.zzts').css('display','block');
            break;
        case '2':
         $('.wyf').css('display','block');
           $('.sp').css('display','block');
            $('.sppt').css('display','block');
            $('.spts').css('display','block');
            break;
        case '3':
        $('.wyf').css('display','block');
           $('.xzl').css('display','block');
            $('.xzlpt').css('display','block');
            $('.xzlts').css('display','block');
            break;
    }

    setFm($('.isfm'));

});

//特色、配置随房源类型切换
$('#ResoldEsfExt_category,#ResoldQgExt_category').click(function(){
    $('.sp').css('display','none');
    $('.peizhi').css('display','none');
     $('.tese').css('display','none');
    $('.esf_type').css('display','none');
    $('.wyf').css('display','none');

    $('.peizhi').find('.checked').find('input').prop("checked",false);
    $('.tese').find('.checked').find('input').prop("checked",false);
    $('.esf_type').find('.checked').find('input').prop("checked",false);
    $('.sp').find('.checked').find('input').prop("checked",false);
    var type = $('#ResoldEsfExt_category,#ResoldQgExt_category').find('.checked').find('.radio-inline').val();
    switch (type) {
        case '1':
            $('.sp_hid').css('display','');
            $('.sp_hid').css('display','');
            $('.zz').css('display','block');
            $('.zzpt').css('display','block');
            $('.zzts').css('display','block');            
            break;
        case '2':
            $('.sp_hid').css('display','none');
            $('.wyf').css('display','block');
            $('.sp').css('display','block');
            $('.sppt').css('display','block');
            $('.spts').css('display','block');
            break;
        case '3':
            $('.sp_hid').css('display','');
            $('.xzl_hid').css('display','none');
            $('.wyf').css('display','block');
            $('.xzl').css('display','block');
            $('.xzlpt').css('display','block');
            $('.xzlts').css('display','block');
            break;
    }

});
