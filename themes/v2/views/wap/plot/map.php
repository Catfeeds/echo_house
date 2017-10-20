<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        body, html {width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
        #allmap{width:100%;height:100%;}
        p{margin-left:5px; font-size:14px;}
    </style>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=DvCxyFxjXZ0eqtg8Z3eSG4OAnXvi0das"></script>
    <title><?=$info->title?></title>
</head>
<body>
    <div id="allmap"></div>
    <p>点击标注点，可查看由纯文本构成的简单型信息窗口</p>
</body>
</html>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    var point = new BMap.Point(<?=$info->map_lng?>,<?=$info->map_lat?>);
    var marker = new BMap.Marker(point);  // 创建标注
    map.addOverlay(marker);              // 将标注添加到地图中
    map.centerAndZoom(point, <?=$info->map_zoom?>);
    var opts = {
      width : 200,     // 信息窗口宽度
      height: 100,     // 信息窗口高度
      title : "<?=$info->title?>" , // 信息窗口标题
      enableMessage:true,//设置允许信息窗发送短息
      message:""
    }
    var infoWindow = new BMap.InfoWindow("<?=$info->address?>", opts);  // 创建信息窗口对象 
    marker.addEventListener("click", function(){          
        map.openInfoWindow(infoWindow,point); //开启信息窗口
    });
</script>