angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimbridgetVideos', {
		url: "/wimbridge/videos",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbridgetVideos.html",
		controller: "wimbridgetVideosController as vm"
	})
});


angular.module("wimtvApp").controller('wimbridgetVideosController', ['$scope', '$interval', '$timeout', 'wimBoxService', 'config', 'bridgetAssetService', function ($scope, $interval, $timeout, wimBoxService, config, bridgetAssetService) {
	var vm = this;

	init();

	vm.addToWimBridget = addToWimBridge;
	vm.videoDelete = videoDelete;
	vm.videoPatch = videoPatch;

	function init() {
		videoList();
	}

	function videoList() {
		bridgetAssetService.videoList(videoListSuccess, videoListError);

		function videoListSuccess(response) {
			vm.videos = response.data;
			vm.videosLoaded = true;
		}
		function videoListError(response) {
			config.error('lang.wimbridget.video.LIST.ERROR');
		}
	}

	function videoDelete(video){
		bridgetAssetService.videoDelete(video.id, videoDeleteSuccess, videoDeleteError);

		// Callback success
		function videoDeleteSuccess(res){
			videoList();
			config.success('lang.wimbridget.video.DELETE.FINISH');
		}

		// Callback error
		function videoDeleteError(err){
			config.error('lang.wimbridget.video.DELETE.ERROR');
		}
	}

	function videoPatch(metadata_set){
		bridgetAssetService.videoPatch(metadata_set, videoPatchSuccess, videoPatchError);

		function videoPatchSuccess(res){
			videoList();
			config.success('lang.wimbridget.video.PATCH.FINISH');
		}
		function videoPatchError(err){
			config.error('lang.wimbridget.video.PATCH.ERROR');
		}
	}

	/**
	* Finds S3key of a box item and calls createVideo function
	* @function addToWimBridge
	* @param boxItem - WimTv box item
	*/
	function addToWimBridge(boxItem) {
		wimBoxService.getBoxItemInfo(boxItem.boxId, getBoxItemInfoSuccess, getBoxItemInfoError);

		// Callback success
		function getBoxItemInfoSuccess(res){
			var wimtvS3key = getS3key(res.data.file);
			boxItem['wimtvS3key'] = wimtvS3key;
			createVideo(boxItem);
		}

		// Callback error
		function getBoxItemInfoError(err){
			config.error('lang.wimbridget.video.CREATE.ERROR');
		}
	}

	/**
	* Returns S3key from a complete file name
	* @function getS3key
	* @param file
	*/
	function getS3key(file){
		var fileSections = file.split('/');
		return fileSections[2];
	}

	/**
	* Creates an asset video on Wimbridget Server
	* @function createVideo
	* @param boxItem - WimTv box item
	*/
	function createVideo(boxItem){
		bridgetAssetService.videoCreate(boxItem, videoCreateSuccess, videoCreateError);

		// Callback success
		function videoCreateSuccess(res){
			vm.videos.push(res.data);
			config.success('lang.wimbridget.video.CREATE.FINISH');
			handleNewVideo(true, res.data);
		}

		// Callback error
		function videoCreateError(err){
			config.error('lang.wimbridget.video.CREATE.ERROR');
		}
	}

	function handleNewVideo(activate, videoData) {
		if(activate) {
			if(!videoData.id && !videoData._id) return;
			videoData.id = videoData.id || videoData._id;
			bridgetAssetService.videoRead(videoData.id, success, failure);
			function success(response) {
				videoData = response.data;
				if(videoData.status === "FAILURE") {
					config.error('lang.wimbridget.video.CREATE.ERROR');
					return;
				}
				if (!videoData.thumb_url){
					$timeout(function () {
						handleNewVideo(true, videoData);
					}, 2500);
					return;
				}
				videoList();
			}
			function failure(response) {
				console.log("handleNewVideo failed", response);
			}
		}
	}

}]);
