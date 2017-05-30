angular.module("wimtvApp").directive("boxList", ["wimBoxService", "$rootScope", "config", function (wimBoxService, $rootScope, config) {
	return {
		restrict: 'E',
		scope: {
			pageSize: '=',
			clickAction: '&',
			addTo: '@',
			innerClass: '@',
			wimCast: '@',
			wimCastCallback: '&'
		},
		templateUrl: wimtv_plugin_path + 'common/directives/box-list.html',
		link: function (scope, element, attribute) {
			
			init();
			scope.getVideos = getVideos;
			scope.resetQuery = resetQuery;
			scope.click = click;

			var previousQuery = "";

			function init() {
				scope.pageIndex = 1;
				scope.query = "";
				scope.videos = null;
				getVideos();
			}

			function getVideos(pageIndex) {
				if(pageIndex) { // why is this necessary?
					// seems that angular is not updating scope.pageIndex
					scope.pageIndex = pageIndex;
				}
				if(scope.query !== previousQuery) {
					// reset pageIndex in case of changing the querySearch
					scope.pageIndex = 1;
				}
				previousQuery = scope.query;
				var filters = {
					pageIndex: scope.pageIndex - 1,
					pageSize: scope.pageSize ? scope.pageSize : 12,
					queryString: scope.query
				};
				if(scope.wimCast && scope.wimCastCallback) {
					// call custom wimcastFunction
					scope.wimCastCallback();
				}
				wimBoxService.searchForContents(filters, success, failure);
				function success(response) {
					scope.videosLoaded = true;
					scope.videos = response.data;
				}
				function failure(response) {
					config.error('lang.wimbox.GETPAGE.ERROR', response);
				}
			}

			function click(video) {
				if(video.status==='READY') {
					scope.clickAction({boxItem: video});
				} else {
					console.log("unable to add a not ready video");
				}
			}

			function resetQuery() {
				scope.query = '';
				getVideos();
			}

		}
	}
}]);