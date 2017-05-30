angular.module("wimtvApp").directive("videoList", function () {
	return {
		restrict: 'E',
		scope: {
			videos: '=videos',
			loaded: '=loaded',
			pageIndex: '=pageIndex',
			changePage: '=changePage',
			itemsPerPage: '=itemsPerPage'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/video-list.html'
	}
});