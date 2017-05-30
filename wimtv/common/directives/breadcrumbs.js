angular.module("wimtvApp").directive("breadcrumbs", function () {
	return {
		restrict: 'E',
		scope: {
			webTv: '=webTv'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/breadcrumbs.html'
	}
});