function callback(data){
    var $html = $($("#thumb-tpl").html());
    var $currentTab = $('.nav-tabs .active');
    var $uploadContainer =$('.tab-content .active').find('.form-container');

    $html.find(".uploaded-setcover").attr("onclick","setValue(this,'"+data.msg.pic+"')");
    $uploadContainer.append($html);

    var imgCount = $uploadContainer.find('.upimg-con').length; //当前图片的索引
    var baseInputName = 'img['+ $currentTab.data('tid') +']['+ imgCount +']';//.e.g img[601][1]

    //如果是户型图则增加表单内容
    if($currentTab.data('tag-name') == '户型图'){
        var tpl = $("#thumb-tpl-hx").html() ;
        $html.find('.formlist').append(tpl);
    }

    //图片地址处理
    $html.find('.uploaded-img').attr('src',data.msg.url);
    $html.find(".js-imgurl").attr("value",data.msg.pic);

    //表单名处理
    $html.find('.js-input').each(function(){
        $(this).attr('name', baseInputName + '['+ $(this).attr('name') +']');
    });

    $('.upimg').hover(function(){
        $(this).find('#shanchu').show();
        $(this).find('#fengmian').show();
    },function(){
        $(this).find('#shanchu').hide();
        $(this).find('#fengmian').hide();
    });
}

$(".upimg").hover(function(){
    $(this).find("#shanchu").show();
    $(this).find("#fengmian").show();
},function(){
    $(this).find("#shanchu").hide();
    $(this).find("#fengmian").hide();
});

$('#del_img').live('click',function(){
    $(this).closest('.upimg-con').remove();
});
function setValue(obj,val){
    // $('#cover').val(val);
    $(obj).closest('.tab-pane').find('.fm.on').removeClass('on').html('设为封面');
    $(obj).find('.fm').addClass('on').html('我是封面');
    $(obj).closest('.tab-pane').find('.js-iscover').val(0);
    $(obj).closest('.upimg').find('.js-iscover').val(1);
}
$('.radioButtons').on('click',function(){
    var val = $("input[type='radio']:checked").val();
    if(val == 2){
        $('#reason').css('display','block');
    }else{
        $('#reason').css('display','none');
    }
})
$('#tj').bind('click',function(){
    var val = $("input[type='radio']:checked").val();
    var reason = $('#album_reason').val();
    if(val==2 && reason==''){
        $('#err_reason').html('请输入未通过理由!');
        return false;
    }
});

//*************** 编辑 *****************

function callback2(data){
    var $con = $('#js-imgReplace');
    $con.find('.uploaded-img').attr('src',data.msg.url);
    $con.find(".js-imgurl").attr("value",data.msg.pic);
}

// 初始化Web Uploader
var uploaderyw_edit = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,
    paste: document.body,
    // swf文件路径
    swf: "/static/global/plugins/webuploader/Uploader.swf",

    // 文件接收服务端。
    server: "/api/file/upload/",
    fileSingleSizeLimit: 5000000,

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: {id:"#js-imgReplace",multiple:false},
    fileVal:"img",
    formData:{"filename":"img"},

    // 只允许选择图片文件。
    accept: {
        title: "Images",
        extensions: "gif,jpg,jpeg,bmp,png",
        mimeTypes: "image/*"
    }
});

//回调函数
uploaderyw_edit.on( "uploadSuccess", function( object, data ) {
    callbackyw2(data);
});
var callbackyw2 = function(data){callback2(data);}
