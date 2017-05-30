angular.module("wimtvApp").factory('authInterceptor', ['$injector', '$rootScope', '$q', 'config', function($injector, $rootScope, $q, config) {

	var CONTEXT = {
		PUBLIC: "public",
		PRIVATE: "private"
	};
	var publicUrls = ['public', 'oauth'];
	function getContextFromURL(url) {
		var split = ["", url];
		if(url.indexOf("http://") !== -1) {
			split = url.split($rootScope.wimtvUrl + "/");
		}
		if(split[1].indexOf("api/") !== -1) {
			split = split[1].split("api/");
		}
		split = split[1].split("/");

		if(publicUrls.indexOf(split[0]) !== -1) {
			return CONTEXT.PUBLIC;
		} else {
			return CONTEXT.PRIVATE;
		}
	}

	function getAuthorizationHeader(path, context) {
		if(config.tokenUrl !== path && ($rootScope.wimtvUrl + config.tokenUrl !== path)) {
			if (context === "public") {
				return "Bearer " + config.tokens.public;
			} else {
				return "Bearer " + config.tokens.private;
			}
		}
	}

	return {
		/**
		 * Request injector for token, language and timezone for each call.
		 * @param config the original request config
		 * @returns {*}
		 */
		request: function(config) {
			var auth = getAuthorizationHeader(config.url, getContextFromURL(config.url));
			if (auth) {
				config.headers['Authorization'] = auth;
			}
			config.headers['Accept-Language'] = 'it';
			config.headers['X-Wimtv-timezone'] = -(new Date().getTimezoneOffset() * 60 * 1000);

			if(config.url.indexOf(EndPointConfig().hostname) === 0) {
				config.data = {
					url: config.url,
					data: config.data,
					method: config.method,
					headers: config.headers
				};
				config.method = 'POST';
				config.url = wimtv_plugin_path + 'api.php';

			}
			return config;
		},

		responseError: function (config) {
			// force redirect
			if(config.status === 301) {
				var index = config.data.indexOf("http://");
				var url = config.data.substr(index);
				window.location.href = url;
			}
			return config;
		}
	};

}]);
