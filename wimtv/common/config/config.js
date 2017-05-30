// wimtv-frontend v.1.2.2b170221
angular.module("wimtvApp").factory('config', ['$rootScope', '$base64', '$translate', '$timeout', '$analytics', function ($rootScope, $base64, $translate, $timeout, $analytics) {
	/**
	 * Dynamic configuration of the entire application
	 */
	var endpoints = EndPointConfig();

	function resetToast(level, options) {
			if((level === "error" || level === "warning") && !options) {
				toastr.options.closeButton = true;
				toastr.options.timeOut = 0;
				toastr.options.extendedTimeOut = 0;
				toastr.options.positionClass = "toast-top-center";
			} else {
				toastr.options.closeButton = false;
				toastr.options.timeOut = 5000;
				toastr.options.extendedTimeOut = 1000;
				toastr.options.positionClass = "toast-top-right";
			}
	}

	/**
	 * Analytics tracking function. For tracking custom events
	 * @param eventName the name of the custom event
	 */
	function track(eventName, options) {
		if($analytics && $analytics.pageTrack && $analytics.eventTrack) {
			$analytics.pageTrack(window.location.href);
			$analytics.eventTrack(eventName);
		}
	}

	function setTitle(title) {
		$timeout(function () {
			$rootScope.pageTitle = title;
		}, 10);
	}
	function setDescription(title) {
		$timeout(function () {
			$rootScope.pageDescription = title;
		}, 10);
	}
	var wimtvLocation;
	var bridgetToken;
	function isDebugMode(hostname) {
		switch (hostname) {
			case "localhost":
			case "127.0.0.1":
			case "52.19.105.240":
			case "52.212.180.76":
			case "server.wimbridge.tv":
			case "peer.wim.tv":
				return true;
			default:
				return null;
		}
	}
	return {
		/**
		 * Centralized control of production or debug/develop environment. USED PRETTY MUCH EVERYWHERE.
		 * @param hostname
		 * @returns {boolean}
		 */
		isDebugMode: function(hostname) {
			return endpoints.isDebug;
		},

		interceptorValid: ['peer.wim.tv:8443', 'new.wim.tv'],
		getWimTvHost: function(location) {
			return endpoints.wimtvServer;
		},

		getWimBridgetHost: function (location) {
			var host = "https://new.wim.tv/wimbridge-server/rest/";
			if (isDebugMode(window.location.hostname)){
				host = "https://peer.wim.tv:8443/wimbridge-server/rest/";
			}
			return host;

		},

		isDebugMode: isDebugMode,

		getHeaders: function (CLIENT_ID, CLIENT_SECRET) {
			if (!CLIENT_ID) {
				CLIENT_ID = "www";
			}
			if (!CLIENT_SECRET) {
				CLIENT_SECRET = "";
			}

			return {
				'Accept': 'application/json',
				'Authorization': 'Basic ' + $base64.encode(CLIENT_ID + ':' + CLIENT_SECRET),
				'Content-Type': 'application/x-www-form-urlencoded'
			};
		},

		// TOKEN HANDLING
		tokenUrl: '/oauth/token',

		setTokens: function (public, private, refresh, expiration) {
			var token = this.getTokens();
			if (!token) {
				token = {};
			}

			if (public) {
				token.public = public;
			}
			if (private) {
				token.private = private;
			}
			if (refresh) {
				token.refresh = refresh;
			}
			if (expiration) {
				token.expiration = expiration;
				token.expires = new Date().getTime() + (expiration*900);
				console.log("Token: " + token.private +  " | life: " + expiration + " | expires in " + new Date(token.expires));
			}

			localStorage.setItem('token', JSON.stringify(token));
			this.tokens = token;
		},

		getTokens: function () {
			return JSON.parse(localStorage.getItem('token'));
		},

		existPrivateToken: function() {
			return this.getTokens().private != null;
		},

		tokens: {},

		/**
		 * Gets the Bridget authentication token.
		 * Check both in the config and in the localstorage.
		 * Missing this one means the user is still not logged in WimBridget application
		 * @returns {*}
		 */
		getBridgetToken: function () {
			if(bridgetToken) {
				return bridgetToken;
			} else {
				bridgetToken = localStorage.getItem('bridgetToken');
				if(bridgetToken) {
					return bridgetToken;
				}
			}
			return null;
		},
		setBridgetToken: function (token) {
			if(token) {
				bridgetToken = token;
				localStorage.setItem('bridgetToken', token);
			}
		},

		timeFromCustomDate: function (dateObject) {
			return moment(dateObject.date + dateObject.time, "DD/MM/YYYYHH:mm:ss").toDate();
		},

		customDateFromTime: function (timeOrDate) {

		},

		getIframe: function (eventType, eventId) {
			return '&lt;div style="position: relative; height: 0; overflow: hidden; padding-bottom: 56.25%;"&gt;&lt;iframe src="https://new.wim.tv/embed/?' + eventType + "=" + eventId + '" allowfullscreen="" style="border:none; position: absolute; top:0; left: 0; width: 100%; height: 100%;"&gt;&lt;/iframe&gt;&lt;/div&gt;';
		},

		/**
		 * Just a simple loader from localstorage or browser.
		 * @returns {*} the new language loaded (encoded)
		 */
		acceptedLanguages: ['it', 'en', 'es', 'fr', 'pt'],

		loadLanguage: function () {
			// load saved language on localstorage from localStorage (if fails, from browser)
			var currentLang = localStorage.lang ? localStorage.lang : navigator.language;

			currentLang = currentLang ? currentLang : $translate.proposedLanguage();

			// gestione dei casi in cui la lingua del browser non è tra quelle accettate
			currentLang = this.acceptedLanguages.indexOf(currentLang.substring(0,2)) == -1 ? 'en' : currentLang.substring(0,2);

			return currentLang;
		},

		changeLanguage: function (lang) {
			$translate.use(lang);
			localStorage.lang = lang;
			$rootScope.currentLang = lang;
			moment.locale(lang);
			$rootScope.$broadcast("changeLanguage", lang); // broadcasting language changed
			track('changeLanguage', {language: lang});
		},

		categories: ['Art', 'Films - Animation', 'People - Life', 'Music', 'Products - Commercial', 'Travels - Events', 'Activism - Non Profits', 'Sports', 'Sciences - Technology', 'Video Production', 'News - Information', 'Casting', 'Entertainment', 'Nature - Animals'],

		defaultThumbnail : '/public/assets/img/thumbnail.png',
		getThumbnailUrl: function (thumbnailId) {
			if(thumbnailId) {
				return $rootScope.wimtvUrl + '/asset/thumbnail/' + thumbnailId;
			} else {
				return this.defaultThumbnail;
			}
		},

		getBridgetThumbnail: function (asset) {
			var thumb = this.defaultThumbnail;
			if(asset.wimtvThumbUrl && asset.wimtvThumbUrl.indexOf("undefined") == -1) {
				thumb = asset.wimtvThumbUrl;
			} else if (asset.thumb_url && asset.thumb_url.indexOf("undefined") == -1){
				thumb = asset.thumb_url;
			}
			return thumb;
		},

		// RESPONSE HANDLING WITH TOAST

		message: function (level, message) {
			resetToast(level);
			toastr[level](message);
		},
		success: function (key, options) {
			this.toast("success", key, null, options);
		},
		error: function (key, response, options) {
			this.toast("error", key, response, options);
		},
		info: function (key, response, options) {
			this.toast("info", key, response, options);
		},
		warning: function (key, response, options) {
			this.toast("warning", key, response, options);
		},
		/**
		 * PUBLIC METHOD FOR GENERATING TOAST MESSAGE STARTING FROM A LANGUAGE CODE.
		 * For error message the Default behaviour is unlimited time with close button available
		 * TODO: handle custom options
		 * @param level     toastr level. Available: [error] [warning] [info] [success]
		 * @param key       language key
		 * @param response  the response got from error
		 * @param options   additional options
		 */
		toast: function (level, key, response, options) {
			// if(level !== "error") {
				toastr.clear();     // clear previous toasts (IF NOT error)
			// }
			$translate(key).then(function (message) {
				resetToast(level, options);
				var string = "";
				if (response && response.data) {
					if (response.data.message) {
						string += "<p>" + response.data.message + "</p>";
					}
					if (response.data.errors) {
						string += "<p>";
						for (var k in response.data.errors) {
							var error = response.data.errors[k];
							string += error.field + ": " + error.defaultMessage + "<br />";
						}
						string += "</p>";
					}
					if(response.data.fieldErrors) {
						for (var k in response.data.fieldErrors) {
							if(response.data.fieldErrors.hasOwnProperty(k) && response.data.fieldErrors[k].errors) {
								for (var j in response.data.fieldErrors[k].errors) {
									string += "<p>" + response.data.fieldErrors[k].errors[j] + "</p>";
								}
							} else {
								string += "<p>" + response.data.fieldErrors[k] + "</p>";
							}
						}
					}
				}
				toastr[level](string, message);
				// automatically disable any kind of loading. TODO: use centralized
				$rootScope.globalLoading = false;
			});
		},

		globalLoading: function (loading) {
			$rootScope.globalLoading = loading;
		},

		// todo: handle errors
		saveLoginData: function (data) {
			localStorage.setItem('loggedUser', JSON.stringify(data));
		},

		getLoginData: function () {
			var loggedUser = localStorage.getItem('loggedUser');
			if(!loggedUser || loggedUser === "undefined") {
				localStorage.removeItem('loggedUser');
				return null;
			}
			return JSON.parse(loggedUser)
		},

		/**
		 * Logout centralized function
		 */
		logout: function(isForced) {
			localStorage.clear();
			$rootScope.loggedUser = null;
			var destinationUrl = '/#';
			if(isForced) {
				destinationUrl += '?forcedLogout';
			}
			window.location = destinationUrl;
		},
		/**
		 * Tracking id Package Payment
		 */
		resetPacketPaymentData: function() {
			localStorage.removeItem('packetTrackingId');
			localStorage.removeItem('packetName');
		},
		setPacketPaymentData: function(trackingId,packageName) {
			localStorage.setItem('packetTrackingId', JSON.stringify(trackingId));
			localStorage.setItem('packetName', JSON.stringify(packageName));
		},
		getPacketPaymentData: function() {
			return  {
				trackingId: JSON.parse(localStorage.getItem('packetTrackingId')),
				packageName: JSON.parse(localStorage.getItem('packetName'))
			};
		},
        setPageTitle: function(pageTitle) {
            localStorage.setItem('pageTitle', pageTitle);
        },
        getPageTitle: function() {
            return localStorage.getItem('pageTitle');
        },
        setThumbnaildId: function(thumbnailId) {
            localStorage.setItem('thumbnailId', thumbnailId);
        },
        getThumbnailId: function() {
            return localStorage.getItem('thumbnailId');
        },
		/**
		 * Trancking id Bundle Payment
		 */
		resetBundlePaymentData: function() {
			localStorage.removeItem('bundleTrackingId');
			localStorage.removeItem('acquiring_bundleId');
		},
		setBundlePaymentData: function(trackingId,bundleId) {
			localStorage.setItem('bundleTrackingId', JSON.stringify(trackingId));
			localStorage.setItem('acquiring_bundleId', JSON.stringify(bundleId));
		},
		getBundlePaymentData: function() {
			return  {
				trackingId: JSON.parse(localStorage.getItem('bundleTrackingId')),
				bundleId: JSON.parse(localStorage.getItem('acquiring_bundleId'))
			};
		},
		/**
		 * Trancking id market place
		 */
		resetMarketPaymentData: function() {
			localStorage.removeItem('marketTrackingId');
			localStorage.removeItem('acquiring_marketId');
		},
		setMarketPaymentData: function(trackingId,marketId) {
			localStorage.setItem('marketTrackingId', JSON.stringify(trackingId));
			localStorage.setItem('acquiring_marketId', JSON.stringify(marketId));
		},
		getMarketPaymentData: function() {
			return  {
				trackingId: JSON.parse(localStorage.getItem('marketTrackingId')),
				marketItemId: JSON.parse(localStorage.getItem('acquiring_marketId'))
			};
		},


		// TRACKING ID FOR PAYMENTS
		setTrackingId: function (trackingId, eventId) {
			// JUST ONE TRACKINGID
			localStorage.setItem('trackingId', JSON.stringify(trackingId));

			// ONE TRACKINGID = ONE EVENT
			// var tracking = JSON.parse(localStorage.getItem('trackingId'));
			// if(!tracking) {
			//     tracking = {};
			// }
			// tracking[eventId] = trackingId;
			// localStorage.setItem('trackingId', JSON.stringify(tracking));
		},
		getTrackingId: function () {
			return JSON.parse(localStorage.getItem('trackingId'));
		},

		/**
		 * UUID generator for wimbox
		 * @returns {string}
		 */
		generateUUID: function () {
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
				var r = Math.random() * 16 | 0,
					v = c == 'x' ? r : (r & 0x3 | 0x8);
				return v.toString(16);
			});
		},

		setTitle: setTitle,
		setTitleMultilanguage: function (key, prefixMessage) {
			$translate(key).then(function (message) {
				if(prefixMessage) {
					message = prefixMessage + " " + message;
				}
				setTitle(message);
			});
		},
		setDescription: setDescription,
		setDescriptionMultilanguage: function (key, prefixMessage) {
			$translate(key).then(function (message) {
				if(prefixMessage) {
					message = prefixMessage + " " + message;
				}
				setDescription(message);
			});
		},

		/**
		 * Check validity of user profile for publishing any kind of payed content.
		 * @param userService for make the calls
		 * @param callback
		 */
		checkPaypalAndFinance: function (userService, callback) {
			userService.getProfile().then(success);

			function success(response) {
				var res = {
					paypal: !response.data.finance || !response.data.finance.paypalEmail,
					billing: (
						response.data.finance && (
						!response.data.finance.billingAddress ||
						!(
							response.data.finance.vatNumber ||
							response.data.finance.taxCode
						) ||
						!response.data.finance.billingAddress.street ||
						!response.data.finance.billingAddress.city ||
						!response.data.finance.billingAddress.zipCode ||
						!response.data.finance.billingAddress.state ||
						!response.data.finance.billingAddress.country)
					)
				};
				callback(res);
			}
		},

		// TODO: handle licence with multilanguage support
		buildLicenses: function () {
			// var baseKey = "lang.LICENSE.";
			// for (var key in this.licenseTypes) {
			// 	if(this.licenseTypes.hasOwnProperty(key)) {
			// 		// translate(baseKey + key, this.licenseTypes[key]);
			// 		$translate(baseKey + key).then(function (message) {
			// 			config.licenseTypes[key] = message;
			// 		});
			// 	}
			// }
			// for (var key in this.ccTypes) {
			// 	if(this.licenseTypes.hasOwnProperty(key)) {
			// 		translate(baseKey + key, this.licenseTypes[key]);
			// 	}
			// }
			// function translate(key, target) {
			// 	$translate(key).then(function (message) {
			// 		target = message;
			// 	});
			// }
		},
		track: track,
		licenseTypes: {
			"FREE": "Free",
			"CREATIVE_COMMONS": "Creative Commons",
			"PAY_PER_VIEW": "Pay Per View"
			// "CONTENT_BUNDLE": "Content Bundle"		// TODO: ABILITARE GLI ABBONAMENTI
		},
		// Market Place Constants
		marketPlaceLicenseType: {
			"FREE" : "Free",
			"CREATIVE_COMMONS" : "Creative Commons",
			"SPOT_PRICE" : "Spot price",
			"PAY_PER_VIEW" : "Cash per view",
			"REVENUE_SHARING" : "Revenue sharing"
		},
		marketPlaceDurationUnit: {
			"DAYS": "Days",
			"MONTHS": "Months",
			"YEARS": "Years"
		},
		ccTypes: {
			"BY": "Attribution",
			"BY_SA": "Attribution, share-alike",
			"BY_ND": "Attribution, no derivatives",
			"BY_NC": "Attribution, non commercial",
			"BY_NC_SA": "Attribution, non commercial, share-alike",
			"BY_NC_ND": "Attribution, non commercial, no derivatives"
		}
	}

}]);
