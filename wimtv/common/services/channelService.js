angular.module("wimtvApp").factory('channelService', ['restService', 'resourceService', function(restService, resourceService) {

	return {
		get: get,
		add: add,
		update: update,
		remove: del,
		privateSearch: privateSearch,
		publicSearch: publicSearch,
		getChannelName: getChannelName
	};

	function add(data, successCallback, failureCallback) {
		restService.wimTvRestAuthPOST(
			'/api/live/channel/',
			data,
			successCallback,
			failureCallback);
	}
	function del(channelId, successCallback, failureCallback) {
		if(channelId) {
			restService.wimTvRestAuthDELETE(
				'/api/live/channel/' + channelId,
				successCallback,
				failureCallback);
		} else {
			failureCallback();
		}
	}
	function get(channelId, successCallback, failureCallback) {
		if(channelId) {
			restService.wimTvRestAuthGET(
				'/api/live/channel/' + channelId + '/event',
				successCallback,
				failureCallback);
		} else {
			failureCallback();
		}
	}
	function update(channel, successCallback, failureCallback) {
		if(channel.channelId) {
			restService.wimTvRestAuthPOST('/api/live/channel/' + channel.channelId, channel, successCallback, failureCallback);
		} else {
			failureCallback();      // TODO: improve error handling
		}
	}

	function getChannelName(channelName, successCallback, failureCallback) {
		if(channelName) {
			restService.wimTvRestAuthGET(
				'/api/public/live/streampath?base=' + channelName,
				successCallback,
				failureCallback);
		} else {
			failureCallback();
		}
	}

	function privateSearch(filters, success, failure) {
		search('private', filters, success, failure);
	}
	function publicSearch(filters, success, failure) {
		search('public', filters, success, failure);
	}
	function search(type, filters, successCallback, failureCallback) {
		var url = "/api";
		if(type === "public") {
			url += "/public";
		}
		url += "/search/live/channels";
		filters = resourceService.validateFilters(filters);
		restService.wimTvRestAuthPOST(
			url,
			filters,
			successCallback,
			failureCallback
		);

	}
}]);