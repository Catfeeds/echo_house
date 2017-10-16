<?php
$this->pageTitle = $house->title.'相册新建/编辑';
$this->breadcrumbs = array($this->controllerName.'管理', $this->pageTitle);
?>
<?php $this->widget('ext.ueditor.UeditorWidget',array('id'=>'ArticleExt_content','options'=>"toolbars:[['fullscreen','source','undo','redo','|','customstyle','paragraph','fontfamily','fontsize'],
        ['bold','italic','underline','fontborder','strikethrough','superscript','subscript','removeformat',
        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain','|',
        'forecolor','backcolor','insertorderedlist','insertunorderedlist','|',
        'rowspacingtop','rowspacingbottom', 'lineheight','|',
        'directionalityltr','directionalityrtl','indent','|'],
        ['justifyleft','justifycenter','justifyright','justifyjustify','|','link','unlink','|',
        'insertimage','emotion','scrawl','insertvideo','music','attachment','map',
        'insertcode','|',
        'horizontal','inserttable','|',
        'print','preview','searchreplace']]")); ?>
<?php $form = $this->beginWidget('HouseForm', array('htmlOptions' => array('class' => 'form-horizontal'))) ?>
<div class="form-group">
<div class="col-md-2  control-label">选择图片</div>
<div class="col-md-4">
<?php $this->widget('FileUpload',array('inputName'=>'img','multi'=>true,'callback'=>'function(data){callback(data);}')); ?>
                    <div class="form-group images-place" style="margin-left: 0">
                  <?php if($infos) foreach ($infos as $key => $v) {?>
                      <div class='image-div' style='width: 150px;display:inline-table;height:180px'><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;'><i class='fa fa-trash'></i></a><img src='<?=ImageTools::fixImage($v->url)?>' style='width: 150px;height: 120px'><select style='width: 120px' name="TkExt[type][]"><?php foreach (Yii::app()->params['imageTag'] as $m => $n) {?>
                        <option value="<?=$m?>" <?=$v->type==$m?'selected':''?>><?=$n?></option>
                      <?php } ?></select><input type='hidden' class='trans_img' name='TkExt[album][]' value='<?=$v->url?>'></input><input type="text" style="width: 30px;height:24px" value="<?=$v->sort?>" name="TkExt[sort][]"></div>
                  <?php }?>
                </div>
                </div></div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <button type="submit" class="btn green">保存</button>
            <?php echo CHtml::link('返回',$this->createUrl('list'), array('class' => 'btn default')) ?>
        </div>
    </div>
</div>

<?php $this->endWidget() ?>

<?php
$js = "

    var getHousesAjax =
     {
        url: '".$this->createUrl('/admin/plot/AjaxGetHouse')."',"."
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                kw:params
            };
        },
        results:function(data){
            var items = [];

             $.each(data.results,function(){
                var tmp = {
                    id : this.id,
                    text : this.name
                }
                items.push(tmp);
            });

            return {
                results: items
            };
        },
        processResults: function (data, page) {
            var items = [];
             $.each(data.msg,function(){
                var tmp = {
                    id : this.id,
                    text : this.title
                }
                items.push(tmp);
            });
            return {
                results: items
            };
        }
    }
        $(function(){

           $('.select2').select2({
              placeholder: '请选择',
              allowClear: true
           });

				var houses_edit = $('#plot');
				var data = {};
				if( houses_edit.length && houses_edit.data('houses') ){
					data = eval(houses_edit.data('houses'));
				}

				$('#plot').select2({
					multiple:true,
					ajax: getHousesAjax,
					language: 'zh-CN',
					initSelection: function(element, callback){
						callback(data);
					}
				});

             $('.form_datetime').datetimepicker({
                 autoclose: true,
                 isRTL: Metronic.isRTL(),
                 format: 'yyyy-mm-dd hh:ii',
                 // minView: 'm',
                 language: 'zh-CN',
                 pickerPosition: (Metronic.isRTL() ? 'bottom-right' : 'bottom-left'),
             });

             $('.form_datetime1').datetimepicker({
                 autoclose: true,
                 isRTL: Metronic.isRTL(),
                 format: 'yyyy-mm-dd',
                 minView: 'month',
                 language: 'zh-CN',
                 pickerPosition: (Metronic.isRTL() ? 'bottom-right' : 'bottom-left'),
             });
        });
        ";


Yii::app()->clientScript->registerScript('add',$js,CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2_locale_zh-CN.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
Yii::app()->clientScript->registerCssFile('/static/admin/pages/css/select2_custom.css');

Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/addCustomizeDialog.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/static/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js', CClientScript::POS_END, array('charset'=> 'utf-8'));
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootbox/bootbox.min.js', CClientScript::POS_END);
?>
<script type="text/javascript">
    <?php Tools::startJs()?>
    function callback(data){
      var op = '';
      <?php foreach (Yii::app()->params['imageTag'] as $key => $value) {?>
        op += '<option value="<?=$key?>"><?=$value?></option>';
      <?php } ?>
        if($('.image-div').length >= 30) {
            alert('最多选择30张图片');
        } else {
            // 指定区域出现图片
            var html = "";
            image_html = "<div class='image-div' style='width: 150px;display:inline-table;height:180px'><a onclick='del_img(this)' class='btn red btn-xs' style='position: absolute;margin-left: 94px;'><i class='fa fa-trash'></i></a><img src='"+data.msg.url+"' style='width: 150px;height: 120px'><select style='width:120px' name='TkExt[type][]'>'"+op+"'</select><input type='hidden' class='trans_img' name='TkExt[album][]' value='"+data.msg.pic+"'></input><input type='text' style='width: 30px;height:24px' value='0' name='TkExt[sort][]'></div>";
            $('.images-place').append(image_html);
        }
    }
    //删除图片
    function del_img(obj)
    {
        //将已选择的图片重设为可以选择
        img = $(obj).parent().find('img').attr('src');
        $('.xqtp').find('img[src="'+img+'"]').parent().find('.ch_img').html('<a onclick="ch_img(this)" >点击选择</a>');
        $(obj).parent().remove();

    }
    <?php Tools::endJs('js')?>
</script>
