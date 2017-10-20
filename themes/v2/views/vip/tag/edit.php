<?php
$this->pageTitle = $model->id ? '添加标签':'编辑标签';
$this->breadcrumbs = array('标签管理',$this->pageTitle);
?>
<div class="portlet-body">
    <?php $form = $this->beginWidget('HouseForm',array('htmlOptions'=>array('class'=>'form-horizontal'))) ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-2 control-label">标签名称<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
                <?php echo $form->textField($model,'name',array('class'=>'form-control')) ?>
            </div>
            <div class="col-md-2"><?php echo $form->error($model, 'name') ?></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">标签分类<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
                <?php echo $form->dropDownList($model,'cate',$dropDownListCates,array('class'=>'form-control','encode'=>false,'disabled'=>!$model->isNewRecord)) ?>
            </div>
            <div class="col-md-2"><?php echo $form->error($model, 'cate') ?></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">状态<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4 radio-list">
                <?php echo $form->radioButtonList($model, 'status', TagExt::$status, array('separator'=>'','template'=>'<label>{input} {label}</label>')); ?>
            </div>
        </div>
        <?php if(!$model->getIsDirectTag()): ?>
        <div class="form-group last">
			<label class="col-md-2 control-label"></label>
			<div class="col-md-10">
				<p class="help-block">
					 【示例】标签名称为“三户型”，则最小值填写“3”，最大值也填写“3”；标签名称为“五户型以上”，则最小值填写“5”，最大值为“0”；
				</p>
			</div>
		</div>
        <div class="form-group">
            <label class="col-md-2 control-label">最小值<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
                <?php echo $form->textField($model,'min',array('class'=>'form-control')) ?>
            </div>
            <div class="col-md-2"><?php echo $form->error($model, 'min') ?></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">最大值<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
                <?php echo $form->textField($model,'max',array('class'=>'form-control')) ?>
            </div>
            <div class="col-md-2"><?php echo $form->error($model, 'max') ?></div>
        </div>
        <?php endif; ?>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-2 col-md-9">
                    <button type="submit" class="btn green">保存</button>
                    <?php echo CHtml::link('返回',$this->createUrl('list'),array('class'=>'btn default')) ?>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
