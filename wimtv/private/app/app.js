'use strict';

	// Per far funzionare gli Iframe
app.filter('trustThisUrl', ['$sce', function ($sce) {
		return function (val) {
			return $sce.trustAsResourceUrl(val);
		};
	}])
	// Per iniettare html
	.filter("sanitize", ['$sce', function ($sce) {
		return function (htmlCode) {
			return $sce.trustAsHtml(htmlCode);
		}
	}])
	// Internalizzazione, carica i file dei testi nelle lingue disponibili
	.config(['$translateProvider', '$stateProvider', '$urlRouterProvider', '$uiViewScrollProvider', '$httpProvider',
		function ($translateProvider, $stateProvider, $urlRouterProvider, $uiViewScrollProvider, $httpProvider) {
			$translateProvider.useStaticFilesLoader({
				files: [{
					prefix: wimtv_plugin_path + 'private/app/json/messages/',
					suffix: '.json'
				}]
			});
			$translateProvider.preferredLanguage('en');
			$translateProvider.useSanitizeValueStrategy(null);

			// Router Configuration
			$uiViewScrollProvider.useAnchorScroll();
			$urlRouterProvider.otherwise("/dashboard");


			// Interceptor
			$httpProvider.interceptors.push('authInterceptor');


		}])

	.run(['$rootScope', '$injector', '$http', '$templateCache', 'authService', '$uibModal', 'config','Pubnub', function($rootScope, $injector, $http, $templateCache, authService, $uibModal, config,Pubnub) {
		// Getting configuration
		$rootScope.wimtvUrl = config.getWimTvHost(window.location);
		$rootScope.bridgetServer = config.getWimBridgetHost(window.location);
		$rootScope.wimtvHost = $rootScope.wimtvUrl;
		$rootScope.debugMode = config.isDebugMode(window.location.hostname);
		$rootScope.CATEGORIES = config.categories;
		$rootScope.vsaUrl = $rootScope.wimtvHost + "/vsa";

		if(localStorage.token) {
			var tokens = JSON.parse(localStorage.token);
			config.setTokens(tokens.public, tokens.private, tokens.refresh, tokens.expiration);
		} else {
			// missing tokens
		}

		$rootScope.getThumbnailUrl = config.getThumbnailUrl;
		$rootScope.getBridgetThumbnail = config.getBridgetThumbnail;
		$rootScope.defaultThumbnail = config.defaultThumbnail;

		// Language
		config.changeLanguage(config.loadLanguage());

		// Legacy changelanguage
		$rootScope.changeLang = config.changeLanguage;

		// Player config
		WimtvPlayer.remoteEndpoint = EndPointConfig().hostname;

		// Verifica il login
		$rootScope.loggedUser = localStorage.getItem('loggedUser');

		config.buildLicenses();

		$rootScope.showHelp = function (param) {
			var params = {
				type: param,
				lang: config.loadLanguage()
			};
			$uibModal.open({
				templateUrl: wimtv_plugin_path + '/private/app/partials/helpModal.html',
				controller: 'helpModalController',
				controllerAs: 'vm',
				resolve: {
					params: function () {
						return params;
					}
				}
			});
		};
		$rootScope.getIframe = config.getIframe;

		$rootScope.logout = function() {
			config.logout();
		};
		$rootScope.loggedUser = config.getLoginData();

		$http.get(wimtv_plugin_path + 'common/directives/form-validators.html').then(function(response) {
			$templateCache.put('form-validator', response.data);
		});

        // PubNub
        Pubnub.init({
            publishKey: 'pub-c-5504ba7c-61b6-4e29-b294-d0a01e03eaf9',
            subscribeKey: 'sub-c-c6bd4094-ec45-11e6-94bb-0619f8945a4f'
        });

	}]);
app.filter('underscoreless', function () {
    return function (input) {
        if (input) {
            return input.replace(/_/g, ' ');
        } return "";

    };
});
app.filter('camelCase', function () {
    return function (input) {
        console.log("apply filter: " + input);
        input = input || '';
        return input.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});

    };
});

