<div class="wrap<?=$this->attribute; ?>">
    <?php
    $value = $this->model->{$this->attribute}->value;
    foreach($value['title'] as $k=>$v):
    ?>
        <div class="form-inline">
            <?php echo $this->form->textField($this->model, $this->attribute.'[title][]', ['class'=>'form-control','placeholder'=>'链接名称','value'=>$value['title'][$k]]); ?>
            <?php echo $this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control','placeholder'=>'链接地址','value'=>$value['url'][$k]]); ?>
            <?php echo CHtml::dropDownList('default', '', $this->model->{$this->attribute}->data['url'], ['class'=>'form-control defaultV']); ?>
            <?php
            $attribute = $this->attribute.'[blank][]';
            $name = CHtml::resolveName($this->model, $attribute);
            echo CHtml::dropDownList($name, $value['blank'][$k], $this->model->{$this->attribute}->data['blank'], ['class'=>'form-control']).($this->addMoveButton()).($k>0 ? $this->addDelButton() : '');
            ?>
        </div>
    <?php endforeach; ?>
</div>
<a href="javascript:;" class="btn " id="add<?=$this->attribute ?>"><i class="fa fa-plus"></i>增加一栏</a>

<?php
$input = $this->form->textField($this->model, $this->attribute.'[title][]', ['class'=>'form-control','placeholder'=>'链接名称','value'=>'']).'&nbsp;'.$this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control','placeholder'=>'链接地址','value'=>'']).'&nbsp;'.CHtml::dropDownList('default', '', $this->model->{$this->attribute}->data['url'], ['class'=>'form-control defaultV']).'&nbsp;'.CHtml::dropDownList($name, '', $this->model->{$this->attribute}->data['blank'], ['class'=>'form-control']).($this->addMoveButton()).($k>0 ? $this->addDelButton() : '');
$input = $this->compressHtml($input);
$html = <<<EOT
<div class="form-inline">$input</div>
EOT;
 ?>


<script type="text/javascript">
<?php
$js = <<<EOT

$(".defaultV").live('change',function(){
    $(this).prev().val($(this).val());
});

var html = '{$html}';
$("#add{$this->attribute}").click(function(){
    if($(".wrap{$this->attribute}").children().length>=13) {
        toastr.error('最多添加13个');
    } else {
        $(".wrap{$this->attribute}").append(html);
    }
});
EOT;

Yii::app()->clientScript->registerScript('js'.$this->attribute, $js, CClientScript::POS_END);

 ?>
</script>
