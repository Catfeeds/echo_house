<?php
$attribute = $this->attribute;
 ?>
<div class="col-md-6">
	<div class="input-group">
		<span class="input-group-addon">标题</span>
        <?=$this->form->textField($this->model, $this->attribute.'[title]',['class'=>'form-control','value'=>$this->model->{$this->attribute}()['title']]); ?>
    </div>
    <div class="input-group">
		<span class="input-group-addon">关键词</span>
        <?=$this->form->textField($this->model, $this->attribute.'[keyword]',['class'=>'form-control','value'=>$this->model->{$this->attribute}()['keyword']]); ?>
    </div>
    <div class="input-group">
		<span class="input-group-addon">描述</span>
        <?=$this->form->textField($this->model, $this->attribute.'[desc]',['class'=>'form-control','value'=>$this->model->{$this->attribute}()['desc']]); ?>
    </div>
</div>
