app.factory('castChannelService', ['restService', 'resourceService', function(restService, resourceService) {

	/**
	 * Service interface
	 */
	return {
		create: create,
		update: update,
		remove: remove,
		privateGet: privateGet,
		publicGet: publicGet,
		privateSearch: privateSearch,
		publicSearch: publicSearch,
		generateStreamPath: generateStreamPath,
		calendarGet: calendarGet,
		calendarCopy: calendarCopy,
		calendarDelete: calendarDelete,
		programsSearch: programsSearch,
		play: play
	};

	/**
	 * Creates a new WimCast Channel
	 * @param newChannel
	 * @param success
	 * @param failure
	 */
	function create(newChannel, success, failure) {
		restService.wimTvRestPOST("/api/cast/channel", newChannel, success, failure);
	}

	/**
	 * Updates a given channel.
	 * @param channel
	 * @param success
	 * @param failure
	 */
	function update(channel, success, failure) {
		restService.wimTvRestPOST("/api/cast/channel/" + channel.channelId, channel, success, failure);
	}

	/**
	 * Deletes a channel by his channelId
	 * @param channelId
	 * @param success
	 * @param failure
	 */
	function remove(channelId, success, failure) {
		restService.wimTvRestAuthDELETE("/api/cast/channel/" + channelId, success, failure);
	}

	/**
	 * Get a cast channel in public pages
	 * @param channelId
	 * @param success
	 * @param failure
	 */
	function publicGet(channelId, success, failure) {
		get('public', channelId, success, failure);
	}

	/**
	 * Gets a cast channel in private pages
	 * @param channelId
	 * @param success
	 * @param failure
	 */
	function privateGet(channelId, success, failure) {
		get('private', channelId, success, failure);
	}

	/**
	 * Private method for getting a channel, both in private or public pages
	 * @param type		public or (everything else) private
	 * @param channelId
	 * @param success
	 * @param failure
	 */
	function get(type, channelId, success, failure) {
		var url = "/api/";
		if(type === 'public') {
			url += 'public/';
		}
		url += 'cast/channel/' + channelId;
		restService.wimTvRestGET(url, success, failure);
	}

	/**
	 * Search channels in public pages
	 * @param filters
	 * @param success
	 * @param failure
	 */
	function publicSearch(filters, success, failure) {
		search('public', filters, success, failure);
	}

	/**
	 * Search channels in private pages
	 * @param filters
	 * @param success
	 * @param failure
	 */
	function privateSearch(filters, success, failure) {
		search('private', filters, success, failure);
	}

	/**
	 * Private method for searching cast channels, both in private or public pages
	 * @param type		public or (everything else) private
	 * @param filters
	 * @param success
	 * @param failure
	 */
	function search(type, filters, success, failure) {
		var url = "/api/";
		if(type === 'public') {
			url += 'public/';
		}
		url += 'search/cast/channels';
		restService.wimTvRestPOST(url, filters, success, failure);
	}

	/**
	 * Generates a new channel streampath, starting from the name passed.
	 * @param baseName
	 * @param success
	 * @param failure
	 */
	function generateStreamPath(baseName, success, failure) {
		restService.wimTvRestGET('/api/public/cast/streampath?base=' + baseName, success, failure);
	}

	/**
	 * Gets the programming, filtered by filters
	 * @param filters channelId, startDate (yyyyMMdd), dayCount (number of days)
	 * @param success
	 * @param failure
	 */
	function calendarGet(filters, success, failure) {
		restService.wimTvRestGET('/api/cast/channel/' + filters.channelId + '/calendar/' + filters.startDate + '/' + filters.dayCount, success, failure);
	}

	/**
	 * Copy a programming period, defined in data, to another programming period, defined in data.
	 * Could be both a "same-channel-copy" or a "different-channel-copy".
	 * @param data
	 * @param success
	 * @param failure
	 */
	function calendarCopy(data, success, failure) {
		restService.wimTvRestPOST('/api/cast/calendar/copy/', data, success, failure);
	}

	/**
	 * Delete all programs in those days
	 * @param filters channelId, startDate (yyyyMMdd), dayCount (number of days)
	 * @param success
	 * @param failure
	 */
	function calendarDelete(filters, success, failure) {
		restService.wimTvRestAuthDELETE('/api/cast/channel/' + filters.channelId + '/calendar/' + filters.startDate + '/' + filters.dayCount, success, failure);
	}

	/**
	 * Public search for programs in the whole wimcast. Could be filtered
	 * @param filters useful ones are: userCode (filters by user), channelId (filters by castChannel)
	 * @param success
	 * @param failure
	 */
	function programsSearch(filters, success, failure) {
		restService.wimTvRestPOST('/api/public/search/cast/programs', filters, success, failure);
	}
	
	
	function play(channelId, success, failure) {
		resourceService.play("cast", channelId, success, failure);
		// restService.wimTvRestPOST('/api/cast/channel/' + channelId + '/play', null, success, failure);
	}

}]);