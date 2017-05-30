angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateLogin', {
		url: "/login",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateLogin.html",
		controller: "loginController as vm"
	});
});

angular.module("wimtvApp").controller('loginController', ['$scope', '$rootScope', 'authService', 'config', function($scope, $rootScope, authService, config) {

	var vm = this;

	vm.login = login;

	function login() {
		authService.login(vm.auth, loginSuccess, loginFailure);
	}

	function loginSuccess(response) {
		window.location.href = "#/dashboard";
		config.saveLoginData(vm.auth);
	}

	function loginFailure(response) {
		// $scope.error = new Error("Login failed");
		config.globalLoading(false);
		$rootScope.loggedUser = null;
		localStorage.removeItem('loggedUser');

		if (response.status == 401 || response.data.error === "invalid_grant" ) {
			config.error("lang.login.MESSAGE-WRONG-DATA");
		} else {
			config.error("lang.login.UNABLE-TO-COMPLETE");
		}
	}
}]);
