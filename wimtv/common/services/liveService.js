angular.module("wimtvApp").factory('liveService', ['restService', 'resourceService', function (restService, resourceService) {

	return {
		privateGet: privateGet,
		publicGet: publicGet,
		play: play,
		add: add,
		update: update,
		remove: del,
		privateSearch: privateSearch,
		publicSearch: publicSearch,
		getLiveEventsByChannelId: getLiveEventsByChannelId,
		getLiveEventsByChannelIdPublic: getLiveEventsByChannelIdPublic
	};

	function privateGet(id, resolve, reject) {
		if(id) {
			restService.wimTvRestAuthGET(
				'/api/live/event/'+id,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		} else {
			reject()
		}
	}
	function publicGet(id, resolve, reject) {
		if(id) {
			restService.wimTvRestAuthGET(
				'/api/public/live/event/'+id,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		} else {
			reject()
		}
	}
	function play(id, resolve, reject) {
		resourceService.play('live', id, resolve, reject);
	}

	function add(channelId, data, successCallback, failureCallback) {
		if(channelId) {
			restService.wimTvRestAuthPOST(
				'/api/live/channel/' + channelId + '/event',
				data,
				successCallback,
				failureCallback);
		} else {
			failureCallback();
		}
	}

	/**
	 *
	 * @param eventId
	 * @param successCallback
	 * @param failureCallback
	 */
	function del(eventId, successCallback, failureCallback) {
		if(eventId) {
			restService.wimTvRestAuthDELETE(
				'/api/live/event/' + eventId,
				successCallback,
				failureCallback);
		} else {
			failureCallback();
		}
	}

	/**
	 * Updates a Live Event
	 * @param liveEvent to update
	 * @param successCallback
	 * @param failureCallback
	 */
	function update(liveEvent, successCallback, failureCallback) {
		restService.wimTvRestAuthPOST('/api/live/event/' + liveEvent.eventId, liveEvent, successCallback, failureCallback);
	}


	function getLiveEventsByChannelId(channelId, pastIncluded, successCallback, failureCallback) {
		var data = {
			channelId: channelId,
			pastIncluded: pastIncluded
		};
		search(data, successCallback, failureCallback);
	}
	function getLiveEventsByChannelIdPublic(channelId, successCallback, failureCallback) {
		var data = {channelId: channelId};
		publicSearch(data, successCallback, failureCallback);
	}

	/**
	 * private search in authenticated pages
	 * @param filters: pagination + queryString + public (indexed in wimtv public pages)
	 * @param successCallback
	 * @param failureCallback
	 */
	function privateSearch(filters, successCallback, failureCallback) {
		filters = resourceService.validateFilters(filters);
		search(filters, successCallback, failureCallback);
	}

	function search(data, successCallback, failureCallback) {
		restService.wimTvRestAuthPOST(
			'/api/search/live/events/',
			data,
			successCallback,
			failureCallback);
	}

	/**
	 * private search in NON authenticated pages
	 * @param filters pagination + querystring + usercode
	 * @param successCallback
	 * @param failureCallback
	 */
	function publicSearch(filters, successCallback, failureCallback) {
		filters = resourceService.validateFilters(filters);
		restService.wimTvRestAuthPOST(
			'/api/public/search/live/events/',
			filters,
			successCallback,
			failureCallback);
	}
}]);