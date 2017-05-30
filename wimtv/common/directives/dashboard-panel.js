angular.module("wimtvApp").directive("dashboardPanel", function () {
	return {
		restrict: 'E',
		scope: {
			colorPanel: '=',
			iconClass: '=',
			title: '=',
			linkHeader: '=',
			linkFirstLine: '=',
			linkSecondLine: '=',
			keyMessageWelcome: '=',
			keyMessageFirstLine: '=',
			keyMessageSecondLine: '='
		},
		templateUrl: wimtv_plugin_path + 'common/directives/dashboard-panel.html'
	}
});