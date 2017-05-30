angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimvod', {
		url: "/wimvod",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimvod.html",
		controller: "wimvodController",
		controllerAs: "vm"
	})
});


angular.module("wimtvApp").controller('wimvodModalController', ['$scope', '$uibModalInstance', 'object', 'vodService', 'commonService', function($scope, $uibModalInstance, object, vodService, commonService) {
	var vm = this;
	vm.confirm = confirm;
	vm.close = close;
	vm.deleteVideo = deleteVideo;
	// vm.updateVideo = updateVideo;
	vm.thumbnailId = null;
	vm.vod = object;

	playVodItem(vm.vod);

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

	function deleteVideo(box) {
		confirm(box);
	}

	function playVodItem(vod) {
		vodService.play(vod.vodId, success, failure);

		function success(response) {
			// playing is delegated to resource Service
		}
		function failure(response) {
			config.error("lang.wimvod.PREVIEW-ERROR", response);
		}

	}
}]);


angular.module("wimtvApp").controller('wimvodController', ['$scope', '$rootScope', '$state', '$filter', '$timeout', '$uibModal', 'restService', 'vodService', 'config', function ($scope, $rootScope, $state, $filter, $timeout, $uibModal, restService, vodService, config) {
	var vm = this;
	vm.videos = [];
	vm.getVideos = getVideos;
	vm.openDeleteModal = openDeleteModal;
	vm.openPreview = openPreview;
	vm.togglePublic = togglePublic;

	init();

	function init() {
		vm.videosLoaded = false;
		vm.querySearchString  = "";
		vm.pageIndex = 1;
		getVideos();
	}

	/**
	 * Switch a video from public to private.
	 * @param video
	 */
	function togglePublic(video) {
		video.public = !video.public;
		vodService.updateVideo(video, success, failure);
		function success(response) {
			$timeout(getVideos, 150);
			config.success("lang.wimvod.EDIT.FINISH");
		}
		function failure(response) {
			config.error("lang.wimvod.EDIT.ERROR", response);
		}
	}

	/**
	 * Get video published on wimvod
	 */
	function getVideos() {
		vm.refreshing = true;
		var pagination = {
			pageIndex: vm.pageIndex-1,
			pageSize: 10,
			queryString : vm.querySearchString
		};

		vodService.getVideos(pagination, success, failure);

		function success(response) {
			vm.videosLoaded = true;
			vm.totalItems = response.data.totalCount;
			vm.videos = response.data.items;
		}

		function failure(response) {
			config.error('lang.wimvod.GETVIDEOS.ERROR', response);
			vm.videosError = true;
			vm.videosLoaded = true;
		}
	}


	/**
	 * Delete Modal handling
	 * @param object
	 */
	function openDeleteModal(object) {
		var modal = $uibModal.open({
			templateUrl: 'removeModal.html',
			controller: 'wimvodModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return object;
				}
			}
		});

		modal.result.then(function (data) {
			vodService.deleteVideo(data.vodId, success, failure);
			function success(response) {
				if(response.status >= 200 && response.status < 300) {
					$timeout(getVideos, 250);
					config.success("lang.wimvod.DELETE.FINISH");
				} else {
					config.error("lang.wimvod.DELETE.ERROR", response);
				}
				getVideos();
			}
			function failure(response) {
				config.error("lang.wimvod.DELETE.ERROR", response);
			}
		}).catch(function (data) {
			console.log(data);
		});
	}
	/**
	 * Preview Modal handling
	 * @param video
	 */
	function openPreview(video) {
		var modal = $uibModal.open({
			templateUrl: 'previewModal.html',
			controller: 'wimvodModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return video;
				}
			}
		});
		modal.result.then(function (data) {
			console.log(data);
		}).catch(function (data) {
			console.log(data);
		});
	}


}]);
