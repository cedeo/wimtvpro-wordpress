angular.module("wimtvApp").factory('vodService', ['restService', 'resourceService', function(restService, resourceService) {

	/**
	 * Service interface
	 */
	return {
		getVideos: getVideos,
		updateVideo: updateVideo,
		publicGetVideos: publicGetVideos,
		deleteVideo: deleteVideo,
		publicGet: publicGet,
		privateGet: privateGet,
		play: play,
		preview: preview,
		pay: pay
	};


	function getVideos(filters, callbackSuccess, failureCallback) {
		filters = resourceService.validateFilters(filters);
		restService.wimTvRestPOST('/api/search/vod/contents', filters, callbackSuccess, failureCallback);
	}

	function updateVideo(video, callbackSuccess, callbackError) {
		restService.wimTvRestPOST('/api/vod/' + video.vodId, video, callbackSuccess, callbackError);
	}

	function publicGetVideos(filters, callbackSuccess, callbackError) {
		filters = resourceService.validateFilters(filters);
		restService.wimTvRestPOST('/api/public/search/vod/contents', filters, callbackSuccess, callbackError);
	}

	function get(type, vodId, success, failure) {
		var url = "/api";
		if(type === "public") {
			url += "/public";
		}
		url += "/vod/" + vodId;
		restService.wimTvRestGET(url, success, failure);
	}

	function deleteVideo(vodId, callbackSuccess, callbackError) {
		restService.wimTvRestAuthDELETE('/api/vod/' + vodId, callbackSuccess, callbackError);
	}

	function play(vodId, success, failure) {
		resourceService.play('vod', vodId, success, failure);
	}
	function preview(vodId, success, failure) {
		resourceService.preview('vod', vodId, success, failure);
	}

	function pay(vodId, options, success, failure) {
		resourceService.pay('vod', vodId, options, success, failure)
	}

	function privateGet(vodId, success, failure) {
		get('private', vodId, success, failure);
	}
	function publicGet(vodId, success, failure) {
		get('public', vodId, success, failure);
	}
}]);