<?php Yii::app()->clientScript->registerCssFile(Yii::app()->params['urmHost'].'static/os/style/hjos.css') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->params['urmHost'].'static/os/js/hjos.js', CClientScript::POS_BEGIN) ?>
<?php if(isset($ads['tonglan'])): ?>
    <?php $tonglan = $ads['tonglan'];?>
    <!--通栏-->
    <div class="wapper">
        <ul class="ad">
            <?php foreach($tonglan as $v) : ?>
            <li>
                <div id="HJ_ad_<?php echo $v?>">
                    <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                </div>
            </li>
            <div class="blank5"></div>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(isset($ads['youcebanner'])): ?>
    <?php $banner = $ads['youcebanner'];?>
    <!--通栏-->
    <div>
        <ul class="ad">
            <?php foreach($banner as $v) : ?>
            <li>
                <div id="HJ_ad_<?php echo $v?>">
                    <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                </div>
            </li>
            <div class="blank5"></div>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['duilian'])):?>
    <?php foreach($ads['duilian'] as $v) : ?>
        <li>
            <div id="HJ_ad_<?php echo $v?>">
                <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
            </div>
        </li>
        <div class="blank5"></div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if(isset($ads['tonglanSlim'])): ?>
    <?php $tonglanS = $ads['tonglanSlim'];?>
    <?php if(!is_array($tonglanS)) $tonglanS = [$tonglanS] ?>
    <!--窄通栏-->
    <div class="wapper">
        <ul class="ad">
            <?php foreach($tonglanS as $v) : ?>
            <li>
                <div id="HJ_ad_<?php echo $v?>">
                    <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                </div>
            </li>
            <div class="blank5"></div>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
 <?php if(isset($ads['sanfenzhiyi'])): ?>
    <!--三分之一通栏-->
    <div class="wapper">
        <ul class="ad3">
            <?php foreach($ads['sanfenzhiyi'] as $k => $v): ?>
            <li id="bbs_ad_<?php echo $v; ?>"  class="<?php if($k % 3 == 0):?>ad-0<?php elseif($k % 3 == 1): ?>ad-1<?php endif; ?>"?>
                <div id="HJ_ad_<?php echo $v?>">
                    <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (isset($ads['erfenzhiyi'])): ?>
    <!--二分之一-->
    <div class="wapper">
        <ul class="ad2">
            <?php foreach($ads['erfenzhiyi'] as $k => $v) : ?>
                <li <?php if($k % 2 == 0) echo 'class="ad-0"'?>>
                    <div id="HJ_ad_<?php echo $v?>">
                        <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['sifenzhiyi'])): ?>
    <!--四分之一-->
    <div class="wapper">
        <ul class="ad4">
            <?php foreach($ads['sifenzhiyi'] as $k => $v) : ?>
                <li>
                    <div id="HJ_ad_<?php echo $v?>">
                        <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['liufenzhiyi'])):?>
    <!--六分之一-->
    <div class="wapper">
        <ul class="ad6">
            <?php foreach($ads['liufenzhiyi'] as $k => $v) : ?>
                <li <?php if($k !== 5): ?>class="ad-<?php echo $k?>"<?php endif; ?>>
                    <div id="HJ_ad_<?php echo $v?>">
                        <script type="text/javascript">HJ_ad(<?php echo $v ?>);</script>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if(isset($ads['dafujiangluo'])):?>
    <?php $ids = $ads['dafujiangluo']?>
    <?php if(is_array($ids)): ?>
    <!--大幅降落-->
        <?php $id = current($ids)?>
        <div id="HJ_ad_<?php echo $id?>">
            <script type="text/javascript">HJ_ad(<?php echo $id ?>);</script>
        </div>
    <?php endif; ?>
<?php endif;?>
<?php if(isset($ads['bottomBanner'])):?>
    <?php $ids = $ads['bottomBanner']?>
    <?php if(is_array($ids)): ?>
    <!--大幅降落-->
        <?php $id = current($ids)?>
        <div id="HJ_ad_<?php echo $id?>">
            <script type="text/javascript">HJ_ad(<?php echo $id ?>);</script>
        </div>
    <?php endif; ?>
<?php endif;?>
<?php if(isset($ads['fuchuang'])):?>
    <?php $ids = $ads['fuchuang']?>
    <?php if(is_array($ids)): ?>
    <!--右下浮窗-->
        <?php $id = current($ids)?>
        <div id="HJ_ad_<?php echo $id?>">
            <script type="text/javascript">HJ_ad(<?php echo $id ?>);</script>
        </div>
    <?php endif; ?>
<?php endif;?>
<?php if(isset($ads['zhongtonglan'])):?>
    <?php $ids = $ads['zhongtonglan']?>
    <?php if(is_array($ids)): ?>
    <!--中通栏-->
        <?php $id = current($ids)?>
        <div id="HJ_ad_<?php echo $id?>">
            <script type="text/javascript">HJ_ad(<?php echo $id ?>);</script>
        </div>
    <?php endif; ?>
<?php endif;?>
