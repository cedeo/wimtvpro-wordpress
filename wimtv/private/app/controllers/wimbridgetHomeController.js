angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimbridget', {
		url: "/wimbridge",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbridgetHome.html",
		controller: "wimbridgetHomeController as vm"
	})
});

angular.module("wimtvApp").controller('wimbridgetModalController', [ 'bridgetAssetService', '$uibModalInstance', 'object', 'config',
function(bridgetAssetService, $uibModalInstance, object, config){
	var vm = this;
	vm.videoPatch = videoPatch;
	vm.confirm = confirm;
	vm.close = close;
	vm.video = object;
	vm.checkAndUpdateTags = checkAndUpdateTags;
	vm.removeTag = removeTag;

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}

	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

	function videoPatch(metadata_set){
		bridgetAssetService.videoPatch(metadata_set, videoPatchSuccess, videoPatchError);

		function videoPatchSuccess(res){
			config.success('lang.wimbridget.video.PATCH.FINISH');
		}
		function videoPatchError(err){
			config.error('lang.wimbridget.video.PATCH.ERROR');
		}
	}

	function removeTag(video, tag) {
		var index = video.tags.indexOf(tag);
		video.tags.splice(index, 1);
	}

	function checkAndUpdateTags($event, video) {
		if($event.keyCode === 9 || $event.keyCode === 13) {			// tab or enter pressed
			if (!video.tags) {
				video.tags = [];
			}
			if (video.tagText.length > 0 ) {
				var tag = video.tagText.substr(0, video.tagText.length);
				if(video.tags.indexOf(tag) === -1) {
					video.tags.push(tag);
				}
				video.tagText = "";
			} else {
				console.log("nodata to push");
			}
			$event.preventDefault();
			$event.stopPropagation();
		}
	}
}]);

angular.module("wimtvApp").controller('wimbridgetHomeController', ['bridgetAssetService', 'bridgetEditorService', 'config', '$timeout', '$uibModal',
function (bridgetAssetService, bridgetEditorService, config, $timeout, $uibModal) {
	var vm = this;
	vm.videosBridget = [];
	init();
	vm.getVideosBridget = getVideosBridget;
	vm.bridgetDelete = bridgetDelete;
	vm.bridgetsClear = bridgetsClear;
	vm.openModal = openModal;

	function init() {
		getVideosBridget();
	}

	function getVideosBridget() {
		bridgetAssetService.videoList(videoBridgetSuccess, videoBridgetFailure);

		function videoBridgetSuccess(response) {
			vm.videosBridget = [];
			var videos = response.data;
			videos.forEach(function(video){
				if (video.sourceof.length > 0){
					vm.videosBridget.push(video);
				}
			})
			vm.videosBridgetLoaded = true;
		}
		function videoBridgetFailure(response) {
			console.log('videoBridgetFailure', response);
		}
	}

	function bridgetDelete(bridget){
		bridgetEditorService.bridgetDelete(bridget._id, bridgetDeleteSuccess, bridgetDeleteError);

		// Callback success
		function bridgetDeleteSuccess(res){
			config.success('lang.wimbridget.bridget.DELETE.FINISH');
		}

		// Callback error
		function bridgetDeleteError(err){
			config.error('lang.wimbridget.bridget.DELETE.ERROR');
		}
	}

	function bridgetsDeleteAll(video){
		if (video) vm.bridgets = video.sourceof;
		if (vm.bridgets && vm.bridgets.length == 0) {
			getVideosBridget();
			return;
		}
		var bridget = vm.bridgets[vm.bridgets.length-1];
		bridgetEditorService.bridgetDelete(bridget._id, bridgetDeleteSuccess, bridgetDeleteError);

		// Callback success
		function bridgetDeleteSuccess(res){
			vm.bridgets.pop();
			config.success('lang.wimbridget.bridget.DELETE.FINISH');
			bridgetsDeleteAll();
		}

		// Callback error
		function bridgetDeleteError(err){
			config.error('lang.wimbridget.bridget.DELETE.ERROR');
			bridgetList(vm.sourceContent);
		}
	}

	function bridgetsClear(video){
		var confirm = window.confirm("are you sure?");
		if (confirm == true) bridgetsDeleteAll(video);
	}

	/**
	 * Edit Modal handling
	 * @param video
	 */
	function openModal (video) {
		var modal = $uibModal.open({
			templateUrl: 'editModal.html',
			controller: 'wimbridgetModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return video;
				}
			}
		});
		modal.result.then(function (data) {
			config.success('lang.wimbox.EDIT.FINISH');
			getVideosBridget();
		}).catch(function (data) {
			getVideosBridget();
		});
	}

}]);
