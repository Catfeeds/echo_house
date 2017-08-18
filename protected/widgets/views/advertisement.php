<?php if(isset($ads['youcebanner'])): ?>
<!-- 右侧banner -->
    <?php foreach($ads['youcebanner'] as $v):?>
        <?php if($v->code): ?>
            <?php echo $v->code; ?>
        <?php elseif($v->isFlash): ?>
            <?php echo $this->flash($v->swf_url); ?>
        <?php else: ?>
            <script type="text/javascript">BAIDU_CLB_fillSlot(<?php echo $v->bd_id; ?>);</script>
            <div class="blank10"></div>
        <?php endif; ?>
    <?php endforeach;?>

<div class="blank15"></div>
<?php endif;?>
<?php if(isset($ads['zhongtonglan'][0])): ?>
<!-- 首页中通栏 -->
<?php if($ads['zhongtonglan'][0]->code): ?>
    <?php echo $ads['zhongtonglan'][0]->code; ?>
<?php elseif($ads['zhongtonglan'][0]->isFlash): ?>
    <?php echo $this->flash($ads['zhongtonglan'][0]->swf_url,90); ?>
<?php else: ?>
    <script type="text/javascript">BAIDU_CLB_fillSlot(<?php echo $ads['zhongtonglan'][0]->bd_id; ?>);</script>
    <?php endif; ?>
<?php endif;?>
<?php if(isset($ads['sanfenzhiyi'])): ?>
    <!-- 三分之一 -->
    <div class="wapper">
        <ul class="ad3">
        <?php foreach($ads['sanfenzhiyi'] as $k=>$v): ?>
        <li id="bbs_ad_<?php echo $v->bd_id; ?>" class="ad-<?php if($k%3==0){ echo 0;}elseif($k%3==1){echo 1;} ?>>
            <script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $v->bd_id; ?>");</script>
        </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['tonglan'])): ?>
    <!-- 通栏 -->
    <div class="wapper">
        <ul class="ad">
        <?php foreach($ads['tonglan'] as $v): ?>
        <?php if($v->code){
            echo $v->code;
        }elseif($v->isFlash){
            echo $this->flash($v->swf_url);
        }else{?>
            <li class="ad-0" id="bbs_ad_<?php echo $v->bd_id;?>">
                <script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $v->bd_id; ?>");</script>
            </li>
        <?php } ?>
        <div class="blank5"></div>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif;?>
<?php if(isset($ads['erfenzhiyi'])): ?>
    <!-- 二分之一 -->
    <div class="wapper">
        <ul class="nad2">
        <?php foreach($ads['erfenzhiyi'] as $k=>$v): ?>
        <li <?php if($k%2==1): ?>class="last"<?php endif;?> id="bbs_ad_<?php echo $v->bd_id;?>">
            <script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $v->bd_id; ?>");</script>
        </li>
        <?php endforeach;?>
        </ul>
    </div>
<?php endif;?>
<?php if(isset($ads['sifenzhiyi'])): ?>
    <!-- 四分之一 -->
    <div class="wapper">
        <ul class="ad4">
        <?php foreach($ads['sifenzhiyi'] as $v): ?>
        <li id="bbs_ad_<?php echo $v->bd_id; ?>">
            <script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $v->bd_id; ?>");</script>
        </li>
        <?php endforeach;?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['liufenzhiyi'])): ?>
    <!-- 六分之一 -->
    <div class="wapper">
        <ul class="ad6">
        <?php foreach($ads['liufenzhiyi'] as $k=>$v): ?>
        <li id="bbs_ad_<?php echo $v->bd_id; ?>" <?php if($k!=5): ?>class="ad-<?php echo $k; ?>"<?php endif;?>>
            <script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $v->bd_id; ?>");</script>
        </li>
        <?php endforeach;?>
        </ul>
    </div>
<?php endif;?>
<?php if(isset($ads['duilian']) || $this->position=='sydl' ):?>
<!-- 对联 -->
<?php foreach($ads['duilian'] as $v): ?>
<script type="text/javascript">BAIDU_CLB_fillSlot(<?php echo $v->bd_id; ?>);</script>
<?php endforeach; ?>
<?php endif; ?>
<?php if(isset($ads['dafujiangluo']) || $this->position=='sydfjl'): ?>
<!-- 大幅降落 -->
<?php
if($ads['dafujiangluo'][0]->code):
    echo $ads['dafujiangluo'][0]->code;
else:
 ?>
<script type="text/javascript">BAIDU_CLB_fillSlot(<?php echo $ads['dafujiangluo'][0]->bd_id; ?>);</script>
<?php endif;endif; ?>
<?php if(isset($ads['fuchuang']) || $this->position=='syyxjfc'): ?>
<!-- 大幅降落 -->
<script type="text/javascript">BAIDU_CLB_fillSlot(<?php echo $ads['fuchuang'][0]->bd_id; ?>);</script>
<?php endif;?>
