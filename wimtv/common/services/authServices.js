angular.module("wimtvApp").factory('authService', ['$rootScope', '$http', 'config', 'restService', function ($rootScope, $http, config, restService) {

	return {
		register: register,
		login: login
	};


	function register(registrationData, successCallback, failureCallback) {
		restService.wimTvRestPOST("/api/public/user/register", registrationData, success, failureCallback);

		function success(response) {
			console.log("Register response:" + response);
			if (response.status === 201) {      // user created
				var auth = {
					username: registrationData.userCode,
					password: registrationData.password
				};
				successCallback(auth);
			} else {
				if (failureCallback) {
					failureCallback(response)
				}
			}
		}
	}

	function login(auth, successCallback, failureCallback) {
		var grant = auth;
		if (!grant) {
			grant = {};
		}
		grant.grant_type = "password";

		$http({
			method: 'POST',
			url: $rootScope.wimtvUrl + config.tokenUrl,
			data: grant,
			// transformRequest: function (obj) {
			// 	var str = [];
			// 	for (var p in obj)
			// 		str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			// 	return str.join("&");
			// },
			headers: config.getHeaders()
		}).then(success).catch(failure);

		function success(response) {
			if (response && response.status >= 200 && response.status < 300) {
				config.setTokens(null, response.data.access_token, response.data.refresh_token, response.data.expires_in);
				config.saveLoginData(auth);
				$rootScope.loggedUser = config.getLoginData();
				if(successCallback) {
					successCallback(response);
				}
			} else {
				failureCallback(response);
			}
		}

		function failure(response) {
			if (failureCallback) {
				failureCallback(response);
			}
		}

	}
}]);