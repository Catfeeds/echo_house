<?php
$this->pageTitle = '站点配置导览';
$this->breadcrumbs = array('后台管理',$this->pageTitle);
?>
<?php foreach($sites as $k => $model): ?>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
        <div class="dashboard-stat grey">
            <div class="visual">
                <i class="fa fa-paw"></i>
            </div>
            <div class="details">
                <div class="desc">
                    <?=$model?>
                </div>
            </div>
            <a class="more" href="<?=$this->createUrl('edit', ['type'=>$k]); ?>">点此进行配置<i class="m-icon-swapright"></i>
            </a>
        </div>
    </div>
<?php endforeach; ?>
