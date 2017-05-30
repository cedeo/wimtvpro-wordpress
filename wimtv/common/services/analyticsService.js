angular.module("wimtvApp").factory('analyticsService', ['genericRestService', 'config', '$timeout', function (genericRestService, config, $timeout) {

	var vsaStats;

	var apiHost = EndPointConfig().vsa;

	function getGlobalStats (user, from, to, callBackSuccess) {
		var url = apiHost + '/api/users/' + user;
		genericRestService.GET(url, getTime(from, to)).success(success);
		// $.getJSON(apiHost + '/api/users/' + user, getTime(from, to)).done(success);
		function success(data) {
			$timeout(function () {
				callBackSuccess({
					storage: toByteString(data.storage),
					traffic: toByteString(data.traffic)
				});
			}, 10);
		}
	}

	function getGlobalStreams (user, from, to, callBackSuccess) {
		var url = apiHost + '/api/users/' + user + '/streams';
		genericRestService.GET(url, getTime(from, to)).success(success);
		// $.getJSON(url, getTime(from, to)).done(success);

		function success(data){
			_.each(data, function(item) {
				VsaStats.decorateView.call(item);
			});
			var allVideos = _.partition(data, function(video) {
				// [ [live, ...], [vod, ...] ]
				return _.isMatch(video, {"type": "LIVE"});
			});

			$timeout(function () {
				var data = {
					live: allVideos[0],
					vod: allVideos[1]
				};
				for (var i = data.vod.length - 1; i >= 0; i--) {
					if (!data.vod[i].type) {
						data.vod.splice(i, 1);
					} else {
						data.vod[i].trafficString = toByteString(data.vod[i].traffic);
					}
				}
				for (i = data.live.length - 1; i >= 0; i--) {
					if (!data.live[i].type) {
						data.live.splice(i, 1);
					} else {
						data.live[i].trafficString = toByteString(data.live[i].traffic);
					}
				}
				callBackSuccess(data);
			}, 1);
		}
	}

	function getVideoStreams (user, streamId, from, to, callbackSuccess) {
		initVsa(user);
		// TODO
	}

	/**
	 * Reads single video stats and builds a map with viewCount based on single days.
	 * @param data
	 * @param filters
	 * @param type
	 * @returns {{}}
	 */
	function buildMap(data, filters, type) {
		var divider = 86400000;		// default divider
		switch (type) {
			case "hour":
				divider = 3600000;
				break;
			case "day":
				divider = 86400000;
				break;
			case "week":
				divider = 604800000;
				break;
			case "month":
				divider = 2592000000;
				break;
			default:
				divider = 86400000;
				type = "day";
				break;
		}

		var start = moment(filters.start).startOf(type).toDate().getTime();
		var end = moment(filters.end).endOf(type).toDate().getTime();
		var diff = end - start;
		var days = Math.ceil(diff / divider);

		var viewMap = {};
		if(!viewMap.total) {
			viewMap.total = {};
		}
		for (var i = 0; i < days; i++) {
			var time = (start + i * divider) /1000;
			viewMap.total[time] = 0;
		}

		// TODO: this isn't a good code
		for (var k in data.vod) {
			var vod = data.vod[k];

			if(!viewMap[vod.title]) {
				viewMap[vod.title] = {};
			}

			for (var i = 0; i < days; i++) {
				var time = (start + i * divider) /1000;
				viewMap[vod.title][time] = 0;
			}

			for (var i in vod.views_expanded) {
				var view = vod.views_expanded[i];
				var time = moment(view.start_time).startOf(type).unix();
				if(!viewMap.total[time]) {
					viewMap.total[time] = 0;
				}
				if(!viewMap[vod.title][time]) {
					viewMap[vod.title][time] = 0;
				}
				viewMap[vod.title][time]++;
				viewMap.total[time]++;
			}
		}

		var liveMap = {};
		for  (var k in data.live) {
			var live = data.live[k];
			if(!liveMap.labels) {
				liveMap.labels = [];
			}
			if(!liveMap.values) {
				liveMap.values = [];
			}
			liveMap.labels.push(live.title);
			liveMap.values.push(live.views);
		}

		return {
			vod: viewMap,
			live: liveMap
		};
	}

	return {
		getGlobalStats: getGlobalStats,
		getGlobalStreams: getGlobalStreams,
		getVideoStreams: getVideoStreams,
		buildMap: buildMap
	};


	// SUPPORT FUNCTIONS
	function toByteString(input) {
		// do not discuss. It works
		var units = ["B", "KB", "MB", "GB", "TB"];
		for (var i = 0; i < units.length; i++) {
			if(input > 1000) {
				input = Math.round(input/10) / 100;
			} else {
				return input + " " + units[i];
			}
		}

	}

	function getTime(startTime, endTime) {
		var time = {};
		if (startTime) { time.from = startTime }
		if (endTime) { time.to = endTime }
		return time;
	}

	function decorateView() {
		var views = this.views_expanded;
		this.countDuration = _.reduce(views, function(memo, val){
			return memo + parseInt(val.duration); }, 0);
		views.forEach(function(view, index) {
			views[index].start_time = parseInt(view.end_time) - (parseInt(view.duration)*1000);
		});
	}

}]);
function VsaStats (username, apiHost) {
	this.apiHost = apiHost || EndPointConfig().vsa;
	this.username = username;
	this.getApiEndpoint = function(endpoint) {
		endpoint = endpoint.replace('<username>', this.username).replace('<stream_id>', this.streamId);
		return this.apiHost + endpoint;
	};
	this.getTime = function(startTime, endTime) {
		var time = {};
		if (startTime) { time.from = startTime }
		if (endTime) { time.to = endTime }
		return time;
	};
}

VsaStats.decorateView = function() {
	var views = this.views_expanded;
	this.countDuration = _.reduce(views, function(memo, val){
		return memo + parseInt(val.duration); }, 0);
	views.forEach(function(view, index) {
		views[index].start_time = parseInt(view.end_time) - (parseInt(view.duration)*1000);
	});
};

VsaStats.infoOnStreamDetailed = '/api/users/<username>/streams/<stream_id>';
VsaStats.prototype.infoOnStreamDetailed = function(cb, startTime, endTime, streamId) {
	if (streamId) {
		this.streamId = streamId;
		$.getJSON(this.getApiEndpoint(VsaStats.infoOnStreamDetailed),
			this.getTime(startTime, endTime))
			.done(function(data){
				VsaStats.decorateView.call(data);
				cb(data);
			});
	}
	else {
		cb({error: "set a stream id"});
	}

};