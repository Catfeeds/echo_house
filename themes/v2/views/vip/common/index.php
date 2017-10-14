<?php
$this->pageTitle = '经纪圈新房通后台欢迎您';
?>
<?php 
	$thishits = $allhits = $thissubs = $allsubs = $thism = $allm = $thiscomes = $allcomes = 0;
	$hids = [];
	$hidsa = Yii::app()->db->createCommand("select id from plot where deleted=0 and company_id=".Yii::app()->user->cid)->queryAll();
	if($hidsa) {
		foreach ($hidsa as $key => $value) {
			$thishits += Yii::app()->redis->getClient()->hGet('plot_views',$value['id']);
			$hids[] = $value['id'];
		}
	}
	$allhits = Yii::app()->db->createCommand("select sum(views) from plot where deleted=0 and company_id=".Yii::app()->user->cid)->queryScalar();

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
	$criteria->addCondition('updated>=:begin and updated<=:end');
	$criteria->params[':begin'] = TimeTools::getDayBeginTime();
	$criteria->params[':end'] = TimeTools::getDayEndTime();
	$thism = SubExt::model()->undeleted()->count($criteria);

    $criteria = new CDbCriteria;
    $criteria->addInCondition('hid',$hids);
    $criteria->addCondition('status>=1');
    $allcomes = SubExt::model()->undeleted()->count($criteria);
    $criteria->addCondition('updated>=:begin and updated<=:end');
    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
    $criteria->params[':end'] = TimeTools::getDayEndTime();
    $thiscomes = SubExt::model()->undeleted()->count($criteria);

?>
<div class="alert alert-info">
    <?php $pa = $this->company->package;?>
    <?=$this->company->name?>欢迎您，贵公司后台到期时间：<?=date('Y-m-d',$pa->expire)?>，剩余房源发布量 <?=$pa->plot_num-$this->company->plotnum?> ，剩余短信量 <?=$pa->msg_num-$this->company->msg_num?> 。
</div>
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
<div class="row">
    <div class="col-md-6">
        <!-- Begin: life time stats -->
        <div class="portlet box blue-steel">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart-o"></i>分组数据
                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <?php if($js = $this->company->getScjl(0)):?>
                            <?php foreach ($js as $key => $value) {?>
                                <li class="<?=$key==0?'active':''?>">
                                    <a href="#overview_<?=$key+1?>" data-toggle="tab">
                                    <?=$value['name'].'组'?></a>
                                </li>
                            <?php } ?>
                        <?php endif;?>
                    </ul>
                    <div class="tab-content">
                    <?php if($js) {
                        foreach ($js as $key => $value) {?>
                            <div class="tab-pane <?=$key==0?'active':''?>" id="overview_<?=$key+1?>">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                         姓名
                                    </th>
                                    <th>
                                         今日新增报备数量/总数
                                    </th>
                                    <th>
                                         今日新增来访数/总数
                                    </th>
                                    <th>
                                         今天成交数量/总数
                                    </th>
                                    <th>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if($us = Yii::app()->db->createCommand("select id,name,phone from user where cid=".$this->company->id." and parent=".$value['id'])->queryAll()) { foreach ($us as  $u) {?>
                                    <?php 
                                    $criteria = new CDbCriteria;
                                    $criteria->addCondition("notice=:no");
                                    $criteria->addInCondition('hid',$hids);
                                    $criteria->params[':no'] = $u['phone'];
                                    $allsubs = SubExt::model()->undeleted()->count($criteria);
                                    $criteria->addCondition('created>=:begin and created<=:end');
                                    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
                                    $criteria->params[':end'] = TimeTools::getDayEndTime();
                                    $thissubs = SubExt::model()->undeleted()->count($criteria);

                                    $criteria = new CDbCriteria;
                                    $criteria->addCondition("notice=:no");
                                    $criteria->addInCondition('hid',$hids);
                                    $criteria->params[':no'] = $u['phone'];
                                    $criteria->addCondition('status>=3 and status<6');
                                    $allm = SubExt::model()->undeleted()->count($criteria);
                                    $criteria->addCondition('updated>=:begin and updated<=:end');
                                    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
                                    $criteria->params[':end'] = TimeTools::getDayEndTime();
                                    $thism = SubExt::model()->undeleted()->count($criteria);

                                    $criteria = new CDbCriteria;
                                    $criteria->addCondition("notice=:no");
                                    $criteria->addInCondition('hid',$hids);
                                    $criteria->params[':no'] = $u['phone'];
                                    $criteria->addCondition('status>=1');
                                    $allcomes = SubExt::model()->undeleted()->count($criteria);
                                    $criteria->addCondition('updated>=:begin and updated<=:end');
                                    $criteria->params[':begin'] = TimeTools::getDayBeginTime();
                                    $criteria->params[':end'] = TimeTools::getDayEndTime();
                                    $thiscomes = SubExt::model()->undeleted()->count($criteria);
                                     ?>
                                    <tr>
                                    <td>
                                        <a href="javascript:;">
                                        <?=$u['name'].$u['phone']?></a>
                                    </td>
                                    <td>
                                         <?=$thissubs.'/'.$allsubs?>
                                    </td>
                                    <td>
                                         <?=$thiscomes.'/'.$allcomes?>
                                    </td>
                                    <td>
                                         <?=$thism.'/'.$allm?>
                                    </td>
                                    <td>
                                        <a href="<?=$this->createUrl('sub/list',['sczy'=>$u['phone']])?>" class="btn default btn-xs green-stripe">
                                        详情 </a>
                                    </td>
                                </tr>
                                  <?php } } ?>
                                
                                </tbody>
                                </table>
                            </div>
                        </div>
                        <?php }
                        } ?>
                    
                    </div>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
    <div class="col-md-6">
        <!-- Begin: life time stats -->
        <div class="portlet box grey-cascade">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-thumb-tack"></i>后台须知
                </div>
            </div>
            <div class="portlet-body">
            <div class="well">
                     <?=SiteExt::getAttr('qjpz','vipNotice')?>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
