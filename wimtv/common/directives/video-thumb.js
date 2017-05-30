angular.module("wimtvApp").directive("videoThumb", function () {
	return {
		restrict: 'E',
		scope: {
			video: '=video',
			showUser: '=showUser',
			showPrice: '=showPrice'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/video-thumb.html'
	}
});