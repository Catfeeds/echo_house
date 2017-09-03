<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1,maximum-scale=1, minimum-scale=1">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/reset.css">
	<link rel="stylesheet" type="text/css" href="../../../../../../../subwap/css/parameter.css">
	<title></title>
</head>
<body>
	<div class="parameter-time">
		<div class="parameter-blank"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">开盘时间</div>
			<div class="parameter-tag-value"><?=$open_time?$open_time:'已开盘'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">交房时间</div>
			<div class="parameter-tag-value"><?=$delivery_time?$delivery_time:'-'?></div>
		</div>
	</div>

	<div class="parameter-developer">
		<div class="parameter-blank"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">开发商</div>
			<div class="parameter-tag-value"><?=$developer?$developer:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">开发商品牌</div>
			<div class="parameter-tag-value"><?=$brand?$brand:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">物业公司</div>
			<div class="parameter-tag-value"><?=$manage_company?$manage_company:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">售楼部电话</div>
			<div class="parameter-tag-value"><?=$sale_tel?$sale_tel:'-'?></div>
		</div>
	</div>

	<div class="parameter-area">
		<div class="parameter-blank"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">建筑面积</div>
			<div class="parameter-tag-value"><?=$size?$size:'-'?>㎡</div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">容积率</div>
			<div class="parameter-tag-value"><?=$capacity?$capacity:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">绿化率</div>
			<div class="parameter-tag-value"><?=$green?($green.'%'):'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">总户数</div>
			<div class="parameter-tag-value"><?=$household_num?$household_num:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">总栋数</div>
			<div class="parameter-tag-value"><?=$building_num?$building_num:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">楼层状况</div>
			<div class="parameter-tag-value"><?=$floor_desc?$floor_desc:'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">车位数</div>
			<div class="parameter-tag-value"><?=$carport?$carport:'-'?></div>
		</div>
	</div>

	<div class="parameter-house-parameter">
		<div class="parameter-blank-2">
			<div class="parameter-blank-text">项目参数</div>
		</div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">均价</div>
			<div class="parameter-tag-value"><?=$price?$price:'-'?>元/㎡</div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">物业费</div>
			<div class="parameter-tag-value"><?=$manage_fee?$manage_fee:'-'?>元/㎡/月</div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">装修状况</div>
			<div class="parameter-tag-value"><?=$zxzt?implode(',', $zxzt):'-'?></div>
		</div>
		<div class="line"></div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">产权年限</div>
			<div class="parameter-tag-value"><?=$property_years?$property_years:'-'?></div>
		</div>
	</div>

	<div class="parameter-house-parameter">
		<div class="parameter-blank-2">
			<div class="parameter-blank-text">交通配套</div>
		</div>
		<div class="parameter-tag">
			<div class="parameter-tag-name">交通状况</div>
			<div class="parameter-tag-value"><?=$transit?$transit:'-'?></div>
		</div>
		<div class="line"></div>
	</div>
<script type="text/javascript" src="../../../../../../../subwap/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/rem.js"></script>
<script type="text/javascript" src="../../../../../../../subwap/js/vue.min.js"></script>
<script src="https://cdn.bootcss.com/vue-resource/1.3.4/vue-resource.min.js"></script>
</body>
</html>