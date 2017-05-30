angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimmarket', {
		url: "/wimMarket",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimmarket.html",
		controller: "wimmarketController",
		controllerAs: "vm"
	}).state('stateWimmarketZoom', {
		url: "/wimMarket/{marketplaceId}",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimmarket.html",
		controller: "wimmarketController",
		controllerAs: "vm"
	})
});



angular.module("wimtvApp").controller('wimmarketPreviewController', ['$uibModalInstance', 'object', 'marketPlaceService', function ($uibModalInstance, object, marketPlaceService) {
	var vm = this;
	vm.confirm = confirm;
	vm.close = close;

	init();

	function init() {
		vm.video = object;
		loadPreview(vm.video);
	}

	function loadPreview(video) {
		marketPlaceService.preview(video.marketplaceId, success, failure);
		function success(response) {
			console.log(response);
		}
		function failure(response) {
			console.log(response);
		}
	}

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

}]);

angular.module("wimtvApp").controller('wimmarketController', ['$stateParams','$uibModal', '$state', '$filter', 'config','marketPlaceService',
		function ($stateParams, $uibModal, $state, $filter, config,marketPlaceService) {
			var vm = this;
			vm.videos = [];
			vm.getVideos = getVideos;
			vm.payMarketItem = payMarketItem;
			vm.acquireMarketItem = acquireMarketItem;
			vm.showVideoPreview = showVideoPreview;


			vm.market = {};
			vm.marketPlacePublishPolicy = {};

			init();

			function init() {
				vm.market.enumLicenseType = config.marketPlaceLicenseType;
				vm.market.enumDurationUnit = config.marketPlaceDurationUnit;
				vm.market.enumCcType = config.ccTypes;
				vm.videosLoaded = false;
				vm.pageIndex = 1;
				getVideos();
				eventuallyCompleteMarketPayment();
			}

			/**
			 * Get video video on marketplace by the logged user.
			 */
			function getVideos() {
				var queryData = {
					pageSize: 8,
					pageIndex: vm.pageIndex - 1,
					queryString : vm.querySearchString
				};

				if (vm.marketPlacePublishPolicy.licenseType) {
					if (vm.marketPlacePublishPolicy.licenseType.length > 0) {
						queryData.conditions = {
							'licenseType' : vm.marketPlacePublishPolicy.licenseType}
					}
				}

				if ($stateParams.marketplaceId) {
					queryData.conditions = { 'marketplaceId' : $stateParams.marketplaceId };
					marketPlaceService.readVideoPublic($stateParams.marketplaceId,
						function(response) {
							$stateParams.marketplaceId = null;
							var items = [];
							items.push(response.data);
							vm.videosLoaded = true;
							vm.totalItems = 1;
							vm.videos = items;
						},function(failure) {
							// TODO: handle
						});
				} else {
					marketPlaceService.searchInPublic(queryData, success, failure);
				}
				function success(response) {
					vm.videosLoaded = true;
					vm.totalItems = response.data.totalCount;
					vm.videos = response.data.items;
				}

				function failure(response) {
					// TODO: handle
				}
			}

			function payMarketItem(marketItem) {
				marketPlaceService.pay(marketItem.marketplaceId, success, failure);

				function success(response) {
					window.location = response.data.url;
				}
				function failure(response) {
					switch (response.status) {
						case 400:
							config.warning("lang.wimmarket.WARNING", response);
							break;
						default:
							config.error("lang.wimmarket.WARNING", response);
							break;
					}
				}
			}

			function acquireMarketItem(marketItem) {
				marketPlaceService.acquire(marketItem.marketplaceId,success,failure);
				function success(response) {

					if (response.data.result == "PAYMENT_REQUIRED") {
						payMarketItem(marketItem);
					} else {
						config.success("lang.wimmarket.SUCCESS-ACQUIRED",response);
					}
				}
				function failure(response) {
					config.error("lang.wimmarket.WARNING",response);
				}
			}

			/**
			 * Check if exist a payment bundle process in progress and eventually activate the associated
			 * acquire packet.
			 */
			function eventuallyCompleteMarketPayment() {
				var data = config.getMarketPaymentData();
				if (data != null) {
					var trackingId = data.trackingId;
					var itemId = data.marketItemId;
					if (trackingId != null && itemId != null) {
						marketPlaceService.activateMarketItem(trackingId,itemId,success,failure);
						//bundleService.activateBundle(trackingId,bundleId,success,failure);
						function success(response) {
							config.info("lang.wimmarket.WARNING",response);
							config.resetMarketPaymentData();
						}
						function failure(response) {
							// TODO: handle
						}
					}
				}
			}

			function showVideoPreview(video) {
				$uibModal.open({
					templateUrl: 'previewModal.html',
					controller: 'wimmarketPreviewController',
					controllerAs: 'vm',
					resolve: {
						object: function () {
							return video;
						}
					}
				});
			}
		}]);
