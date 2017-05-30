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
		var data = {
			eventType: eventType,
			eventId: eventId,
			playerId: "#theplayer"
		};
		var tracking = config.getTrackingId();                  // get trackingId
		if(tracking) {
			data.trackingId = tracking;
		}

		if(eventType === "box") {
			WimtvPlayer.preview(data,
				restService.wimTvRestAuthPOST,
				function (resp, player, config, sources) {
					resolve(resp);
				},
				function (resp, player, config, sources) {
					reject(resp);
				}
			);
		} else {
			WimtvPlayer.play(data,
				restService.wimTvRestAuthPOST,
				function (resp, player, config, sources) {
					resolve(resp);
				},
				function (resp, player, config, sources) {
					reject(resp);
				}
			);
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
		var data = {
			eventType: eventType,
			eventId: eventId,
			playerId: "#theplayer"
		};

		WimtvPlayer.vodPreview(data, restService.wimTvRestAuthPOST, success, failure);

		function success (resp, player, config, sources) {
			if(resolve) {
				resolve(resp);
			}
		}
		function failure (resp, player, config, sources) {
			if(reject) {
				reject(resp);
			}
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