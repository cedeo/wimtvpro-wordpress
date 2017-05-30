angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimtrade', {
		url: "/wimTrade",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimtrade.html",
		controller: "wimtradeController",
		controllerAs: "vm"
	})
});

angular.module("wimtvApp").controller('wimtradeModalController', ['$uibModalInstance','object','wimBoxService','marketPlaceService','config', function($uibModalInstance, object, wimBoxService, marketPlaceService,config) {
	var vm = this;
	vm.confirm = confirm;
	vm.close = close;

	// Market place data
	vm.publishOnMarketPlace = publishOnMarketPlace;
	initMarketPlace();


	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

	/**
	 * Market Place Init
	 */
	function initMarketPlace() {
		vm.video = object;
		vm.market = {};
		vm.marketPlacePublishPolicy = {};

		vm.market.enumLicenseType = config.marketPlaceLicenseType;
		vm.market.enumDurationUnit = config.marketPlaceDurationUnit;
		vm.market.enumCcType = config.ccTypes;
		vm.market.publish = {};
	}

	/**
	 * Public a video in market place.
	 */
	function publishOnMarketPlace() {
		vm.marketPlacePublishPolicy.boxId = vm.video.boxId;
		marketPlaceService.publish(vm.marketPlacePublishPolicy,
			success,
			failure);
		function success(response) {
			//TODO: HANDLE
		}
		function failure(response) {
			var messageError = JSON.stringify(response);
			// TODO: HANDLE
			config.error();
			config.toast("error", messageError);
		}
	}

}]);


angular.module("wimtvApp").controller('wimtradeController', ['$uibModal', '$filter','config', 'wimBoxService','marketPlaceService', function ($uibModal, $filter, config, wimBoxService, marketPlaceService) {
	var vm = this;

	vm.videos = [];

	vm.pageIndex = 1;

	vm.marketVideos = [];
	vm.marketPageIndex = 1;
	vm.marketVideosLoaded = true;
	vm.marketTotalItems	= 0;

	vm.selectedVideoToSell = null;

	vm.market = {};
	vm.marketPlacePublishPolicy = {};

	vm.getVideos = getVideos;
	vm.getMarketVideos = getMarketVideos;

	vm.removeFromMarket = removeFromMarket;
	vm.showSellForm = showSellForm;
	vm.closeSellForm = closeSellForm;
	vm.publishOnMarketPlace = publishOnMarketPlace;

    vm.playBoxItem = playBoxItem;
    vm.preview = preview;

	init();

	function init() {
		getVideos();
		getMarketVideos();
		initMarket();
		vm.pageSize = 6;
	}

	function initMarket() {
		vm.market.enumLicenseType = config.marketPlaceLicenseType;
		vm.market.enumDurationUnit = config.marketPlaceDurationUnit;
		vm.market.enumCcType = config.ccTypes;
		vm.market.enumDurationUnit.DAYS = $filter('translate')('lang.wimtrade.DAYS');
		vm.market.enumDurationUnit.MONTHS = $filter('translate')('lang.wimtrade.MONTHS');
		vm.market.enumDurationUnit.YEARS = $filter('translate')('lang.wimtrade.YEARS');

	}


    function playBoxItem(boxId) {
        wimBoxService.playBoxItem(boxId, success, failure);
        function success(response) {
            // playing is delegated to resource Service: nothing to do
        }
        function failure(response) {
            config.error("lang.wimvod.PREVIEW-ERROR", response);
        }
    }

    function preview(video) {
        var modal = $uibModal.open({
            templateUrl: 'videoPreview.html',
            controller: 'wimboxModalController',
            controllerAs: 'vm',
            resolve: {
                object: function () {
                    return video;
                }
            }
        });
        modal.result.then(function (data) {
            // nothing to do
        }).catch(function (data) {
            // todo: handle errors;
            // nothing to do here too?
        });
    }

	/**
	 * Get Wimbox Videos
 	 */
	function getVideos() {
		vm.refreshing = true;
		var pagination = {
			pageIndex: vm.pageIndex-1,
			pageSize: vm.pageSize,
			queryString : vm.querySearchString,
			source: "UPLOAD"
		};

		wimBoxService.searchForContents(pagination, success, failure);

		function success(response) {
			vm.videosLoaded = true;
			vm.totalItems = response.data.totalCount;
			vm.videos = response.data.items;
		}

		function failure(response) {
			config.error('lang.wimtrade.GETVIDEOS.ERROR', response);
			vm.videosError = true;
			vm.videosLoaded = true;
		}
	}

	function publishOnMarketPlace() {
		vm.marketPlacePublishPolicy.boxId = vm.selectedVideoToSell.boxId;
		marketPlaceService.publish(vm.marketPlacePublishPolicy,
			success,
			failure);
		function success(response) {
			closeSellForm();
			getMarketVideos();
			getVideos();

		}
		function failure(response) {
			vm.marketPlacePublishPolicy = {};
			config.error("lang.wimtrade.MESSAGES.FAILURE-SELL-TO-MARKET", response);
		}
	}

	function showSellForm(video) {
		vm.selectedVideoToSell = video;
	}

	function closeSellForm() {
		vm.selectedVideoToSell = null;
		vm.marketPlacePublishPolicy = {};

	}
	/**
	 * Get video pubished by the logged user.
 	 */
	function getMarketVideos() {
		var queryData = {
			pageSize: 8,
			pageIndex: vm.marketPageIndex - 1
		};
		marketPlaceService.searchInPrivate(queryData,success,failure);
		function success(response) {
			vm.marketVideosLoaded = true;
			vm.marketTotalItems = response.data.totalCount;
			vm.marketVideos = response.data.items;
		}
		function failure(response) {
			// TODO: HANDLE
		}
	}

	function removeFromMarket(video) {
		var marketplaceId = video.marketplaceId
		marketPlaceService.deleteVideo(marketplaceId,success,failure);

		function success(response) {
			// TODO: HANDLE
			getMarketVideos();
			getVideos;
		}

		function failure(response) {
			// TODO: HANDLE
		}
	}

}]);
