function VsaStats (username, apiHost) {
	this.apiHost = apiHost || EndPointConfig().vsa;
	this.username = username;
	this.getApiEndpoint = function(endpoint) {
		endpoint = endpoint.replace('<username>', this.username)
			.replace('<stream_id>', this.streamId);
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

VsaStats.infoOnUserApi = '/api/users/<username>';
VsaStats.prototype.infoOnUser = function(cb, startTime, endTime) {
	$.getJSON(this.getApiEndpoint(VsaStats.infoOnUserApi),
		this.getTime(startTime, endTime))
		.done(function(data){
			cb({
				storage: data.storage,
				traffic: data.traffic
			});
		});
};

VsaStats.infoOnStreams = '/api/users/<username>/streams';
VsaStats.prototype.infoOnStreams = function(cb, startTime, endTime) {
	$.getJSON(this.getApiEndpoint(VsaStats.infoOnStreams),
		this.getTime(startTime, endTime))
		.done(function(data){
			_.each(data, function(item) {
				VsaStats.decorateView.call(item);
			});
			allVideos = _.partition(data, function(video) {
				// [ [live, ...], [vod, ...] ]
				return _.isMatch(video, {"type": "LIVE"});
			});
			cb({
				live: allVideos[0],
				vod: allVideos[1]
			});
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