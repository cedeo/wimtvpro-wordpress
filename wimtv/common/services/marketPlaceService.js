angular.module("wimtvApp").factory('marketPlaceService', ['restService', 'resourceService', 'config', function(restService, resourceService, config) {

	/**
	 * Service interface
	 */
	return {
		publish: publish,
		searchInPrivate : searchInPrivate,
		searchInPublic : searchInPublic,
		deleteVideo : deleteVideo,
		updateVideo : updateVideo,
		readVideo : readVideo,
        readVideoPublic : readVideoPublic,
		pay : pay,
		preview : preview,
		activateMarketItem : activateMarketItem,
		acquire : acquire
	};

	/**
	 * Pay per view
	 License Type: Creative Commons --> disabiltare duration & prices --> enable download
	 Free --> disabilitare prices & abiltare duration --> enable download
	 Pay Per view --> enable price per view --> disable download
	 Revenue sharing --> enable eariningPercentage --> disable download
	 Spot Price --> enable activation price --> enable download
	 * @param policy
	 * @param callbackSuccess
	 * @param failureCallback
     */
	function publish(policy, callbackSuccess, failureCallback) {
		var boxId = policy.boxId;
		if (boxId != null) {
			// http://www.wim.tv:8080/api/box/b9b6103d-fa5a-42c4-9e1f-e06b6a779dad/marketplace'
			restService.wimTvRestPOST('/api/box/' + boxId  + '/marketplace',
				policy,
				callbackSuccess,
				failureCallback);

		} else {
			// TODO: handle
		}
	}

	/**
	 * Deletes an existing Market Place item
	 * curl 'http://www.wim.tv:8080/api/marketplace/404a6f9a-803a-4d80-940b-522884701c49' -i -X DELETE -H 'Accept-Language: it' -H 'X-Wimtv-timezone: 3600000'
	 *
	 * @param marketPlaceId marketplace identifier
	 * @param callbackSuccess
	 * @param failureCallback
	 */
	function deleteVideo(marketPlaceId,callbackSuccess,callbackFailure) {
		var path = "/api/marketplace/" + marketPlaceId;
		restService.wimTvRestAuthDELETE(path,callbackSuccess,callbackFailure);
	}

	/**
	 * Updates an existing Market Place item
	 *
	 * @param marketPlaceId marketplace identifier
	 * @param callbackSuccess
	 * @param failureCallBack
     */
	function updateVideo(marketPlaceId,callbackSuccess,callbackFailure) {
		var path = "/api/marketplace/" + marketPlaceId;
		restService.wimTvRestAuthPOST(marketPlaceId,callbackSuccess,callbackFailure);
	}

	/**
	 * Reads a Market Place item
	 *
	 * @param marketPlaceId marketplace identifier
	 * @param callbackSuccess
	 * @param callbackFailure
     */
	function readVideo(marketPlaceId,callbackSuccess,callbackFailure) {
		var path = "/api/marketplace/" + marketPlaceId;
		restService.wimTvRestAuthGET(path,callbackSuccess,callbackFailure);
	}

    function readVideoPublic(marketPlaceId,callbackSuccess,callbackFailure) {
        var path = "/api/public/marketplace/" + marketPlaceId;
        restService.wimTvRestAuthGET(path,callbackSuccess,callbackFailure);
    }

	/**
	    Searches for Market Place items in private pages
	 		$ curl 'http://www.wim.tv:8080/api/search/marketplace/contents' -i -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -H 'Accept-Language: it' -H 'X-Wimtv-timezone: 3600000' -d '{
  			"queryString" : "tag21 tag32",
  			"pageSize" : 20,
  			"pageIndex" : 0
			}'
	 */
	function searchInPrivate(queryData, success, failure) {
		var url = "/api/search/marketplace/contents";
		restService.wimTvRestPOST(url, queryData, success, failure);
	}

	/**
		Search for Market Place Public items
     */
	function searchInPublic(queryData, success, failure) {
		var url = "/api/public/search/marketplace/contents";
		restService.wimTvRestPOST(url, queryData, success, failure);
	}

	/* Pays to acquire a Market Place item
	 * $ curl 'http://www.wim.tv:8080/api/marketplace/e2cb6399-2537-4f07-8bf2-fd5d6e2ef9b2/pay' -i -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -H 'Accept-Language: it' -H 'X-Wimtv-timezone: 3600000' -d '{
	 * "embedded" : false,
	 *	"mobile" : false,
     *		"returnUrl" : "http://www.wim.tv/marketplace/acquire",
	 *	"cancelUrl" : "http://www.wim.tv/marketplace/rejected"
	 */

	function pay(itemId, resolve, reject) {
		var callbackUrl =  window.location.href;
		var data = {
			emdedded : false,
			mobile : false,
			returnUrl : callbackUrl,
			cancelUrl : callbackUrl
		};
		resourceService.pay("marketplace", itemId, data, function (response) {
			config.setMarketPaymentData(response.data.trackingId,itemId);		// save the trackingId
			resolve(response);
		}, reject);
	}

	/**
	 * Acquires a Market Place item
     * $ curl 'http://www.wim.tv:8080/api/marketplace/a7c4201f-330a-43ae-ae80-27b9cbc8bd26/acquire' -i -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -H 'Accept-Language: it' -H 'X-Wimtv-timezone: 3600000' -d '{ }'
	 */
	function acquire(itemId,success,failure) {
		var url = "/api/marketplace/" + itemId + "/acquire";
		restService.wimTvRestPOST(url, {}, success, failure);
	}

	function activateMarketItem(trackingId, itemId, success, failure) {
		console.log("activate market item id: " + itemId);
		var url = "/api/marketplace/" + itemId + "/acquire";
		var data = {
			trackingId: trackingId
		};
		restService.wimTvRestAuthPOST(url, data, success, failure);
	}


	function preview(marketPlaceId, success, failure) {
		resourceService.preview('marketplace', marketPlaceId, success, failure);
	}
}]);