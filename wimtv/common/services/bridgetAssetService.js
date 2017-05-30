angular.module("wimtvApp").factory('bridgetAssetService', ['bridgetRestService', function(bridgetRestService) {

	/**
	 * Service Interface: Bridget asset service
	 * Just for the management (crud) of assets and resources of wimbridget. No other action here!
	 */
	return {
		// image
		imageCreate: imageCreate,
		imageRead: imageRead,
		imagePatch: imagePatch,
		imageDelete: imageDelete,
		imageList: imageList,

		// video
		videoCreate: videoCreate,
		videoRead: videoRead,
		videoPatch: videoPatch,
		videoDelete: videoDelete,
		videoList: videoList
	};

	/**
	 * Methods implementation
	 */

	function imageCreate(data, callbackSuccess, callbackError){
		var url = 'private/images';
		bridgetRestService.customCall(
			url,
			'POST',
			undefined,
			data,
			null,
			callbackSuccess,
			callbackError
		);
	}

	function imageRead(imageId, callbackSuccess, callbackError) {
		var url = 'private/images/' + imageId;
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}

	function imagePatch(data, callbackSuccess, callbackError) {
		var url = 'private/images/' + data.id;
		var metadata_set = {
			"metadata_set": [
				{data_type: "title", data: data.title},
				{data_type: "description", data: data.description},
				{data_type: "tags", data: data.tags}
			]
		};
		bridgetRestService.PATCH(url, metadata_set, callbackSuccess, callbackError);
	}

	function imageDelete(imageId, callbackSuccess, callbackError) {
		var url = 'private/images/' + imageId;
		bridgetRestService.DELETE(url, callbackSuccess, callbackError);
	}

	function imageList(callbackSuccess, callbackError) {
		var url = 'private/images';
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}

	function videoCreate(videoMetaData, callbackSuccess, callbackError) {
		var url = 'private/videos/';
		bridgetRestService.POST(url, videoMetaData, callbackSuccess, callbackError);
	}

	function videoRead(videoId, callbackSuccess, callbackError) {
		var url = 'private/videos/' + videoId;
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}

	function videoPatch(data, callbackSuccess, callbackError) {
		var url = 'private/videos/' + data.id;
		var metadata_set = {
				"metadata_set": [
				{data_type: "title", data: data.title},
				{data_type: "description", data: data.description},
				{data_type: "tags", data: data.tags}
			]
		};

		if (data.shotsArray){
			var shotsArray = ((typeof data.shotsArray != "string") ? JSON.stringify(data.shotsArray) : data.shotsArray);
			metadata_set.metadata_set.push({data_type: "shotsArray", data: shotsArray});
		}
		bridgetRestService.PATCH(url, metadata_set, callbackSuccess, callbackError);
	}

	function videoDelete(videoId, callbackSuccess, callbackError) {
		var url = 'private/videos/' + videoId;
		bridgetRestService.DELETE(url, callbackSuccess, callbackError);
	}

	function videoList(callbackSuccess, callbackError) {
		var url = 'private/videos';
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}

}]);
