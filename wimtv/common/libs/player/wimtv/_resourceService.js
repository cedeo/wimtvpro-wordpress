angular.module("wimtvApp").factory('resourceService', ['restService', 'config', function (restService, config) {

	return {
		play: play,
		preview: preview,
		pay: pay,
		validateFilters: validateFilters
	};

	/**
	 * Handles the playing of ANY element in wimtv.
	 * @param eventType	box, vod or live
	 * @param eventId
	 * @param resolve
	 * @param reject
	 */
	function play(eventType, eventId, resolve, reject) {
		var url = "/api/";
		if(!config.getLoginData()) {
			url += "public/";
		}

		switch (eventType) {
			case "vod":
			case "box":
				url += eventType + "/" + eventId + "/play";
				break;
			case "live":
				url += eventType + "/channel/" + eventId + "/play";
				break;
			default:
				reject();	// todo: handle
		}

		var data = {};
		var tracking = config.getTrackingId();                  // get trackingId
		if(tracking) {
			data.trackingId = tracking;
		}
		restService.wimTvRestPOST(url, data, success, reject);

		function success(response) {
			// success
			if(response.data.result === "PAYMENT_REQUIRED") {
				reject(response);
			} else {
				var message = "";
				if(response.data.message) {
					message = response.data.message;
				}
				setTimeout(function () {
					var thumbnailId;
					var authorCode;
					if (response.data.resource.eventId) {
						// is a Live Event
						thumbnailId = response.data.resource.channel.thumbnailId? response.data.resource.channel.thumbnailId : response.data.resource.channel.publisher.thumbnailId;
						authorCode = response.data.resource.channel.publisher.userCode;
					} else if (response.data.resource.publisher) {
						thumbnailId = response.data.resource.publisher.thumbnailId;
						authorCode = response.data.resource.publisher.userCode;
					} else {
						thumbnailId = null;
						authorCode = null;
					}
					Player.play ({
						file: response.data.file,
						resource: response.data.resource,
						uniqueStreamer: response.data.uniqueStreamer,
						streamer: response.data.streamer,
						logo: {
							file: config.getThumbnailUrl(thumbnailId),
							link: authorCode? window.location.origin + "/#/webtv/" + authorCode : window.location.origin
						},
						autostart: true,		// always autostart
						result: response.data.result || "PLAY", // PLAY or MESSAGE or BAND_LIMIT_EXCEEDED or PAYMENT_REQUEST. Play as default
						message: message // empty in case of PLAY or PAYMENT_REQUEST, required for BAND_LIMIT_EXCEEDED or MESSAGE
					});
					if(resolve) {
						resolve(response);
					}
				}, 250);
			}
		}
	}

	/**
	 * Handles the preview of ANY element in wimtv. Live are not previewable.
	 * @param eventType
	 * @param eventId
	 * @param resolve
	 * @param reject
	 */
	function preview(eventType, eventId, resolve, reject) {
		var url = "/api/public/" + eventType + "/" + eventId + "/preview";
		restService.wimTvRestPOST(url, null, success, reject);

		function success(response) {
			// success
			setTimeout(function () {
				var thumbnailId;
				var authorCode;
				var message = "";
				if (response.data.resource.publisher) {
					thumbnailId = response.data.resource.publisher.thumbnailId;
					authorCode = response.data.resource.publisher.userCode;
				} else {
					thumbnailId = null;
					authorCode = null;
				}
				Player.play ({
					file: response.data.file,
					resource: response.data.resource,
					uniqueStreamer: response.data.uniqueStreamer,
					streamer: response.data.streamer,
					logo: {
						file: config.getThumbnailUrl(thumbnailId),
						link: authorCode? window.location.origin + "/#/webtv/" + authorCode : window.location.origin
					},
					preview: true,
					autostart: false,		// always autostart
					result: response.data.result || "PLAY", // PLAY or MESSAGE or BAND_LIMIT_EXCEEDED or PAYMENT_REQUEST. Play as default
					message: message // empty in case of PLAY or PAYMENT_REQUEST, required for BAND_LIMIT_EXCEEDED or MESSAGE
				});
				if(resolve) {
					resolve(response);
				}
			}, 250);
		}
	}

	/**
	 * Handles Payment for ANY element in wimtv.
	 * @param eventType
	 * @param itemId
	 * @param options
	 * @param resolve
	 * @param reject
	 */
	function pay(eventType, itemId, options, resolve, reject) {

		var url;
		switch (eventType) {
			case "vod":
				url = "/api/public/" + eventType + "/" + itemId + "/pay";
				break;
			case "live":
				url = '/api/public/' + eventType + '/event/' + itemId + '/pay';
				break;
			case "license":
				url = '/api/license/' + itemId + '/subscribe';
				break;
			case "contentbundle":
				url = '/api/contentbundle/' + itemId + '/subscribe';
				break;
			case "marketplace":
				url = '/api/marketplace/' + itemId + '/pay';
				break;
			default:
				reject();	// todo: handle
		}


		// default options
		var opt = {
			embedded : false,
			returnUrl : "http://www.wim.tv/live/event/play",
			cancelUrl : "http://www.wim.tv/live/event/rejected",
			mobile : false
		};

		// overriding default options
		for(var k in options) {
			if(options.hasOwnProperty(k)) {
				opt[k] = options[k];
			}
		}

			// TODO: test this one!
		// var tokens = config.tokens;                             // get tokens
		// if(tokens.private) {                                    // if private token is available => use private call
		// 	url = '/api/' + eventType + '/event/' + eventId + '/pay';
		// }

		restService.wimTvRestPOST(
			url,
			opt,
			function (response) {
				config.setTrackingId(response.data.trackingId);		// save the trackingId
				// window.location = response.data.url;				// redirects to paypal payments: delegated to invoker
				resolve(response);
			},
			function (response) {
				reject(response);
			});
	}


	/**
	 * support function for validating searchfilters
	 * @param filters
	 * @returns {*}
	 */
	function validateFilters(filters) {
		if(!filters.pageIndex) {
			filters.pageIndex = 0;
		}
		if(!filters.pageSize) {
			filters.pageSize = 10;
		}
		if(filters.queryString && filters.queryString.substring(filters.queryString.length) !== "*") {
			filters.queryString += "*";
		}
		return filters;
	}

}]);