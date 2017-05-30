angular.module("wimtvApp").factory('wimBoxService', ['restService', 'resourceService', function(restService, resourceService) {

	/**
	 * Service interface
	 */
	return {
		searchForContents : searchForContents,
		updateVideo : updateVideo,
		publishVideo : publishVideo,
		deleteVideo : deleteVideo,
		playBoxItem : playBoxItem,
		getBoxItemInfo : getBoxItemInfo,
		downloadVideo : downloadVideo
	};

	function playBoxItem(boxId, callbackSuccess,callbackError) {
		resourceService.play("box", boxId, callbackSuccess, callbackError);
	}

	/**
	* Return informations about a box item
	* @function getBoxItemInfo
	* @param boxItem - WimTv box item
	*/
	function getBoxItemInfo(boxId, callbackSuccess,callbackError){
		var url = "/api/box/"+boxId+"/play";
		restService.wimTvRestPOST(url, {}, callbackSuccess, callbackError);
	}

	function searchForContents(filters, callbackSuccess, failureCallback) {
		filters = resourceService.validateFilters(filters);
		restService.wimTvRestPOST(
			'/api/search/box/contents',
			filters,
			callbackSuccess,
			failureCallback);
	}


    /**
     * Update metadata & thumbnail video.
     * @param video json containing video metadata
     * @param callbackSuccess
     * @param callbackError
     */
    function updateVideo(video,callbackSuccess, callbackError) {
		restService.wimTvRestAuthPOST(
			'/api/box/' + video.boxId,
			video,
			callbackSuccess,
			callbackError);
	}

	/**
	 * Publish video
	 * @param boxId video box identifier
	 * @param data containing licence info
	 * @pstaram callbackSuccess
	 * @param callbackError
	 */
	function publishVideo(boxId, data, callbackSuccess, callbackError) {
		restService.wimTvRestAuthPOST(
			'/api/box/' + boxId + '/vod',
			data,
			callbackSuccess,
			callbackError
		);
	}

	/**
	 * Requests the token for the download
	 * @param video
	 * @param callbackSuccess
	 * @param callbackError
	 */
	function downloadVideo(video, callbackSuccess, callbackError) {
		restService.wimTvRestGET('/api/content/' + video.contentId + '/download', callbackSuccess, callbackError);
	}

	function deleteVideo(boxId, callbackSuccess, callbackError) {
		restService.wimTvRestAuthCustom(
			'/api/box/' + boxId,
			'DELETE',
			null,
			null,
			callbackSuccess,
			callbackError
		);
	}

}]);

angular.module("wimtvApp").service('uploadQueue', ['$rootScope', '$q', 'Upload', 'config', function ($rootScope, $q, Upload, config) {
	var queue = [];			// upload queue

	/**
	 * Execute next element of the queue
	 */
	var next = function() {
		var task = queue[0];	// task to execute;
		var video = task.video;

		// build upload datas
		var jsonData = {};
		for(var key in video.metadata) {
			if (video.metadata.hasOwnProperty(key)) {
				jsonData[key] = video.metadata[key];
			}
		}
		jsonData.file = video;
		jsonData.contentIdentifier = "urn:wim:tv:content:" + config.generateUUID();

		// upload the data
		Upload.upload({
			url: $rootScope.wimtvUrl + '/api/box',
			data: jsonData,
			arrayKey: '',
			headers: {
				"X-Wimtv-progressbarId": task.progressId
			}
		}).then(function(data) {
			queue.shift();
			data.queue_length = queue.length;			// to get in controller the LAST upload
			video.completedUpload = true;
			task.deferred.resolve(data);
			if (queue.length>0) {
				next();
			}
		}, function(err) {
			queue.shift();
			task.deferred.reject(err);
			if (queue.length>0) {
				next();
			}
		}, function (loading) {
			var progressPercentage = parseInt(100.0 * loading.loaded / loading.total);
			if (progressPercentage < 1) {
				progressPercentage = 1;
			}
			video.progress = progressPercentage;
			video.loaded = loading.loaded;
			video.total = loading.total;
		})
		;
	};

	return function(video) {
		var deferred = $q.defer();
		queue.push({
			video: video,
			deferred: deferred}
		);
		if (queue.length===1) {
			next();
		}
		return deferred.promise;
	};
}]);
