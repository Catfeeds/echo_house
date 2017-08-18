UE.registerUI('dialog',function(editor,uiName){
   var tpl="";
tpl += "   <div class=\"form-horizontal\">";
tpl += "      <div class=\"form-group\">";
tpl += "         <label class=\"col-md-2 control-label\">子标题<\/label>";
tpl += "         <div class=\"col-md-6\">";
tpl += "            <input class=\"form-control\" id=\"child-titles-title\" type=\"text\" maxlength=\"50\" value=\"\" \/>";
tpl += "         <\/div>";
tpl += "      <\/div>";
tpl += "";
tpl += "      <div class=\"form-group\">";
tpl += "         <label class=\"col-md-2 control-label\">楼盘<\/label>";
tpl += "         <div class=\"col-md-6\">";
tpl += "            <input class=\"form-control child-titles-house\" type=\"text\" maxlength=\"50\" value=\"\" \/>";
tpl += "         <\/div>";
tpl += "      <\/div>";
tpl += "   <\/div>";



    //参考addCustomizeButton.js
    var btn = new UE.ui.Button({
      //   name:'dialogbutton' + uiName,
        name:'分页',
        title:'添加分页',
        //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
        cssRules :'background-position: -460px -40px;',
        onclick:function () {


            //渲染dialog
            bootbox.dialog({
               message: tpl,
               title: '添加子标题',
               buttons: {
                  danger: {
                     label: "取消",
                     className: "btn-danger",
                     callback: function() {

                     }
                  },
                  main: {
                     label: "插入",
                     className: "btn-primary",
                     callback: function() {
                        var hid = $('.child-titles-house').select2('val');
                        var title = $('#child-titles-title').val();

//                        var insert_data = '[page]'+title+','+hid+'[/page]';
                        var insert_data = '[page][title]'+title+'[/title][hid]'+hid+'[/hid][/page]';
                        editor.execCommand( 'inserthtml', insert_data);
                     }
                  }
               }
            });


            $('.child-titles-house').select2({
               ajax: getHousesAjax,
               language: 'zh-CN',
            });
        }
    });

    return btn;
}/*index 指定添加到工具栏上的那个位置，默认时追加到最后,editorId 指定这个UI是那个编辑器实例上的，默认是页面上所有的编辑器都会添加这个按钮*/);
