angular.module("wimtvApp").directive("bundle", function () {
	return {
		restrict: 'E',
		scope: {
			bundle: '=bundle',
			hideLink: '=hideLink'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/bundle.html'
	}
});