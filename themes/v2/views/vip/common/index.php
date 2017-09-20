<?php
$this->pageTitle = '经纪圈新房通后台欢迎您';
?>
<?php 
	$thishits = $allhits = $thissubs = $allsubs = $thism = $allm = $thiscomes = $allcomes = 0;
	$hids = [];
	$hidsa = Yii::app()->db->createCommand("select id from plot where company_id=".Yii::app()->user->cid)->queryAll();
	if($hidsa) {
		foreach ($hidsa as $key => $value) {
			$thishits += Yii::app()->redis->getClient()->hGet('plot_views',$value['id']);
			$hids[] = $value['id'];
		}
	}
	$allhits = Yii::app()->db->createCommand("select sum(views) from plot where company_id=".Yii::app()->user->cid)->queryScalar();

	$criteria = new CDbCriteria;
	$criteria->addInCondition('hid',$hids);
	$allsubs = SubExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thissubs = SubExt::model()->undeleted()->count($criteria);

	$criteria = new CDbCriteria;
	$criteria->addInCondition('hid',$hids);
	$criteria->addCondition('status>=3 and status<6');
	$allm = SubExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thism = SubExt::model()->undeleted()->count($criteria);

    $criteria = new CDbCriteria;
    $criteria->addInCondition('hid',$hids);
    $criteria->addCondition('status>=1');
    $allcomes = SubExt::model()->undeleted()->count($criteria);
    $criteria->addCondition('created>=:begin and created<=:end');
    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
    $criteria->params[':end'] = TimeTools::getDayEndTime();
    $thiscomes = SubExt::model()->undeleted()->count($criteria);

?>
<div class="row">
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat blue-madison">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thishits.'/'.($allhits+$thishits) ?>
                </div>
                <div class="desc">
                    今日楼盘点击数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('plot/list')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thissubs.'/'.$allsubs ?>
                </div>
                <div class="desc">
                    今日新增报备数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('sub/list')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat green-haze">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thiscomes.'/'.$allcomes ?>
                </div>
                <div class="desc">
                    今日新增来访数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('sub/list',['cate'=>1])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat purple-plum">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thism.'/'.$allm?>
                </div>
                <div class="desc">
                    今天成交数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('sub/list',['cj'=>1])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>
<div class="alert alert-info alert-dismissable">
            <strong>后台须知: </strong><br>
            <?=SiteExt::getAttr('qjpz','vipNotice')?>
        </div>