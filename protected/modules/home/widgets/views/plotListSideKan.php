<div class="title">
    看房团报名<a href="<?$this->owner->create('/home/tuan/index'); ?>" target="_blank" class="more">更多&gt;</a>
</div>
<div class="new-box">
    <?php foreach($data as $v): ?>
    <p class="p1"><a href="<?=$this->owner->createUrl('/home/tuan/index'); ?>#r<?=$v->id; ?>" target="_blank"><?=$v->title; ?></a></p>
    <p class="p2"><span>时间：<?=date('m-d H:i',$v->gather_time); ?></span><a href="javascript:;" data-title=<?=$v->title; ?> data-spm="<?=OrderExt::generateSpm('看房团', $v); ?>" class="k-dialog-type-1">免费报名</a></p>
    <?php endforeach; ?>
</div>
<div class="blank10"></div>
