<div class="wrap<?=$this->attribute; ?>">
<style type="text/css">
    hr{
        margin: 3px;
        width: 200px;
    }
</style>
    <?php
    $value = $this->model->{$this->attribute}->value;
    $allItems = Yii::app()->params['resoldNav'];
    foreach($allItems as $k=>$v):
    ?>
    
        <?php if(!is_array($v)):?>
            <span style="font-size: 16px"><?=$v?></span> <a class="btn <?=in_array($v, $value)?'yellow':'blue'?> btn-sm close-item" onclick="changeStatus(this)"><?=in_array($v, $value)?'开启':'关闭'?></a><hr>
        <?php else:?>
            <span style="font-size: 16px"><?=$k?></span> <a class="btn <?=in_array($k, $value)?'yellow':'blue'?> btn-sm close-item" onclick="changeStatus(this)"><?=in_array($k, $value)?'开启':'关闭'?></a><br><hr>
            <?php foreach ($v as $item) {?>
                <?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<span>'.$item;?></span> <a class="btn <?=in_array($item, $value)?'yellow':'blue'?> btn-sm close-item" onclick="changeStatus(this)"><?=in_array($item, $value)?'开启':'关闭'?></a><hr>
            <?php }?>
        <?php endif;?>
    <?php endforeach; ?>
</div>

<div class="input-space">
    
</div>

<script type="text/javascript">
<?php Tools::startJs()?>

var list = [];
<?php if($value) foreach ($value as $i) {?>
        list.push('<?=$i?>');
    <?php }?>

function changeStatus(obj) {
    $('.input-space').empty();
    var item = $(obj).prev().html();

    if($(obj).html() == '开启') {
        $(obj).html('关闭');
        $(obj).attr('class','btn blue btn-sm close-item');
        removeByValue(list, item);
    } else {
        $(obj).html('开启');
        $(obj).attr('class','btn yellow btn-sm close-item');
        list.push(item);
    }
    if(list.length > 0) {
        for (var i = 0; i < list.length; i++) {
            $('.input-space').append('<input type="hidden" name="<?=get_class($this->model)?>[<?=$this->attribute?>][]" value="'+list[i]+'"></input>');       
        }
    } else {
        $('.input-space').append('<input type="hidden" name="<?=get_class($this->model)?>[<?=$this->attribute?>][]" value=""></input>'); 
    }
}
function removeByValue(arr, val) {
  for(var i=0; i<arr.length; i++) {
    if(arr[i] == val) {
      arr.splice(i, 1);
      break;
    }
  }
}

<?php Tools::endJs('js')?>
</script>
