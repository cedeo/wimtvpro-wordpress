angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimbridgetImages', {
		url: "/wimbridge/images",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbridgetImages.html",
		controller: "wimbridgetImagesController as vm"
	})
});

angular.module("wimtvApp").controller('wimbridgetModalImageController', [ 'bridgetAssetService', '$uibModalInstance', 'object', 'config',
function(bridgetAssetService, $uibModalInstance, object, config){
	var vm = this;
	vm.imagePatch = imagePatch;
	vm.confirm = confirm;
	vm.close = close;
	vm.image = object;
	vm.imageDelete = imageDelete;

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}

	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

	function imagePatch(metadata_set){
		bridgetAssetService.imagePatch(metadata_set, imagePatchSuccess, imagePatchError);

		function imagePatchSuccess(res){
			config.success('lang.wimbridget.image.PATCH.FINISH');
			vm.close();
		}
		function imagePatchError(err){
			config.error('lang.wimbridget.image.PATCH.ERROR');
		}
	}

	function imageDelete(image){
		bridgetAssetService.imageDelete(image.id, imageDeleteSuccess, imageDeleteError);

		// Callback success
		function imageDeleteSuccess(res){
			config.success('lang.wimbridget.image.DELETE.FINISH');
			vm.close();
		}

		// Callback error
		function imageDeleteError(err){
			config.error('lang.wimbridget.image.DELETE.ERROR');
		}
	}
}]);

angular.module("wimtvApp").controller('wimbridgetImagesController', ['$timeout', 'bridgetAssetService', 'config', '$uibModal', function ($timeout, bridgetAssetService, config, $uibModal) {
	var vm = this;

	vm.image = {};
	vm.images = [];
	vm.imageCreate = imageCreate;
	vm.imageDelete = imageDelete;
	vm.imageList = imageList;
	vm.imagePatch = imagePatch;
	vm.toggleAdd = toggleAdd;
	vm.addingWaiting = false;
	vm.openModal = openModal;

	init();

	function init() {
		vm.imagesLoaded = false;
		vm.addText = true;
		imageList();
	}

	function imageCreate(image){
		vm.addingWaiting = true;
		bridgetAssetService.imageCreate(getFormData(image), imageCreateSuccess, imageCreateError);

		// Callback success
		function imageCreateSuccess(res){
			imageList();
			config.success('lang.wimbridget.image.CREATE.FINISH');
			vm.toggleAdd(vm.adding);
			vm.image = {};
			vm.addingWaiting = false;
		}

		// Callback error
		function imageCreateError(err){
			config.error('lang.wimbridget.image.CREATE.ERROR');
			vm.addingWaiting = false;
		}
	}

	function imageDelete(image){
		bridgetAssetService.imageDelete(image.id, imageDeleteSuccess, imageDeleteError);

		// Callback success
		function imageDeleteSuccess(res){
			imageList();
			config.success('lang.wimbridget.image.DELETE.FINISH');
		}

		// Callback error
		function imageDeleteError(err){
			config.error('lang.wimbridget.image.DELETE.ERROR');
		}
	}

	function imageList(){
		bridgetAssetService.imageList(imageListSuccess, imageListError);

		// Callback success
		function imageListSuccess(res){
			if (!angular.equals(res.data, vm.images)){
				vm.images = res.data;
			}
			vm.imagesLoaded = true;
		}

		// Callback error
		function imageListError(err){
			config.error('lang.wimbridget.image.LIST.ERROR');
		}
	}

	function imagePatch(metadata_set){
		bridgetAssetService.imagePatch(metadata_set, imagePatchSuccess, imagePatchError);

		function imagePatchSuccess(res){
			$timeout(imageList, 500);
			config.success('lang.wimbridget.image.PATCH.FINISH');
		}
		function imagePatchError(err){
			config.error('lang.wimbridget.image.PATCH.ERROR');
		}
	}

	function getFormData(obj){
		var formData = new FormData();
		// clono l'oggetto da manipolare
		var data = angular.extend({}, obj);
		for (var key in data) {
			formData.append(key, data[key]);
		}
		return formData;
	}

	function toggleAdd() {
		vm.addText = vm.adding;
		vm.adding = !vm.adding;
	}

	/**
	 * Edit Modal handling
	 * @param video
	 */
	function openModal (image, templateUrl) {
		var modal = $uibModal.open({
			templateUrl: templateUrl,
			controller: 'wimbridgetModalImageController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return image;
				}
			}
		});
		modal.result.then(function (data) {
			config.success('lang.wimbox.EDIT.FINISH');
			imageList();
		}).catch(function (data) {
			imageList();
		});
	}
}]);
