angular.module("wimtvApp").factory('bundleService', ['restService', 'resourceService', 'vodService', 'config', function(restService, resourceService, vodService, config) {

	/**
	 * Service interface
	 */
	return {
		create: create,
		update: update,
		remove: remove,
		getPublic: getPublic,
		getPrivate: getPrivate,
		searchPrivate: searchPrivate,
		searchPublic: searchPublic,
		activateBundle : activateBundle,
		pay : pay
	};



	function create(newBundle, success, failure) {
		restService.wimTvRestPOST("/api/contentbundle", newBundle, success, failure);
	}
	
	function update(bundle, success, failure) {
		restService.wimTvRestPOST("/api/contentbundle/" + bundle.bundleId, bundle, success, failure);
	}

	function remove(bundle, success, failure) {
		restService.wimTvRestAuthDELETE("/api/contentbundle/" + bundle.bundleId, success, failure);
	}

	function getPublic(id, success, failure) {
		var url = "/api/public/contentbundle/" + id;
		restService.wimTvRestGET(url, success, failure);
	}

	function getPrivate(id, success, failure) {
		get('private', id, success, failure);
	}

	/**
	 * common get method
	 * @param type		'public' or 'private'
	 * @param id
	 * @param success
	 * @param failure
	 */
	function get(type, id, success, failure) {
		var url = "/api";
		if(type === "public") {
			url += "/public";
		}
		url += "/contentbundle/" + id;
		restService.wimTvRestGET(url, success, failure);
	}


	function searchPrivate(queryData, success, failure) {
		var url = "/api/search/contentbundles/";
		restService.wimTvRestPOST(url, queryData, success, failure);
	}

	/**
	 * Search bundle
	 * @param queryData query string plus pagination
	 * @param success success callback
	 * @param failure failure callback
	 */
	function searchPublic(queryData, success, failure) {
		var url = "/api/public/search/contentbundles";
		restService.wimTvRestPOST(url, queryData, success, failure);
	}



	function pay(bundleId, resolve, reject) {
		var callbackUrl =  window.location.href;
		var data = {
			emdedded : false,
			mobile : false,
			returnUrl : callbackUrl,
			cancelUrl : callbackUrl
		};
		resourceService.pay("contentbundle", bundleId, data, function (response) {
			config.setBundlePaymentData(response.data.trackingId,bundleId);		// save the trackingId
			resolve(response);
		}, reject);
	}

	/**
	 * Activates a license checking for the succeeded payment
	 *
	 * @param trackingId payment tracking id
	 * @param bundleId
	 * @param success callback
	 * @param failure callback
	 */
	function activateBundle(trackingId, bundleId, success, failure) {
		console.log("activateBundle: " + bundleId);
		var data = {
			trackingId: trackingId
		};
		restService.wimTvRestAuthPOST(
			"/api/contentbundle/" + bundleId +"/activate",
			data,
			success,
			failure
		);
	}
}]);