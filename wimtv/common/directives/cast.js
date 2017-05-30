angular.module("wimtvApp").directive("cast", ["config", "castChannelService", "$interval", function (config, castChannelService, $interval) {
	return {
		restrict: 'E',
		scope: {
			cast: '=',
			fixedHeight: '='
		},
		templateUrl: wimtv_plugin_path + 'common/directives/cast.html',
		link: function (scope, element) {
			scope.getThumbnail = getThumbnail;

			checkIfOnAir(scope.cast);

			function getThumbnail(cast) {
				// var cast = scope.cast;
				if(cast.thumbnailId) {
					return config.getThumbnailUrl(cast.thumbnailId);
				} else if(cast.publisher) {
					return config.getThumbnailUrl(cast.publisher.thumbnailId)
				}
				return config.defaultThumbnail;
			}

			function checkIfOnAir(cast) {
				var filters = {
					pageSize: 10,
					pageIndex: 0,
					channelId: cast.channelId
				};
				castChannelService.programsSearch(filters, success, failure);

				function success(response) {
					if(response.data && response.data.items && response.data.items.length > 0) {
						var start = moment(response.data.items[0].startDate.date + "-" + response.data.items[0].startDate.time, "DD/MM/YYYY-HH:mm:ss");
						var end = moment(response.data.items[0].endDate.date + "-" + response.data.items[0].endDate.time, "DD/MM/YYYY-HH:mm:ss");
						cast.onAir = moment().isBetween(start, end);
						cast.nextEvent = response.data.items[0];
					}
				}
				function failure(response) {
					console.log(response);
				}
			}
		}
	}
}]);