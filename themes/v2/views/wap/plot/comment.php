<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1,maximum-scale=1, minimum-scale=1">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/reset.css">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/comment.css">
	<title></title>
</head>
<body>
<?php if($infos = $info->news) foreach ($infos as $key => $value) {?>
	<div class="comment-item">
		<div class="line"></div>
		<img class="user-pic" src="../../../../../../../subwap/img/user-pic.png">
		<div class="user-name"><?=$value->author?></div>
		<div class="user-comment"><?=$value->content?></div>
		<div class="date-time">
		<div class="date"><?=date('m-d',$value->updated)?></div>
		<div class="time"><?=date('H:i',$value->updated)?></div>
		</div>
	</div>
<?php }?>
	
<script type="text/javascript" src="../../../../../../../subwap/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/rem.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/vue.min.js"></script>
<script src="https://cdn.bootcss.com/vue-resource/1.3.4/vue-resource.min.js"></script>
</body>
</html>