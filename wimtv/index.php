<?php
$wimtv_plugin_root = plugin_dir_url(__FILE__);
$wimtv_plugin_path = wp_make_link_relative($wimtv_plugin_root);
?>
<script type="text/javascript">
	wimtv_plugin_path = "<?php echo $wimtv_plugin_path; ?>";
	<?php
		$username = get_option("wimtvpro_username");
		$password = get_option("wimtvpro_password");
		if($username && $password) {
	?>
	localStorage.setItem("loggedUser", JSON.stringify({username: '<?php echo $username ?>', password: '<?php echo $password ?>'}));
	<?php
		}
	?>


</script>

<div ng-app="wimtvApp">
	<div id="head">
		<!-- Web fonts -->
		<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>

		<!-- CSS Global Compulsory -->
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/bootstrap/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/font-awesome/css/font-awesome.min.css">

		<!-- CSS Common libraries -->
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/toastr.min.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/visjs/vis.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/fullcalendar/fullcalendar.min.css" type="text/css" media="screen">


		<!-- CSS PLAYER -->
		<link type="text/css" rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/libs/player/skin/skin.css"/>

		<!-- CSS personalizzato del template -->
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>private/assets/css/bootstrap-clockpicker.min.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/css/circle.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>private/assets/css/wimtv-admin.css">
		<link rel="stylesheet" href="<?php echo $wimtv_plugin_root; ?>common/css/common.css">

		<!-- CSS video-js -->
		<link href="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/video-js/video-js.min.css" rel="stylesheet">

		<!-- endpoint-->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/config/endpointconfig.js"></script>

		<!-- JS Plugins -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/metismenu/metisMenu.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/js/bootstrap-clockpicker.min.js"></script>

		<!-- JS PLAYER -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/player/flowplayer/flowplayer.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/player/flowplayer/plugins/flowplayer.dashjs.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/player/flowplayer/plugins/flowplayer.hlsjs.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/player/player.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/player/wimtv/wimtv-player.js"></script>

		<!--<script type="text/javascript" src="common/libs/player/player.min.js"></script>-->

		<!-- JS Common libraries -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/toastr.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/jquery-ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/moment/moment.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/underscore.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/chartjs/chart.min.js"></script>

		<!-- VSA -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/vsa.lib.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/fullcalendar/fullcalendar.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/visjs/vis-timeline-graph2d.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/Timeline.js"></script>

		<!-- Web Producer -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/js/web-producer/swfobject.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/js/web-producer/webproducer.js"></script>

		<!-- Angular -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-ui-router.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-translate.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-translate-loader-static-files.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-sanitize.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-base64.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/bootstrap/dist/js/ui-bootstrap-tpls-1.3.3.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-file-upload.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-animate.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-message.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angular/angular-aria.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/videojs-range-slider/lib/video-js/video.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/angular-video-js/dist/vjs-video.js"></script>

		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angulartics/angulartics.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/angulartics/angulartics-ga.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/chartjs/angular-chart.min.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/libs/fullcalendar/ui-calendar.js"></script>

		<script type="text/javascript">
			var app = angular.module('wimtvApp', ['pubnub.angular.service','ui.router', 'ui.bootstrap', 'ui.calendar', 'pascalprecht.translate', 'ngSanitize', 'base64', 'ngAnimate', 'ngAria', 'ngMessages', 'ngFileUpload', 'angulartics', 'angulartics.google.analytics', 'chart.js', 'vjs.video']);
			angular.module("wimtvApp").run(['$rootScope', function($rootScope) {
				$rootScope.wordpressUrl = '<?php echo $wimtv_plugin_root ?>';
				console.log($rootScope.wordpressUrl);
			}]);
		</script>


		<!-- Charts Analytics -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/js/wimtv-admin.js"></script>

		<!-- APP e controller -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/app.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/config/config.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/restService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/genericRestService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/userCustomizationService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/analyticsService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/userService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/liveService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/authServices.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/resourceService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/channelService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/commonService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/bridgetRestService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/bridgetEditorService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/bridgetAssetService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/wimBoxService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/vodService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/bundleService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/marketPlaceService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/castChannelService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/castStreamService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/dailyProgrammingService.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/services/chatService.js"></script>

		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/interceptor.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/validator.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/bundle.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/video-thumb.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/country-picker.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/dashboard-panel.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/box-list.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/bridget-asset.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/bridgetHomeHeaders.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/video-list.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/profile-thumbnail.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/dashboardController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/loginController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimboxController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimBundleController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimBundleSingleController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimvodController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimliveChannelController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/producerController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/userprofileController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimbridgetHomeController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimbridgetImagesController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimbridgetVideosController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimbridgetBridgetsController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/analyticsController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimcastController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimmonthlyCastController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimdailyCastController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/helpModalController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimmarketController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimtradeController.js"></script>
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/app/controllers/wimchatController.js"></script>

		<!-- directives -->
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>common/directives/chat-console.js"></script>

		<!-- Pubnub -->
		<script type="text/javascript" src="https://cdn.pubnub.com/sdk/javascript/pubnub.4.4.0.js"></script>
		<script src="https://cdn.pubnub.com/sdk/pubnub-angular/pubnub-angular-4.0.2.js"></script>

		<!-- Videojs RangeSlider Pluging -->
		<link href="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/videojs-range-slider/rangeslider.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/videojs-range-slider/rangeslider.js"></script>
		<!-- Videojs Markers Pluging -->
		<link href="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/videojs-markers/dist/videojs.markers.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo $wimtv_plugin_root; ?>private/assets/plugins/videojs-markers/dist/videojs-markers.js"></script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-17717703-2', 'auto');
			//ga('send', 'pageview');	REMOVED. INCLUDED IN ANGULARTICS

		</script>


	</div>

	<div id="body">
		<div class="loading-overlay" ng-class="{'loading-visible': $root.globalLoading}">
			<div class="spinner"></div>
			<div class="loading-text">Loading...</div>
		</div>

		<div id="wrapper">
			<div class="wimtv-nav">
				<div class="navbar-default sidebar" role="navigation">
					<div class="sidebar-nav flex center">
						<ul class="nav flex-wrap" id="side-menu">
							<!-- SEARCHBAR LATERALE -->
							<li class="sidemenu flex-wrap">
								<a href="#/userprofile" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-user fa-fw"></i> <span class="menu-title">{{'lang.privatepage.PROFILE' | translate}}</span></a>
							</li>
							<li class="sidemenu dashboard">
								<a href="#/dashboard" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-home fa-fw"></i> <span class="menu-title"><span class="menu-title">Dashboard</span></a>
							</li>
							<li class="sidemenu wimbox">
								<a href="#/wimbox" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-folder-open-o fa-fw"></i> <span class="menu-title">WimBox</span></a>
							</li>
							<li class="sidemenu wimvod">
								<a href="#/wimvod" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-play-circle-o fa-fw"></i> <span class="menu-title">WimVod</span></a>
							</li>
							<li class="sidemenu wimbundle display-none">
								<a href="#/wimbundle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-film fa-fw"></i> <span class="menu-title">WimBundle</span></a>
							</li>
							<li class="sidemenu wimlive">
								<a href="#/wimliveListChannel" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-video-camera fa-fw"></i> <span class="menu-title">WimLive</span></a>
							</li>
							<li class="sidemenu wimcast">
								<a href="#/wimcast" tooltip-placement="top" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-calendar fa-fw"></i> <span class="menu-title">WimCast</span></a>
							</li>
							<li class="sidemenu wimbridge display-none">
								<a href="#/wimbridge/videos" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-window-restore"></i> <span class="menu-title">WimBridge</span></a>
							</li>
							<li class="sidemenu wimtrade display-none">
								<a href="#/wimTrade" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-handshake-o" aria-hidden="true"></i> <span class="menu-title">WimTrade</span></a>
							</li>
							<li class="sidemenu analytics">
								<a href="#/analytics" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bar-chart fa-fw"></i> <span class="menu-title">Analytics</span></a>
							</li>
						</ul>
					</div>
					<!-- /.sidebar-collapse -->
				</div>
			</div>
		</div>
		<div class="ui-view" autoscroll="true"></div>

		<div class="copyright">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 text-center">
						<div class="btn-group margin-bottom-10 dropup">
							<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								{{'lang.privatepage.LANGUAGE' | translate}} <i class="fa fa-globe"></i>
							</button>
							<ul class="dropdown-menu languages hoverSelectorBlock">
								<li ng-class="{active: (currentLang == 'it')}">
									<a href="" ng-click="changeLang('it')">{{'lang.privatepage.ITALIAN' | translate}}
										<i ng-if="(currentLang == 'it')" class="fa fa-check"></i>
									</a>
								</li>
								<li ng-class="{active: (currentLang == 'en')}">
									<a href=""	ng-click="changeLang('en')">{{'lang.privatepage.ENGLISH' | translate}}
										<i ng-if="(currentLang == 'en')" class="fa fa-check"></i>
									</a>
								</li>
								<li ng-class="{active: (currentLang == 'es')}">
									<a href=""	ng-click="changeLang('es')">{{'lang.privatepage.SPANISH' | translate}}
										<i ng-if="(currentLang == 'es')" class="fa fa-check"></i>
									</a>
								</li>
								<li ng-class="{active: (currentLang == 'fr')}">
									<a href=""	ng-click="changeLang('fr')">{{'lang.privatepage.FRENCH' | translate}}
										<i ng-if="(currentLang == 'fr')" class="fa fa-check"></i>
									</a>
								</li>
								<li ng-class="{active: (currentLang == 'pt')}">
									<a href=""	ng-click="changeLang('pt')">{{'lang.privatepage.PORTUGUES' | translate}}
										<i ng-if="(currentLang == 'pt')" class="fa fa-check"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
