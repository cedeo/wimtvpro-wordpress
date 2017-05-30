angular.module("wimtvApp").directive("live", ["config", "$interval", function (config, $interval) {
	return {
		restrict: 'E',
		scope: {
			live: '='
		},
		templateUrl: wimtv_plugin_path + 'common/directives/live.html',
		link: function (scope, element) {
			var startTime = moment(scope.live.eventDate.date + "-" + scope.live.eventDate.time, "DD/MM/YYYY-HH:mm:ss");
			handleInterval(startTime);

			scope.$on('changeLanguage',function (event, data) {
				startTime.locale(data);
				handleInterval(startTime)
			});

			scope.getThumbnail = getThumbnail;

			function getThumbnail() {
				var live = scope.live;
				if(live.thumbnailId) {
					return config.getThumbnailUrl(live.thumbnailId);
				} else if(live.channel) {
					if(live.channel.thumbnailId) {
						return config.getThumbnailUrl(live.channel.thumbnailId);
					} else if(live.channel.publisher){
						return config.getThumbnailUrl(live.channel.publisher.thumbnailId)
					}
				}
				return config.defaultThumbnail;
			}

			function handleInterval(time) {
				var reverse = false;
				var difference = moment.duration(time.diff(moment()));
				if(difference > 86400000) {
					// if the live starts in more than one day, show how much time we still have
					scope.live.timer = time.fromNow();
				} else {
					if(difference < 0) {
						// if the live is already broadcasting, show a positive counter
						reverse = true;
					}
					$interval(function () {
						var diff = 0;
						if(reverse) {
							diff = moment.duration(moment().diff(time));
						} else {
							diff = moment.duration(time.diff(moment()));
						}
						scope.live.timer = diff.hours() + ":" + diff.minutes() + ":" + diff.seconds();
					}, 1000);
				}
			}
		}
	}
}]);