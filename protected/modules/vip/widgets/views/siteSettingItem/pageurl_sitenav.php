<div class="wrap<?=$this->attribute; ?>">
    <?php
    $value = $this->model->{$this->attribute}->value;
    foreach($value['name'] as $k=>$v):
    ?>
        <div class="form-inline">
            <?php echo $this->form->textField($this->model, $this->attribute.'[name][]', ['class'=>'form-control','placeholder'=>'菜单链接','value'=>$v]); ?>
            <?php echo $this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control','placeholder'=>'菜单链接','value'=>$value['url'][$k],'style'=>'width:400px']).($k>0 ? $this->addDelButton() : ''); ?>
        </div>
    <?php endforeach; ?>
</div>
<a href="javascript:;" class="btn " id="add<?=$this->attribute ?>"><i class="fa fa-plus"></i>增加一栏</a>

<?php
$input = $this->form->textField($this->model, $this->attribute.'[name][]', ['class'=>'form-control','placeholder'=>'菜单标题','value'=>'']).'&nbsp;'.$this->form->textField($this->model, $this->attribute.'[url][]', ['class'=>'form-control','placeholder'=>'菜单链接','value'=>'','style'=>'width:400px']).$this->addDelButton();
$input = $this->compressHtml($input);
$html = <<<EOT
<div class="form-inline">$input</div>
EOT;
 ?>


<script type="text/javascript">
<?php
$js = <<<EOT
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
