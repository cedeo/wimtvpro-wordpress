angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateUserprofile', {
		url: "/userprofile",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateUserprofile.html",
		controller: "userprofileController",
		controllerAs: "vm"
	})
});

angular.module("wimtvApp").controller('changePasswordController', ['$scope', '$uibModalInstance', 'user', function ($scope, $uibModalInstance, user) {
	var vm = this;
	vm.user = user;
	vm.close = close;
	vm.save = save;
	
	function save() {
		$uibModalInstance.close(vm.user);
	}
	
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}
}]);

angular.module("wimtvApp").controller('userprofileController', ['$log',"$scope", "$rootScope", "$filter", "$timeout", "$location", "$uibModal", "Upload", "userService", "commonService", "config",function ($log,$scope, $rootScope, $filter, $timeout, $location, $uibModal, Upload, userService, commonService, config) {
	var vm = this;
	vm.activePanel = $location.search().activePanel || 'home';
	vm.getProfile = getProfile;
	vm.changeImage = changeImage;
	vm.changeImmediatelyImage = changeImmediatelyImage;
	vm.profileUpdate = profileUpdate;
	vm.requestPacketPay = requestPacketPay;
	vm.showChangePassword = showChangePassword;
	vm.downgrade = downgrade;
	vm.isDisabledPacket = isDisabledPacket;
	vm.profile = null;
	vm.features = null;
	vm.finance = null;
	vm.errors = {request: null, profileUpdate: {}};
	vm.newUserImage = {};
	vm.msgs = {profileUpdate: null};
	vm.licenseName = "";

	function getProfileOverview() {
		userService.getMeOverview().then(success).catch(failure);
		function success(response) {
			vm.licenseName = response.data.licenseName;

		}

		function failure(response) {
			config.error('lang.userprofile.GET.ERROR', response);
		}
	}

	function getProfile() {
		userService.getProfile().then(success).catch(failure);

		function success (response) {
          	vm.profile = response.data.profile;
			vm.features = response.data.features;
			vm.finance = response.data.finance;
			vm.userCustomization = response.data.userCustomization;
            config.setThumbnaildId(vm.profile.thumbnailId);
            config.setPageTitle(vm.profile.pageTitle);

			if(vm.profile.birthDate) {
				if(typeof vm.profile.birthDate) { // date is a string, trying to convert
					vm.profile.birthDate = moment(vm.profile.birthDate, 'DD/MM/YYYY').toDate();
				}
				vm.profile.birthDate = moment(vm.profile.birthDate, 'DD/MM/YYYY').toDate();
			}
			if(vm.finance && vm.finance.companyName) {
				vm.finance.companyConfirm = true;
				vm.oldCompanyName = vm.finance.companyName;
			}
			// if(!vm.features.livePassword) {
			// 	config.toast("warning", "lang.userprofile.MESSAGES.MISSING-LIVEPASSWORD");
			// }
		}
		function failure(response) {
			config.error('lang.userprofile.GET.ERROR', response);
		}
	}

	function changeImage (file, errFiles) {
		commonService.uploadImage(file, errFiles, vm.profile);
	}
	
	function changeImmediatelyImage(file, errFiles) {
		vm.updatingProfileImage = true;
		commonService.uploadImage(file, errFiles, vm.profile, success, error, uploading);
        
        function success(response) {
			profileUpdate();
			$timeout(hideUpdating, 1500);
		}
		function uploading(response) {
			vm.updatingProfileImage = true;
			$timeout(hideUpdating, 2500);
		}
		function error(response) {
			vm.updatingProfileImageError = true;
		}
		function hideUpdating() {
			vm.updatingProfileImage = false;
		}
	}

	function profileUpdate() {
		var requestData = {
			profile: angular.copy(vm.profile)	,
			features: vm.features,
			finance: {}
		};

		for (var k in vm.finance) {
			if(vm.finance.hasOwnProperty(k) && vm.finance[k]) {
				requestData.finance[k] = vm.finance[k];
			}
		}

		if(vm.profile && vm.profile.birthDate) {
			requestData.profile.birthDate = moment(vm.profile.birthDate).format('DD/MM/YYYY');
		}

		userService.updateProfile(requestData).then(success).catch(failure);

		function success(response) {
			config.success('lang.userprofile.UPDATE.FINISH');
		}
		function failure(response) {
			config.error('lang.userprofile.UPDATE.ERROR', response);
		}
	}


	function showChangePassword() {
		var modal = $uibModal.open({
			templateUrl: 'changePassword.html',
			controller: 'changePasswordController',
			controllerAs: 'vm',
			resolve: {
				user: function () {
					return vm.user;
				}
			}
		});
		modal.result.then(function (data) {
			userService.changePassword(data).then(success).catch(failure);
			function success(response) {
				if(response.status >= 200 && response.status < 300) {
					config.success("lang.userprofile.MESSAGES.SUCCESSFUL-CHANGED-PASSWORD");
				} else {
					config.error("lang.userprofile.MESSAGES.FAILURE-CHANGE-PASSWORD", response);
				}
			}
			function failure(response) {
				config.error("lang.userprofile.MESSAGES.FAILURE-CHANGE-PASSWORD", response);
			}
		}).catch(function (data) {
			config.toast("info", data);
		});
	}

	/**
	 * Request package payment
	 * @param packetName
     */
	function requestPacketPay(packetName) {
		config.globalLoading(true);
		var options = {
			embedded : false,
			returnUrl : "http://" + location.host + "/privatePage.html#/userprofile?activePanel=package",
			cancelUrl : "http://" + location.host + "/privatePage.html#/userprofile?activePanel=package",
			mobile : false
		};

		userService.requestPacketPay(packetName,options,resolve, reject);

		function resolve(response) {
			window.location = response.data.url;
		}
		function reject(response) {
			config.globalLoading(false);
			config.error('lang.userprofile.MESSAGES.PAYMENT-FAILED', response);
		}
	}


	/**
	 * Downgrade to free
	 */
	function downgrade() {
		config.globalLoading(true);
		userService.downgrade(success, failure);
		function success(response) {
			config.globalLoading(false);
			config.success('lang.userprofile.MESSAGES.DOWNGRADE-COMLPETED');
			vm.licenseName = response.data.licenseName;
		}
		function failure(response) {
			config.globalLoading(false);
			config.error('lang.userprofile.MESSAGES.DOWNGRADE-FAILED', response);
		}

	}
	/**
	 * Check if exist a payment packet process in progress and eventually activate the associated
	 * acquire packet.
	 */
	function eventuallyCompletePacketPayment() {
		var data = config.getPacketPaymentData();
		if (data != null) {
			var trackingId = data.trackingId;
			var packageName = data.packageName;
			if (trackingId != null && packageName != null) {
				userService.activateLicense(trackingId,packageName,success,failure);
				function success(response) {
					config.resetPacketPaymentData();
					getProfileOverview();
				}
				function failure(response) {
					config.globalLoading(false);
					config.error('lang.userprofile.MESSAGES.PAYMENT-FAILED', response);
				}

			}
		}
	}

	function isDisabledPacket() {
		return vm.missingBillingInfo;
	}

	function checkPaypalAndFinance() {
		config.checkPaypalAndFinance(userService, callback);
		function callback(response) {
			vm.missingPaypal = response.paypal;
			vm.missingBillingInfo = response.billing;
		}
	}


	getProfile();
	checkPaypalAndFinance();
	getProfileOverview();
	eventuallyCompletePacketPayment();

}]);

angular.module("wimtvApp").controller('userOverviewController', ["$scope", "$rootScope", "$timeout", "userService", "config", function ($scope, $rootScope, $timeout, userService, config) {
	var vm = this;
	vm.getProvileOverview = getProfileOverview;
	vm.savePaypal = savePaypal;
	init();

	function init() {
		getProfileOverview();
	}

	function getProfileOverview() {
		// service call
		userService.getMeOverview().then(success).catch(failure);

		function success (response) {
			vm.profileOverview = response.data;
			if(vm.profileOverview && parseInt(vm.profileOverview.storagePercent) >= 100) {
				config.warning("lang.wimbox.UPLOAD-LIMIT-REACHED");
			}
			addReadableData();
		}

		function failure(response) {
			config.error("lang.userprofile.GET.ERROR", response);
		}
	}

	/**
	 * Support function for converting datas in a more readable way
	 */
	function addReadableData() {
		if(vm.profileOverview.storagePercent) {
			vm.profileOverview.storagePercentRound = Math.round(parseFloat(vm.profileOverview.storagePercent.replace(',','.')));
			vm.profileOverview.storagePercentClass = "p" + vm.profileOverview.storagePercentRound;
		}
		if(vm.profileOverview.bandPercent) {
			vm.profileOverview.bandPercentRound = Math.round(parseFloat(vm.profileOverview.bandPercent.replace(',','.')));
			vm.profileOverview.bandPercentClass = "p" + vm.profileOverview.bandPercentRound;
		}
	}

	function savePaypal() {
		userService.getProfile().then(success).catch(failure);

		function success(response) {
			var user = response.data;
			user.finance.paypalEmail = vm.profileOverview.paypalEmail;
			userService.updateProfile(user).then(function () {
				config.success("lang.privatepage.PAYPAL-EMAIL.SUCCESS-UPDATE");
				$rootScope.$broadcast("paypalUpdated", vm.profileOverview.paypalEmail);
			}).catch(failure);
		}

		function failure(response) {
			config.error("lang.privatepage.PAYPAL-EMAIL.FAILURE-UPDATE", response);
		}
	}
}]);