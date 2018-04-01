
var $list = $('#fileList');
var nameCover = $('#fileList').data('name-cover');
var nameVal = $('#fileList').data('name-val');
var thumbnailWidth = 100;
var thumbnailHeight = 100;

function callback2(data,file){
    var $li = $(
            '<div id="' + file.id + '" class="file-item thumbnail">' +
                '<img>' +
                '<div class="coverBadge">我是封面</div>' +
                '<input type="hidden" name="'+nameVal+'" class="hiddenPicVal" value="'+data.msg.pic+'">'+
                '<input type="hidden" name="'+nameCover+'" class="hiddenIsCover" value="0">'+
            '</div>'
            ),
        $img = $li.find('img');

        $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<div class="setCover" href="javascript:;">设为封面</div>'+
                        '</div>').appendTo( $li ),

    $list.append( $li );

    // 创建缩略图
    // 如果为非图片文件，可以不用调用此方法。
    // thumbnailWidth x thumbnailHeight 为 100 x 100
    uploaderyw.makeThumb( file, function( error, src ) {
        if ( error ) {
            $img.replaceWith('<span>不能预览</span>');
            return;
        }

        $img.attr( 'src', src );
    }, thumbnailWidth, thumbnailHeight );
}

// 初始化Web Uploader
var uploaderyw = WebUploader.create({

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
   pick: {id:"#picker",multiple:false},
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
uploaderyw.on( "uploadSuccess", function( object, data ) {
   callback2(data,object);
});

//删除
$(document).on('click','.file-panel .cancel',function(){
   $(this).closest('.file-item').remove();
});

//设为封面
$(document).on('click','.setCover',function(){
   var $setCoverBtn = $(this);
   var $fileitem = $setCoverBtn.closest('.file-item');
   var coverUrl = $fileitem.find('.hiddenPicVal').val();

   $('.hiddenIsCover').val(0);
   $('.setCover').show();

   $setCoverBtn.hide();
   $('.coverBadge.show').removeClass('show');

   $fileitem.find('.coverBadge').addClass('show');
   $fileitem.find('.hiddenIsCover').val(1);

   $(this).closest('#uploader').find('.coverUrl').val( coverUrl );
});

$(document).on('mouseover mouseout','.thumbnail',function(event){
   if(event.type == "mouseover"){
      $(this).find('.file-panel').height(25);
   }else if(event.type == "mouseout"){
      $(this).find('.file-panel').height(0);
   }

});
