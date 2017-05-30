angular.module("wimtvApp").config(["$stateProvider", function ($stateProvider) {
	$stateProvider.state('stateDashboard', {
		url: "/dashboard",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateDashboard.html",
		controller: "dashboardController",
		controllerAs: "dashCtrl"
	});
}]);

angular.module("wimtvApp").controller('dashboardController', ['$rootScope', 'userCustomizationService', function($rootScope, userCustomizationService) {
	var dashCtrl = this;

	init();

	function init() {
		dashCtrl.items = [
			{
				colorPanel: 'panel-blue',
				title: 'WimBox',
				iconClass: 'fa-folder-open-o',
				linkHeader: '#wimbox',
				keyMessageWelcome: 'lang.wimbox.WELCOME'
			},{
				colorPanel: 'panel-green',
				title: 'WimVod',
				iconClass: 'fa-play-circle-o',
				linkHeader: '#wimvod',
				keyMessageWelcome: 'lang.wimvod.WELCOME'
			}, {
				colorPanel: 'panel-yellow',
				title: 'WimLive',
				iconClass: 'fa-video-camera',
				linkHeader: '#wimliveListChannel',
				keyMessageWelcome: 'lang.wimchannel.WELCOME'
			}, {
				colorPanel: 'panel-red',
				title: 'WimCast',
				iconClass: 'fa fa-calendar',
				linkHeader: '#/wimcast',
				keyMessageWelcome: 'lang.wimcast.WELCOME'
			}, {
				colorPanel: 'panel-analytics',
				title: 'Analytics',
				iconClass: 'fa-bar-chart-o',
				linkHeader: '#/analytics',
				keyMessageWelcome: 'lang.analytics.WELCOME'
			}, {
				colorPanel: 'panel-idea',
				title: 'lang.dashboard.CONTACT-TITLE',
				iconClass: 'fa-lightbulb-o',
				linkHeader: 'mailto:info@wimlabs.com?subject=WimTVPro',
				keyMessageWelcome: 'lang.dashboard.CONTACT-INTRO'
			}
		]
	}
}]);
