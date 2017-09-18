<?php
$this->pageTitle = $this->controllerName.'新建/编辑';
$this->breadcrumbs = array($this->controllerName.'管理', $this->pageTitle);
?>
<?php $this->widget('ext.ueditor.UeditorWidget',array('id'=>'UserExt_content','options'=>"toolbars:[['fullscreen','source','undo','redo','|','customstyle','paragraph','fontfamily','fontsize'],
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
    <label class="col-md-2 control-label">联赛</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'lid', $leas, array('class' => 'form-control', 'encode' => false)); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'lid') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">比赛时间<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <div class="input-group date form_datetime">
                  <?php echo $form->textField($article,'time',array('class'=>'form-control','value'=>($article->time?date('Y-m-d',$article->time):''))); ?>
                  <span class="input-group-btn">
                    <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                 </span>
              </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">主队</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'home_id', $cates, array('class' => 'form-control', 'encode' => false)); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'home_id') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">主队进球数<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'home_score', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'home_score') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">客队</label>
    <div class="col-md-4">
        <?php echo $form->dropDownList($article, 'visitor_id', $cates, array('class' => 'form-control', 'encode' => false)); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'visitor_id') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">客队进球数<span class="required" aria-required="true">*</span></label>
    <div class="col-md-4">
        <?php echo $form->textField($article, 'visitor_score', array('class' => 'form-control')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'visitor_score') ?></div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label">状态</label>
    <div class="col-md-4">
        <?php echo $form->radioButtonList($article, 'status', UserExt::$status, array('separator' => '')); ?>
    </div>
    <div class="col-md-2"><?php echo $form->error($article, 'status') ?></div>
</div>
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
