app.factory('dailyProgrammingService', ['restService', function(restService) {

	/**
	 * Service interface
	 */
	return {
		privateGet: privateGet,
		publicGet: publicGet,
		saveEvents: saveEvents,
		insertEvent: insertEvent,
		movesEvent: movesEvent,
		removesEvent: removesEvent
	};

	/**
	 * Get a cast channel daily programming in public pages
	 * @param channelId
	 * @param date 		DateString format: yyyyMMdd (or YYYYMMDD for moment.js)
	 * @param success
	 * @param failure
	 */
	function publicGet(channelId, date, success, failure) {
		get('public', date, channelId, success, failure);
	}

	/**
	 * Gets a cast channel daily Programming in private pages
	 * @param channelId
	 * @param date 		DateString format: yyyyMMdd (or YYYYMMDD for moment.js)
	 * @param success
	 * @param failure
	 */
	function privateGet(channelId, date, success, failure) {
		get('private', date, channelId, success, failure);
	}

	/**
	 * Private method for getting a channel daily Programming, both in private or public pages
	 * @param type		public or (everything else) private
	 * @param date 		DateString format: yyyyMMdd (or YYYYMMDD for moment.js)
	 * @param channelId
	 * @param success
	 * @param failure
	 */
	function get(type, date, channelId, success, failure) {
		var url = "/api/";
		if(type === 'public') {
			url += 'public/';
		}
		url += 'cast/channel/' + channelId + '/dailyprogramming/' + date;
		restService.wimTvRestGET(url, success, failure);
	}

	/**
	 * Inserts one or more elements in the daily programming
	 * @param channelId		the channel reference
	 * @param date 		DateString format: yyyyMMdd (or YYYYMMDD for moment.js)
	 * @param data			the array of data to be inserted
	 * @param success
	 * @param failure
	 */
	function insertEvent(channelId, date, data, success, failure) {
		restService.wimTvRestPOST('/api/cast/channel/' + channelId + '/dailyprogramming/' + date + '/insert', data, success, failure);
	}

	/**
	 * Moves an event by given metadata
	 * @param channelId
	 * @param date 		DateString format: yyyyMMdd (or YYYYMMDD for moment.js)
	 * @param metadata
	 * @param success
	 * @param failure
	 */
	function movesEvent(channelId, date, metadata, success, failure) {
		restService.wimTvRestPOST('/api/cast/channel/' + channelId + '/dailyprogramming/' + date + '/move', metadata, success, failure);
	}

	/**
	 * Removes all programs from a daily programming
	 * @param channelId
	 * @param date
	 * @param success
	 * @param failure
	 */
	function removesEvent(channelId, date, success, failure) {
		restService.wimTvRestAuthDELETE('/api/cast/channel/' + channelId + '/dailyprogramming/' + date, success, failure);
	}

	
	function saveEvents(channelId, date, programs, success, failure) {
		restService.wimTvRestAuthPOST('/api/cast/channel/' + channelId + '/dailyprogramming/' + date, programs, success, failure)
	}
}]);