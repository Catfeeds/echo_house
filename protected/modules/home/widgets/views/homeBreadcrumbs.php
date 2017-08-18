
<div class="wapperout">
    <div class="wapper">
        <div class="p_current fs14">
        <?php foreach($links as $k=>$v):?>
            <?php if(!$k): ?>
                当前位置：
            <?php endif;?>
            <?php echo $v; ?>
            <?php if(count($links)!=($k+1)): ?>
                &gt;<span></span>
            <?php endif; ?>
        <?php endforeach;?>
        </div>
    </div>
    <div class="line"></div>
</div>