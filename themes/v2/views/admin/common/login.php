<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.2
Version: 3.7.0
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $this->pageTitle='经纪圈新房通后台登录' ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<!-- <link href="http://fonts.useso.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="/static/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="/static/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/pages/css/login-soft.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="/static/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="/static/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="/static/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">

<!-- BEGIN LOGO -->
<div class="logo">
    	<!-- <img src="/static/admin/layout/img/logo-hj.png" alt=""/> -->
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content ">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" action="" method="post">
		<h3 class="form-title" style="font-family: 微软雅黑">经纪圈新房通后台登录</h3>
		<div class="alert alert-danger <?php if(!$model->hasErrors('error')) echo 'display-hide'; ?>">
			<button class="close" data-close="alert"></button>
			<span><?php echo $model->getError('error') ?></span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">用户名</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">密码</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="password"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<input type="checkbox" name="remember" value="1" /> 记住我 </label>
			<button type="submit" class="btn blue pull-right">
			登录 <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 2017 &copy; 回音网络科技版权所有
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/static/global/plugins/respond.min.js"></script>
<script src="/static/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="/static/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/static/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/global/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/static/global/scripts/metronic.js" type="text/javascript"></script>
<script src="/static/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="/static/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script src="/static/admin/pages/scripts/login-soft.js" type="text/javascript"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<script>
//如果退出登录的话，则不被iframe
	if(top.location!=self.location){
		top.location=self.location;
	}
jQuery(document).ready(function() {
  Metronic.init(); // init metronic core components
Layout.init(); // init current layout
  Login.init();
  Demo.init();
       // init background slide images
       $.backstretch([
        // "/static/admin/pages/media/bg/1.jpg",
        // "/static/admin/pages/media/bg/2.jpg",
        // "/static/admin/pages/media/bg/3.jpg",
        ], {
          fade: 1000,
          duration: 3000
    }
    );
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>