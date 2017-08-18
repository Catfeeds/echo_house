/**
 * Created by Administrator on 2016/8/22.
 */
function callback(data) {
    if($('.image-div').length >= 28) {
        alert('最多选择28张图片');
    } else {
        // 指定区域出现图片
        var image_html = "<div class='image-div' style='width: 150px;display:inline-table;height:180px'><a href='javascript::void(0)' class='btn green btn-xs fm-btn' style='position: absolute;display:none'>已设置封面</a><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;margin-left: 94px;'><i class='fa fa-trash'></i></a><img src='" + data.msg.url + "' style='width: 120px;height: 90px'><input name='image_des[]' type='text' style='width: 80px'/><a class='btn btn-xs green' onclick='setFm(this)' style='width: 40px;'>封面</a><input type='hidden' class='trans_img' name='images[]' value='" + data.msg.pic + "'/><input type='hidden' class='fm'/></div>";
        $('.images-place').append(image_html);
    }
}
//设定封面，前四行是显示，后面两行给表单控件赋值
function setFm(obj) {
    $('.fm-btn').css('display', 'none');
    $('.fm').removeAttr('name');
    $('.fm').removeAttr('value');
    $(obj).parent().find('.fm-btn').css('display', 'block');
    $(obj).parent().find('.fm').attr('name', 'fm');
    $(obj).parent().find('.fm').attr('value', $(obj).parent().find('.trans_img').val());
}
//删除图片
function del_img(obj) {
    //将已选择的图片重设为可以选择
    var img = $(obj).parent().find('img').attr('src');
    $('.xqtp').find('img[src="' + img + '"]').parent().find('.ch_img').html('<a onclick="ch_img(this)" >点击选择</a>');
    $(obj).parent().remove();

}
//找到小区的30张图片
if($('#ResoldZfExt_hid').val() || $("#ResoldQzExt_hid").val()){
    images_init();
}
$('#ResoldZfExt_hid,#ResoldQzExt_hid').on('change',function () {
    $(".xqtp").html("");//清空当前所选图片
    images_init();
})
//将选择的图片显示在添加图片区域，已选的图片不能再次选择
function ch_img(obj) {
    if($('.image-div').length >= 28) {
        alert('最多选择28张图片');
    } else {
        var img = $(obj).parent().parent().find('img').attr('src');
        var ori_img = $(obj).parent().parent().find('.ori_image').val();
        var image_html = "<div class='image-div' style='width: 150px;display:inline-table;height:180px'><a href='javascript::void(0)' class='btn green btn-xs fm-btn' style='position: absolute;display:none'>已设置封面</a><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;margin-left: 94px;'><i class='fa fa-trash'></i></a><img src='" + img + "' style='width: 120px;height: 90px'><input name='image_des[]' type='text' style='width: 80px'/><a class='btn btn-xs green' onclick='setFm(this)' style='width: 40px;'>封面</a><input type='hidden' class='trans_img' name='images[]' value='" + ori_img + "'/><input type='hidden' class='fm'/></div>";
        $('.images-place').append(image_html);
        $(obj).replaceWith('<span style="color:gray">已选择</span>');
    } 
}

//监听小区图片div，若出现的节点图片与已选图片一致，则避免重复选择
$(".xqtp").bind('DOMNodeInserted', function (e) {
    $(".images-place img").each(function (index, element) {
        if ($(e.target).find('img').attr('src') == $(element).attr('src')) {
            $(e.target).find('.ch_img').html('<span style="color:gray">已选择</span>');
        }
    });
});
//根据hid展示小区图片
function images_init() {

    var hid = $('#ResoldZfExt_hid').val() || $("#ResoldQzExt_hid").val();
    $.ajax({
        'url': $('#getImgs').val(),
        'type': 'get',
        'data': {'hid': hid},
        'dataType': 'json',
        'success': function (data) {
            if (data.msg == 'success') {
                var obj = data.data;
                //小区地址
                $('#ResoldZfExt_address').val(data.addr);
                //图片区域
                for (var i = 0; i < obj.length; i++) {
                    html = '<div class="tp_info" style="display:inline-table;width: 120px;height: 120px;"><img style="width: 120px;height: 90px;" src="' + obj[i]['image'] + '"><div class="ch_img" style="width: 100px;margin-left: 30px"><a onclick="ch_img(this)" >点击选择</a></div><input type="hidden" class="ori_image" value="' + obj[i]['ori_image'] + '"/></div>';
                    $('.xqtp').append(html);
                }
            }
        }
    })
}
//特色、配置随房源类型切换
$('#ResoldQzExt_category').click(function(){
    var type = $('#ResoldQzExt_category').find('.checked').find('.radio-inline').val();
    switch (type) {
        case '1':
            $('#hid,#zfsppt,#esfzfsptype,#zfspkjyxm').css('display','none');
            $('#hid,#zfxzlpt,#esfzfxzltype').css('display','none');
            $('#hid,#qzchamber,#rent_type,#zfzzpt,#esfzfzztype').css('display','block');
            // $('.sp_hid').css('display','');
            // $('.sp_hid').css('display','');           
            break;
        case '2':
            $('#hid,#zfxzlpt,#esfzfxzltype').css('display','none');
            $('#hid,#qzchamber,#rent_type,#zfzzpt,#esfzfzztype').css('display','none');
            // $('.sp_hid').css('display','');
            $('#hid,#zfsppt,#esfzfsptype,#zfspkjyxm').css('display','block');
            // $('.sp_hid').css('display','none');
            break;
        case '3':
            $('#hid,#zfsppt,#esfzfsptype,#zfspkjyxm').css('display','none');
            $('#hid,#qzchamber,#rent_type,#zfzzpt,#esfzfzztype').css('display','none');
            $('#hid,#zfxzlpt,#esfzfxzltype').css('display','block');
            // $('.sp_hid').css('display','');
            // $('.xzl_hid').css('display','none');
            break;
    }

});

$(function(){
    setFm($('.isfm'));
});

