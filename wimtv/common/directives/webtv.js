angular.module("wimtvApp").directive("webTv", function () {
	return {
		restrict: 'E',
		scope: {
			webtv: '='
		},
		templateUrl: wimtv_plugin_path + 'common/directives/webtv.html'
	}
});