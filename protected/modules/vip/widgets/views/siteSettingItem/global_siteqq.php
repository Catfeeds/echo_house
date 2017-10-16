<div class="wrap<?=$this->attribute; ?>">
    <?php
    $value = $this->model->{$this->attribute}->value;
    foreach($value['type'] as $k=>$v):
        $attribute = $this->attribute.'[type][]';
        $attribute = CHtml::resolveName($this->model, $attribute);
    ?>
        <div class="form-inline">
            <?php echo CHtml::dropDownList($attribute, $v, QqModel::$typeEnum, ['class'=>'form-control', 'onChange'=>'showQQ()']); ?>
            <?php echo $this->form->textField($this->model, $this->attribute.'[name][]', ['class'=>'form-control','placeholder'=>'QQ昵称或群组名称','value'=>$value['name'][$k]]); ?>
            <?php echo $this->form->textField($this->model, $this->attribute.'[number][]', ['class'=>'form-control','placeholder'=>'QQ号或QQ群号','value'=>$value['number'][$k]]); ?>
            <?php echo $this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control','placeholder'=>'QQ加群链接','value'=>$value['url'][$k]]).($k>0 ? $this->addDelButton() : ''); ?>
        </div>
    <?php endforeach; ?>
</div>
<a href="javascript:;" class="btn " id="add<?=$this->attribute ?>"><i class="fa fa-plus"></i>增加一栏</a>

<?php
$input = CHtml::dropDownList($attribute, $v, QqModel::$typeEnum, ['class'=>'form-control', 'onChange'=>'showQQ()']).'&nbsp;'.$this->form->textField($this->model, $this->attribute.'[name][]', ['class'=>'form-control','placeholder'=>'QQ昵称或群组名称','value'=>'']).'&nbsp;'.$this->form->textField($this->model, $this->attribute.'[number][]', ['class'=>'form-control','placeholder'=>'QQ号或QQ群号','value'=>'']).'&nbsp;'.$this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control hide','placeholder'=>'QQ加群链接','value'=>'']).$this->addDelButton();
$input = $this->compressHtml($input);
$html = <<<EOT
<div class="form-inline">$input</div>
EOT;
 ?>


<script type="text/javascript">
<?php
$js = <<<EOT
function showQQ()
{
    $('.wrap{$this->attribute}').children().each(function(i){
        $(this).find('input:last').removeClass('hide');
        if($(this).find(':first').val()=='qq') {
            $(this).find('input:last').addClass('hide');
        }
    });
}
showQQ();



var html = '{$html}';
$("#add{$this->attribute}").click(function(){
    if($(".wrap{$this->attribute}").children().length>=4) {
        toastr.error('最多添加4个');
    } else {
        $(".wrap{$this->attribute}").append(html);
    }
});
EOT;

Yii::app()->clientScript->registerScript('js', $js, CClientScript::POS_END);

 ?>
</script>
