<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1,maximum-scale=1, minimum-scale=1">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/reset.css">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/commission.css">
	<title></title>
</head>
<body>
	<?php if($pays = $info->pays) foreach ($pays as $key => $value) {?>
		<div class="commission-head">
		<img class="commission-head-logo" src="../../subwap/img/pay.png">
		<div class="commission-head1"><?=$value->price?></div>
		<div class="commission-head2"><?=$value->name?></div>
		<div class="commission-head3"><?=$value->content?></div>
	</div>
	<div class="line"></div>
	<?php } ?>
	
	<!-- <div class="jieyong-rules">
		<div class="jieyong-rules-title">结佣规则</div>
		<div class="jieyong-text1"><?=$info->jy_rule?$info->jy_rule:'暂无'?></div>
	</div>
	<div class="line"></div>
	<div class="developer-rules">
		<div class="developer-rules-title">开发商规则</div>
		<div class="jieyong-text1"><?=$info->kfs_rule?$info->kfs_rule:'暂无'?></div>
	</div> -->






<script type="text/javascript" src="../../../../../../../subwap/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/rem.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/vue.min.js"></script>
<script src="https://cdn.bootcss.com/vue-resource/1.3.4/vue-resource.min.js"></script>
</body>
</html>