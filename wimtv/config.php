<?php
$wimtv_plugin_root = plugin_dir_url(__FILE__);
$wimtv_plugin_path = wp_make_link_relative($wimtv_plugin_root);

$username = get_option("wimtvpro_username");
$password = get_option("wimtvpro_password");

?>
<script type="text/javascript">
	wimtv_plugin_path = "<?php echo $wimtv_plugin_path; ?>";
</script>

	<div id="head">
		<!-- Web fonts -->
		<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>

		<!-- CSS Global Compulsory -->
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/bootstrap/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/font-awesome/css/font-awesome.min.css">

		<!-- CSS Common libraries -->
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/toastr.min.css" type="text/css" media="screen">

		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>private/assets/css/wimtv-admin.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/css/common.css">

		<!-- JS Plugins -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/bootstrap/dist/js/bootstrap.min.js"></script>
		<!-- JS Common libraries -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/toastr.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/jquery-ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/moment/moment.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/underscore.min.js"></script>
	</div>

	<div id="body">
		<div class="loading-overlay" ng-class="{'loading-visible': $root.globalLoading}">
			<div class="spinner"></div>
			<div class="loading-text">Loading...</div>
		</div>

		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h3>WimTVPro Settings</h3>

					<div class="col-md-4"></div>
					<div class="col-md-4 text-center">
						<form class="form panel panel-setting" name="settings" action="admin-post.php" accept-charset="UTF-8" id="login-nav">
							<input type="hidden" name="action" value="wimtvpro_config">
							<div class="form-group margin-top-10">
								<img src="<?php echo $wimtv_plugin_root; ?>private/assets/img/wimtvpro_logo_small.png" alt="WimTVPro">
								<h4>WimTV Username</h4>
								<label class="sr-only">Username</label>
								<input type="text" name="username" value="<?php echo $username; ?>" class="form-control" placeholder="Username" required>
							</div>
							<div class="form-group">
								<h4>WimTV Password</h4>
								<label class="sr-only">Password</label>
								<input type="password" name="password" value="<?php echo $password; ?>" class="form-control" placeholder="Password" required>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">Login</button>
							</div>
						</form>
						<div class="bottom text-center">
							<p>Not yet registered? Create your Web TV in a few simple steps!</p>
							<p>
								<a class="btn btn-success btn-lg" href="https://new.wim.tv/#/registrazione" target="_blank">
									<b>Get your free accout now!</b>
								</a>
							</p>
							<p><a href="https://new.wim.tv/#/funzionalita" target="_blank">Learn More</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
