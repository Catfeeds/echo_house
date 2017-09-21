<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $this->pageTitle ?>-经纪圈新房通管理后台</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<script src="/static/global/plugins/pace/pace.js" type="text/javascript"></script>
<link href="/static/global/plugins/pace/themes/pace-theme-flash.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/static/global/plugins/bootstrap-toastr/toastr.min.css">
<!-- <link href="/static/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/> -->
<!-- <link href="/static/global/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/> -->
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->

<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="/static/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="/static/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<style>
    .help-box{padding:10px 20px;border-top: 1px solid #3d4957; color: #b4bcc8; font-size:14px;}
    .help-box ul{margin:0;padding:0;}
    .help-box ul li{padding:10px 0; list-style:none;}
    .help-box ul li a.qq{ display:inline-block;width:80px; height:26px; line-height:26px;text-align:center; color:#333; background-color:#d7d9dd;font-size:12px;}
    .help-box ul li a.baoming{ display:block;width:145px;height:40px;line-height:40px; text-align:center; background-color:#f3f3f3;color:#333;}
    .help-box ul li a:hover{text-decoration: none;}
</style>
<!-- END THEME STYLES -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-quick-sidebar-over-content">
<!-- BEGIN HEADER -->
<div class="page-header -i navbar navbar-static-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo" style="width:500px">
			<a href="<?php echo $this->createUrl('/vip/common/index') ?>"  class="logo-name">
			经纪圈新房通管理后台-<?=Yii::app()->user->cname?>
			<!-- <img src="/static/admin/layout/img/logo.png" alt="logo" class="logo-default"/> -->
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<?php if(!empty(Yii::app()->user->avatar)): ?>
						<img alt="" class="img-circle" src="<?php echo ImageTools::fixImage(Yii::app()->user->avatar,0,30); ?>" height="30px" width='30px'/>
						<?php endif; ?>
						<span class="username username-hide-on-mobile">
							<i class="fa fa-cog"></i><?php echo Yii::app()->user->username; ?>
						</span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?php echo $this->createUrl('/vip/common/logout') ?>">
							<i class="icon-key"></i> 退出系统 </a>
						</li>
					</ul>
				</li>

				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu hidden-sm hidden-xs" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>

					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<br>
                <?php $this->widget('HouseMenu', ['items' => $this->getVipMenu()]) ?>
				<div class="help-box">
                	<ul>
                	<?php $siteqq = SiteExt::getAttr('qjpz','siteqq')?>
                    	<li>有事您找我</li>
                        <li>客服：<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=$siteqq&amp;site=qq&amp;menu=yes" target="_blank" class="qq">QQ交谈</a></li>                                            </ul>
                  </div>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->
			<?php if(!empty($this->pageTitle)): ?>
			<h3 class="page-title" style="font-family: 微软雅黑">
				<?php echo $this->pageTitle; ?>
			</h3>
			<?php endif; ?>
			<!-- BEGIN:面包屑 -->
			<?php
				if(isset($this->breadcrumbs))
					$this->widget('HouseBreadcrumbs',array('links'=>$this->breadcrumbs));
			?>
			<!-- END:面包屑 -->
			<!-- END PAGE HEADER-->
			<!-- BEGIN:单页正文-->
			<div class="row">
				<div class="col-md-12 <?php if($this->route=='vip/common/error') echo 'page-500' ?>">
					<?php echo $content ?>
				</div>
			</div>
			<!-- END:单页正文 -->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2017 &copy; <?php echo '回音网络科技版权所有'; ?>
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/static/global/plugins/respond.min.js"></script>
<script src="/static/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="/static/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/static/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>

<!-- <script src="/static/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js" type="text/javascript"></script> -->
<!-- <script src="/static/global/plugins/bootstrap-modal/js/bootstrap-modal.js" type="text/javascript"></script> -->
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->

<!-- END PAGE LEVEL PLUGINS -->
<script src="/static/global/scripts/metronic.js" type="text/javascript"></script>
<script src="/static/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="/static/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script>
      jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		Demo.init(); // init demo features
      });
      toastr.options = {
            closeButton: $("#closeButton").prop("checked"),
            timeOut: "2000",
            positionClass: "toast-top-center",
            onclick: null
        };
        if($('.xf').find('.active').length!='0')
        	$('.xf').attr('class','start active xf');
        if($('.esf').find('.active').length!='0')
        	$('.esf').attr('class','start active esf');
        if($('.public').find('.active').length!='0')
        	$('.public').attr('class','start active public');
        // $('.esf').children('ul').children('.active').children('a').css("cssText","background-color:rgb(28, 175, 154)!important");
        // $('.xf').children('ul').children('.active').children('a').css("cssText","background-color:rgb(28, 175, 154)!important");


</script>
<?php $this->widget('HouseTip'); ?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
