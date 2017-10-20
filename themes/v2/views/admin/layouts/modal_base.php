<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $this->pageTitle; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="/static/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="/static/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/static/global/plugins/bootstrap-toastr/toastr.min.css">
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->

<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="/static/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="/static/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="/static/admin/layout/css/themes/light.css" rel="stylesheet" type="text/css"/>
<link href="/static/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
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
<body class="page-header-fixed page-quick-sidebar-over-content">

	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<!-- BEGIN:单页正文-->
		<div class="row">
			<div class="col-md-12">
				<?php echo $content ?>
			</div>
		</div>
		<!-- END:单页正文 -->
	</div>
	<!-- END CONTENT -->
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
<script src="/static/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>
<script src="/static/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
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
   </script>
<!-- END JAVASCRIPTS -->
<?php $this->widget('HouseTip'); ?>
</body>
<!-- END BODY -->
</html>
