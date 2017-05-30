app.factory('castStreamService', ['restService', 'config', function(restService, config) {

	/**
	 * Service interface
	 */
	return {
		create: create,
		update: update,
		remove: remove,
		get: get,
		search: search
	};

	/**
	 * Creates a new WimCast Stream
	 * @param newStream
	 * @param success
	 * @param failure
	 */
	function create(newStream, success, failure) {
		restService.wimTvRestPOST("/api/cast/stream", newStream, success, failure);
	}

	/**
	 * Updates a given stream.
	 * @param stream
	 * @param success
	 * @param failure
	 */
	function update(stream, success, failure) {
		restService.wimTvRestPOST("/api/cast/stream/" + stream.streamId, stream, success, failure);
	}

	/**
	 * Deletes a stream by his streamId
	 * @param streamId
	 * @param success
	 * @param failure
	 */
	function remove(streamId, success, failure) {
		restService.wimTvRestAuthDELETE("/api/cast/stream/" + streamId, success, failure);
	}

	/**
	 * Method for getting a stream
	 * @param streamId
	 * @param success
	 * @param failure
	 */
	function get(streamId, success, failure) {
		restService.wimTvRestGET("/api/cast/stream/" + streamId, success, failure);
	}


	/**
	 * Method for searching cast streams
	 * @param filters
	 * @param success
	 * @param failure
	 */
	function search(filters, success, failure) {
		restService.wimTvRestPOST("/api/search/cast/streams/", filters, success, failure);
	}

}]);