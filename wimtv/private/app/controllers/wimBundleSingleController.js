angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimBundleSingle', {
		url: "/wimbundle/{id}",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimBundleSingle.html",
		controller: "wimBundleSingleController",
		controllerAs: "vm"
	})
});


angular.module("wimtvApp").controller('wimBundleSingleController', ['$stateParams', 'config', 'bundleService', 'wimBoxService', 'vodService',
	function ($stateParams, config, bundleService, wimBoxService, vodService) {
		var vm = this;

		vm.getWimboxVideos = getWimboxVideos;
		vm.getBundleVideos = getBundleVideos;
		vm.removeFromBundle = removeFromBundle;
		vm.addToBundle = addToBundle;
		vm.saveBundle = saveBundle;

		init();
		
		
		function init() {
			vm.showBundleForm = true;
			vm.bundleId = $stateParams.id;
			getBundle(vm.bundleId);
			getWimboxVideos();
			getBundleVideos();
		}

		/**
		 * Gets the bundle from a bundleId
		 * @param bundleId the ID
		 */
		function getBundle(bundleId) {
			bundleService.getPrivate(bundleId, success, failure);
			function success(response) {
				vm.bundle = response.data;
			}
			function failure(response) {
				config.error('lang.wimbundle.single.UNABLE-FETCHING-BUNDLE', response);
			}
		}


		/**
		 * Get user's uploaded video (wimbox)
		 */
		function getWimboxVideos() {
			var pagination = {
				pageIndex: vm.boxPageIndex - 1,
				pageSize: 6,
				queryString: "",
				excludeBundleId : vm.bundleId
			};
			wimBoxService.searchForContents(pagination,success,failure);
			function success(response) {
				vm.boxVideos = response.data.items;
				vm.boxTotalVideos = response.data.totalCount;
				vm.boxLoaded = true;
			}
			function failure(response) {
				config.error('lang.wimbundle.single.UNABLE-FETCHING-VIDEOS', response);
			}
		}

		/**
		 *	Updates WimBundle
		 */
		function saveBundle() {
			bundleService.update(vm.bundle, success, failure);
			function success(response) {
				config.success("lang.wimbundle.EDIT-BUNDLE.SUCCESS");
				vm.showBundleForm = true;
				init();
			}
			function failure(response) {
				config.error("lang.wimbundle.EDIT-BUNDLE.FAILURE", response);
			}
		}


		/**
		 * Check if the selected bundle contains the video
		 * @param video video to be checked
		 * @returns {boolean}
		 */
		function bundleContains(video) {
			for (var k in vm.bundleVideos) {
				if(vm.bundleVideos.hasOwnProperty(k) && vm.bundleVideos[k].contentId === video.contentId) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Add video to selected bundle
		 * @param video video to add to selected bundle
		 */
		function addToBundle(video) {
			if(bundleContains(video)) {
				config.error("lang.wimbundle.single.ADD.ALREADY-EXIST");
			}
			var data = {
				bundleId: vm.bundleId,
				licenseType: "CONTENT_BUNDLE"
			};
			wimBoxService.publishVideo(video.boxId, data, success, failure);
			function success(response) {
				config.success("lang.wimbundle.single.ADD.SUCCESS");
				getWimboxVideos();
				getBundleVideos();
			}
			function failure(response) {
				config.error("lang.wimbundle.single.ADD.ERROR", response);
			}
		}

		/**
		 * Remove a video from the selected bundle
		 * @param video
		 */
		function removeFromBundle(video) {
			vodService.deleteVideo(video.vodId, success, failure);
			function success(response) {
				config.success("lang.wimbundle.single.REMOVE.SUCCESS");
				vm.getWimboxVideos();
				vm.getBundleVideos();
			}
			function failure(response) {
				config.error("lang.wimbundle.single.REMOVE.ERROR", response);
			}
		}

		/**
		 * Get contents of a bundle
		 * @param bundleId bundle id
		 */
		function getBundleVideos() {
			var filters = {
				conditions: {
					bundleId: vm.bundleId,
					licenseType: "CONTENT_BUNDLE"
				},
				pageIndex: vm.zoomedBundlePageIndex - 1,
				pageSize: 6
			};

			vodService.getVideos(filters, success, failure);
			function success(response) {
				vm.bundleVideos = response.data.items;
				vm.bundleVideosTotal = response.data.totalCount;
			}

			function failure(response) {
				config.error("lang.wimbundle.single.UNABLE-FETCHING-VIDEOS", response);
			}
		}
	}]);
