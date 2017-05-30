angular.module("wimtvApp").factory('bridgetRestService', ['$rootScope', '$http','$timeout', 'config', function ($rootScope, $http, $timeout, config) {

	var firstTry = true;

	return {
		GET: get,
		POST: post,
		PATCH: patch,
		DELETE: remove,

		customCall: customCall
	};

	// method, auth, contentType, headersExtra, data, responseType, callbackSuccess, callbackError, pending

	function get(url, callbackSuccess, callbackError) {
		commonCall(url, "GET", null, callbackSuccess, callbackError);
	}

	function post(url, data, callbackSuccess, callbackError) {
		commonCall(url, "POST", data, callbackSuccess, callbackError);
	}

	function patch(url, data, callbackSuccess, callbackError) {
		commonCall(url, "PATCH", data, callbackSuccess, callbackError);
	}

	function remove(url, callbackSuccess, callbackError) {
		commonCall(url, "DELETE", null, callbackSuccess, callbackError);
	}

	/**
	 * Private common internal layer.
	 * @param url
	 * @param method
	 * @param data
	 * @param callbackSuccess
	 * @param callbackError
	 */
	function commonCall(url, method, data, callbackSuccess, callbackError) {
		call(url, method, 'application/json', data, null, callbackSuccess, callbackError);
	}


	/**
	 * Public "generic" Call method.
	 * @param url
	 * @param method
	 * @param contentType
	 * @param data
	 * @param responseType
	 * @param callbackSuccess
	 * @param callbackError
	 */
	function customCall(url, method, contentType, data, responseType, callbackSuccess, callbackError) {
		call(url, method, contentType, data, responseType, callbackSuccess, callbackError)
	}


	/**
	 * PRIVATE CALL METHOD.
	 * Unified for all Wimbridget calls
	 * @param url				path to the api. Without serverName, only the variable path
	 * @param method			GET, POST, PUT or DELETE
	 * @param contentType
	 * @param data
	 * @param responseType
	 * @param callbackSuccess
	 * @param callbackError
	 */
	function call(url, method, contentType, data, responseType, callbackSuccess, callbackError) {

		var headers = {
			'Content-Type': contentType,
			'Accept': 'application/json'
		};


		/*
			Each wimbridget call is 100% authenticated, so the user must be logged in
		 */

		var bridgetToken = config.getBridgetToken();
		console.log('bridgetToken', bridgetToken);
		if (bridgetToken) {
			headers['Authorization'] = 'Bearer ' + bridgetToken;

			$http({
				method: method,
				url: config.getWimBridgetHost(window.location) + url,
				data: data,
				headers: headers,
				responseType: responseType || null
			}).then(success, callbackError);

		} else {
			// Missing Bridget Token, trying to get a new one and recursively call ourselves.
			// In case the Login fails, the whole call is considered failed.
			login(function (response) {
				// Login successful, call ourselves.
				call(url, method, contentType, data, responseType, callbackSuccess, callbackError);
			}, callbackError);
		}



		/**
		 * Server responds with success status
		 */
		function success(response) {
			var resultError = false;
			try {
				resultError = response.data.result.toLowerCase() == "failure";
			}catch(e){
				// why trycatch if catch is unused?
			}
			if (response.status == 200 && !resultError) {
				callbackSuccess(response);
			} else {
				callbackError(response);
			}
		}
	}

	/**
	 *	Similar to an interceptor, it is called when we need an authentication.
	 *
	 * @param success
	 * @param failure
	 */
	function login(success, failure) {
		if($rootScope.loggedUser && firstTry) {
			firstTry = false;

			$http({
				method: 'POST',
				url: config.getWimBridgetHost(window.location) + 'public/login',
				data: $rootScope.loggedUser,
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				},
				responseType: 'application/json'
			}).then(function (response) {
				// user is now logged: save token
				console.log('login', response);
				config.setBridgetToken(response.data.access_token);
				success(response);
			}, failure);
		} else {
			failure();
		}
	}

}]);
