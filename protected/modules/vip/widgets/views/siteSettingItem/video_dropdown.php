<?php
$attribute = $this->attribute;
$value = $this->model->{$this->attribute}->value;
$data = $this->model->{$this->attribute}->data;
$name = $this->model->className;
?>
<div class="col-md-6 form-group">
    <?php echo CHtml::dropDownList($name.'['.$attribute.']',$value,$data,['class'=>'form-control'])?>
</div>
