<?php
$this->pageTitle = '经纪圈新房通后台欢迎您';
?>
<style>
    .hid{
        display: none;
    }
</style>
<?php 
	$thishits = $allhits = $thissubs = $allsubs = $thisreports = $allreports = $thiscoos = $allcoos = $thiszd = $allzd = $thisfx = $allfx = $thiszduser = $allzduser = $thisfxuser = $allfxuser = $thisdl = $alldl = 0;
	$hids = [];
	$hidsa = Yii::app()->redis->getClient()->hGetAll('plot_views');
	if($hidsa) {
		foreach ($hidsa as $key => $value) {
			$thishits += $value;
			// $hids[] = $value['id'];
		}
	}
	$allhits = Yii::app()->db->createCommand("select sum(views) from plot where status=1")->queryScalar();

	$criteria = new CDbCriteria;
	$allcoos = CooperateExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thiscoos = CooperateExt::model()->undeleted()->count($criteria);

	$criteria = new CDbCriteria;
	$allsubs = SubExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thissubs = SubExt::model()->undeleted()->count($criteria);

	$criteria = new CDbCriteria;
	$allreports = ReportExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thisreports = ReportExt::model()->undeleted()->count($criteria);

	$criteria = new CDbCriteria;
	$criteria->addCondition('type=1');
	$allzd = CompanyExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thiszd = CompanyExt::model()->undeleted()->count($criteria);

	$criteria = new CDbCriteria;
	$criteria->addCondition('type=2');
	$allfx = CompanyExt::model()->undeleted()->count($criteria);
	$criteria->addCondition('created>=:begin and created<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thisfx = CompanyExt::model()->undeleted()->count($criteria);

    $criteria = new CDbCriteria;
    $criteria->addCondition('type=1');
    $allzduser = UserExt::model()->undeleted()->count($criteria);
    $criteria->addCondition('created>=:begin and created<=:end');
    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
    $criteria->params[':end'] = TimeTools::getDayEndTime();
    $thiszduser = UserExt::model()->undeleted()->count($criteria);

    $criteria = new CDbCriteria;
    $criteria->addCondition('type=2');
    $allfxuser = UserExt::model()->undeleted()->count($criteria);
    $criteria->addCondition('created>=:begin and created<=:end');
    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
    $criteria->params[':end'] = TimeTools::getDayEndTime();
    $thisfxuser = UserExt::model()->undeleted()->count($criteria);

    $criteria = new CDbCriteria;
    $criteria->addCondition('type=3');
    $alldl = UserExt::model()->undeleted()->count($criteria);
    $criteria->addCondition('created>=:begin and created<=:end');
    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
    $criteria->params[':end'] = TimeTools::getDayEndTime();
    $thisdl = UserExt::model()->undeleted()->count($criteria);

?>
<div class="btn default" onclick="show()">显示数据</div>

<div class="row info hid">
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat blue-madison">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo 5000+$thishits.'/'.($allhits+$thishits+2000000) ?>
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
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thiscoos.'/'.$allcoos ?>
                </div>
                <div class="desc">
                    今日在线申请签约数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('cooperate/list')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat green-haze">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thissubs.'/'.$allsubs ?>
                </div>
                <div class="desc">
                    今日报备数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('sub/list')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>
<div class="row info hid">
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat blue-madison">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thisreports.'/'.$allreports ?>
                </div>
                <div class="desc">
                    今日举报数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('report/list')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thiszd.'/'.$allzd ?>
                </div>
                <div class="desc">
                    今日总代公司新增数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('company/list',['cate'=>1])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat green-haze">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thisfx.'/'.$allfx ?>
                </div>
                <div class="desc">
                    今日分销公司新增数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('company/list',['cate'=>2])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>
<div class="row info hid">
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat blue-madison">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thiszduser.'/'.$allzduser ?>
                </div>
                <div class="desc">
                    今日总代经纪人新增数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('user/list',['cate'=>1])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thisfxuser.'/'.($allfxuser+ImportUserExt::model()->count()) ?>
                </div>
                <div class="desc">
                    今日分销经纪人新增数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('user/list',['cate'=>2])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="dashboard-stat green-haze">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $thisdl.'/'.$alldl ?>
                </div>
                <div class="desc">
                    今日独立经纪人新增数/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('user/list',['cate'=>3])?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>
<script>
    function show() {
        if($('.info').attr('class')=='row info') {
            $('.info').attr('class','row info hid');
        } else {
            $('.info').attr('class','row info');
        }
    }
</script>
