angular.module("wimtvApp").directive("marketVideoThumb", function () {
	return {
		restrict: 'E',
		scope: {
			video : '=video',
            showLicense : '=showLicense'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/market-video-thumb.html'
	}
});