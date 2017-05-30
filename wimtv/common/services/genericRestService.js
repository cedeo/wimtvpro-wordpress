angular.module("wimtvApp").factory('genericRestService', ['$http', 'config', function ($http, config) {

	var localServerData = {
		method: 'POST',
		url: wimtv_plugin_path + 'api.php'
	};
	/**
	 * Private. Promise for Handling ALL Rest Call.
	 * @param url FULL URI
	 * @param method
	 * @param data
	 */
	var call = function (url, method, data) {
		var callData = {
			method: method,
			url: url,
			data: data,
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json'
			}
		};
		localServerData.data = callData;
		return $http(localServerData);
	};

	return {
		GET: function (url, data) {
			return call(url, 'GET', data);
		},
		POST: function (url, data) {
			return call(url, 'POST', data);
		}
	}

}]);
