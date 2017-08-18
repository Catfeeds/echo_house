<?php 
    Yii::app()->clientScript->registerCssFile("/themes/v2/static/home/style/a8ui.common.css");
?>

<!--顶部导航-->

<div class="menu"></div>
<!--顶部导航-->
<div class="error_404">
  <div class="container clearfix">
    <div class="error_pic"></div>
    <div class="error_info">
      <h1><?php echo $code; ?></h1>
      <h2>
        <p>对不起，您访问的页面不存在！</p>
      </h2>
      <div class="operate">
        <input class="global_btn btn_89bf43" onClick="location.href='/'" type="button" value="返回主页">
        <input class="global_btn btn_39dec8 ml1" onClick="history.go(-1)" type="button" value="返回上一页">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function(){
	 $("#back_top > a").click(function(){
		$("html, body").animate({scrollTop:"0px"},1000);
		return false
	});
})
</script>