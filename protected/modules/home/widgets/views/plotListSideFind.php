<div class="title">帮你找房</div>
<div class="baoming-box  clearfix">
    <form class="ui-baoming-form" action="<?=$this->owner->createUrl('/api/order/ajaxKanOrderSubmit'); ?>" method="post">
        <dl>
            <dt>意向区域：</dt>
            <dd>
                <?=CHtml::dropDownList('yxqy', '', $this->allArea); ?>
            </dd>
        </dl>
        <dl>
            <dt>意向楼盘：</dt>
            <dd><input type="text" name="loupan" placeholder="请填写意向楼盘" nullmsg="请输入意向楼盘"></dd>
        </dl>
        <dl>
            <dt>预算总价：</dt>
            <dd><input type="text" class="phone-txt" placeholder="请填写预算，单位：万元" name="jiage" nullmsg="请填写预算，单位：万元" ></dd>
        </dl>
        <dl>
            <dt>您的姓名：</dt>
            <dd><input type="text" class="phone-txt" placeholder="请填写姓名" name="name" nullmsg="请输入姓名" ></dd>
        </dl>
        <dl>
        <dt>手机号码：</dt>
            <dd><input type="text" class="phone-txt" placeholder="请填写手机号码" name="phone"  datatype="m" nullmsg="请输入手机号" errormsg="手机号码格式不正确"></dd>
        </dl>
        <?php echo CHtml::hiddenField('spm', OrderExt::generateSpm('自由组团')); ?>
        <?php echo CHtml::hiddenField('csrf', Yii::app()->request->getCsrfToken()); ?>
        <input type="submit" class="tj-btn" value="提交">
    </form>
</div>
