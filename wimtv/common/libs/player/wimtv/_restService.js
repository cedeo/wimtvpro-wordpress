angular.module("wimtvApp").factory('restService', ['$rootScope', '$http','$timeout', 'config', function ($rootScope, $http, $timeout, config) {
	/**
	 * Centralized wimtv-backend REST API handler.
	 */

	var restService = {};

	/**
	 * Private. For Handling ALL Rest Call.
	 * This still calls $http, and wrap promises into callback. Not so nice. We may just use promises (but we have to do a big refactoring)
	 * @param path  URI
	 * @param method
	 * @param contentType
	 * @param data
	 * @param callbackSuccess
	 * @param callbackError
	 */
	var call = function (path, method, contentType, data, callbackSuccess, callbackError) {
		var context = getContextFromURL(path);
		var callData = buildCallData(path, method, contentType, data, context);
		$http(callData).then(success).catch(failure);

		function success(response) {
			if (response) {
				console.log("Response status: " + response.status);
				if(response.status >= 200 && response.status < 300) {
					callbackSuccess(response, []);
				} else {
					callbackError(response);
				}
			} else {
				console.log("Warning: Success but response was undefined");
			}
		}
		function failure(response) {
			callbackError(response);
		}
	};

	/**
	 * private method for building original call data.
	 * @param path
	 * @param method
	 * @param data
	 */
	function buildCallData(path, method, contentType, data, context) {
		var url = $rootScope.wimtvUrl + path;
		var headers = {
			'Content-Type': contentType,
			'Accept': 'application/json',
			'Accept-Language': $rootScope.currentLang,
			'X-Wimtv-timezone': -(new Date().getTimezoneOffset() * 60 * 1000)
		};
		headers['Authorization'] = getAuthorizationHeader(path, context);
		return {
			method: method,
			url: url,
			data: data,
			headers: headers
		};
	}

	/**
	 * Promise to regenerate the token, based on the context.
	 * @param context
	 * @returns {*}
	 */
	function refreshTokens(context) {
		var headers = config.getHeaders();
		var grant = generateGrantFromContext(context);
		return $http({
			method: 'POST',
			url: $rootScope.wimtvUrl + config.tokenUrl,
			data: grant,
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			},
			headers: headers
		});
	}

	var publicUrls = ['public', 'oauth'];

	/**
	 * Generates the context based only on the url
	 * @param url
	 * @returns {*}
	 */
	function getContextFromURL (url) {
		var split = url.split("/");

		if(publicUrls.indexOf(split[2]) !== -1) {
			return "public";
		} else {
			return "private";
		}
	}

	/**
	 * Generates the Grant & Granttype based on the context given
	 * @param context
	 * @returns {{grant_type: string}}
	 */
	function generateGrantFromContext (context) {
		var grant = {grant_type:'client_credentials'};
		if (context === "private") {
			grant = {
				refresh_token: config.tokens.refresh,
				grant_type: "refresh_token"
			}
		}
		return grant;
	}

	/**
	 * generate the Authorization header based on the context given
	 * @param path
	 * @param context
	 * @returns {string}
	 */
	function getAuthorizationHeader(path, context) {
		if(config.tokenUrl !== path) {

			if (context === "public") {
				return "Bearer " + config.tokens.public;
			} else {
				return "Bearer " + config.tokens.private;
			}

		}
	}

	/**
	 * PUBLIC INTERFACES
	 * TODO: UNIFY IN ONLY 4 MAIN CALLS: GET POST PUT DELETE (no more difference between authenticated calls and non-authenticated ones)
	 */

	restService.wimTvRestAuthGET = function (path, callbackSuccess, callbackError) {
		call(path, 'GET', 'application/json', null, callbackSuccess, callbackError);
	};
	restService.wimTvRestAuthPOST = function (path, data, callbackSuccess, callbackError) {
		call(path, 'POST', 'application/json', data, callbackSuccess, callbackError);
	};
	restService.wimTvRestAuthDELETE = function (path, callbackSuccess, callbackError) {
		call(path, 'DELETE', 'application/json', null, callbackSuccess, callbackError);
	};
	restService.wimTvRestAuthCustom = function (path, method, contentType, data, callbackSuccess, callbackError) {
		call(path, method, contentType, data, callbackSuccess, callbackError);
	};
	restService.wimTvRestGET = function (path, callbackSuccess, callbackError) {
		call(path, 'GET', 'application/json', null, callbackSuccess, callbackError);
	};
	restService.wimTvRestPOST = function (path, data, callbackSuccess, callbackError) {
		call(path, 'POST', 'application/json', data, callbackSuccess, callbackError);
	};
	restService.wimTvRestCustom = function (path, method, contentType, data, callbackSuccess, callbackError) {
		call(path, method, contentType, data, callbackSuccess, callbackError);
	};

	return restService;
}]);
