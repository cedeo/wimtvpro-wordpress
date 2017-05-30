angular.module("wimtvApp").directive("bridgetHomeHeaders", [ '$timeout', '$interval', function ($timeout, $interval) {
	return {
		restrict: 'E',
		scope: {
			location: '=',
			addText: '=',
			showAdd: '=',
			addAction: '&',
			refreshAction: '&'
		},
		link: function (scope, element, attribute) {
			scope.refreshing = false;
			scope.refresh = refresh;

			startRefreshing();

			function startRefreshing(){
				var promise = $interval(scope.refresh, 6000);
				scope.$on('$destroy',function(){
					if(promise)
					$interval.cancel(promise);
				});
			}

			function refresh(){
				scope.refreshing = true;
				scope.refreshAction();
				$timeout(function () {
					scope.refreshing = false;
				}, 1250);
			}
		},
		templateUrl: wimtv_plugin_path + 'common/directives/bridgetHomeHeaders.html'
	}
}]);
