angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimBundle', {
		url: "/wimbundle",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimBundle.html",
		controller: "wimBundleController",
		controllerAs: "vm"
	})
});

angular.module("wimtvApp").controller('wimBundleModalController', ['$scope', '$uibModalInstance', 'object', 'vodService', 'commonService', function($scope, $uibModalInstance, object, vodService, commonService) {
    var vm = this;
    vm.confirm = confirm;
    vm.close = close;
    vm.deleteVideo = deleteVideo;
	// vm.updateVideo = updateVideo;
    vm.thumbnailId = null;
    vm.selectedBundle = object;



    //playVodItem(vm.vod);

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

angular.module("wimtvApp").controller('wimBundleController', ['$scope', '$timeout', '$uibModal', 'bundleService', 'config', 'userService', 'vodService', 'commonService',
	function ($scope, $timeout, $uibModal, bundleService, config, userService, vodService, commonService) {
		var vm = this;
		vm.openDeleteModal = openDeleteModal;
		vm.getBundles = getBundles;
		vm.saveBundle = saveBundle;
		vm.toggleShowBundleForm = toggleShowBundleForm;
		vm.changeBundleThumbnail = changeBundleThumbnail;
		vm.isNewBundleDisabled = isNewBundleDisabled;

		init();

		function init() {
			vm.bundlesLoaded = false;
			vm.querySearchString  = "";
			vm.pageIndex = 1;
			vm.showBundleForm = false;
			getBundles();
			checkPaypalAndFinance();
		}

		/**
		 * Listener on event "paypalUpdated" triggered when the user updates the paypal email address located in the top of the privatePage.html ('monetisation')
		 */
		$scope.$on('paypalUpdated',function (event, data) {
			checkPaypalAndFinance();
		});


		function checkPaypalAndFinance() {
			config.checkPaypalAndFinance(userService, callback);
			function callback(response) {
				vm.missingPaypal = response.paypal;
				vm.missingBillingInfo = response.billing;
			}
		}

		/**
		 * Toggles the form for creating a new wimbundle
		 */
		function toggleShowBundleForm() {
			vm.showBundleForm = !vm.showBundleForm;
			vm.newBundle = {}
		}

		/**
		 * disable the newForm Add button
		 */
		function isNewBundleDisabled() {
			return vm.missingPaypal || vm.missingBillingInfo;
		}

		/**
		 *	Create new WimBundle
		 */
		function saveBundle() {
			bundleService.create(vm.newBundle, success, failure);
			function success(response) {
				config.success("lang.wimbundle.NEW-BUNDLE.SUCCESS");
				toggleShowBundleForm();
				getBundles();
			}
			function failure(response) {
				config.error("lang.wimbundle.NEW-BUNDLE.ERROR", response);
			}
		}

		/**
		 * Changes the bundle thumbnail and immediately saves the updated bundle.
		 * 100% handling of the upload
		 * @param file
		 * @param errFiles
		 * @param bundle
		 */
		function changeBundleThumbnail(file, errFiles, bundle) {
			bundle.updatingThumbnail= true;
			commonService.uploadImage(file, errFiles, bundle, success, error, uploading);
			function success(response) {
				bundleService.update(bundle, function (response) {
					config.success('lang.wimbundle.update.SUCCESS');
				}, error);
				$timeout(hideUpdating, 1500);
			}
			function uploading(response) {
				bundle.updatingThumbnail = true;
				$timeout(hideUpdating, 2500);
			}
			function error(response) {
				bundle.updatingThumbnailError = true;
			}
			function hideUpdating() {
				bundle.updatingThumbnail = false;
			}
		}

		/**
		 * Bundle List
		 */
		function getBundles() {
			var queryData = {
				pageIndex: vm.pageIndex - 1,
				pageSize: 5,
				queryString : ""
			};
			bundleService.searchPrivate(queryData, success, failure);
			function success(response) {
				vm.bundles = response.data.items;
				vm.totalItems = response.data.totalCount;
				vm.bundlesLoaded = true;
			}
			function failure(response) {
				config.error("lang.wimbundle.UNABLE-FETCHING-BUNDLES", response);
			}
		}

		/**
		 * Delete Modal handling
		 * @param object
		 */
		function openDeleteModal(object) {
			var modal = $uibModal.open({
				templateUrl: 'removeModal.html',
				controller: 'wimBundleModalController',
				controllerAs: 'vm',
				resolve: {
					object: function () {
						return object;
					}
				}
			});

			modal.result.then(function (bundle) {
				bundleService.remove(bundle,success, failure);
				//vodService.deleteVideo(data.vodId, success, failure);
				function success(response) {
					if(response.status >= 200 && response.status < 300) {
						$timeout(getBundles, 250);
						config.success("lang.wimbundle.DELETE.FINISH");
					} else {
						config.error("lang.wimbundle.DELETE.ERROR", response);
					}
					getBundles();
				}
				function failure(response) {
					config.error("lang.wimbundle.DELETE.ERROR", response);
				}
			});
		}
}]);
