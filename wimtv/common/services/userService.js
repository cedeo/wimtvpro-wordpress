angular.module("wimtvApp").factory('userService', ['restService', '$q','config', 'userCustomizationService', function(restService, $q, config, userCustomizationService) {

	/**
	 * Service interface
	 */
	return {
		getProfile: getProfile,
		getMeOverview: getMeOverview,
		updateProfile: updateProfile,
		changePassword: changePassword,
		requestPacketPay: requestPacketPay,
		downgrade: downgrade,
		getUser: getUser,
		activateEmail: activateEmail,
		resetPw: resetPw,
		renewPassword: renewPassword,
		activateLicense:activateLicense,
		search : search
	};

	/**
	 * Service Methods
	 */
	
	function resetPw(params) {
		return $q(function(resolve, reject) {
			restService.wimTvRestPOST(
				'/api/public/user/password/renew',
				params,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		});
	}

	function renewPassword(token) {
		return $q(function(resolve, reject) {
			restService.wimTvRestGET(
				'/api/public/user/password/renew?token=' + token,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		});
	}

	/**
	 * get logged user profile. Complete with ALL information
	 * @returns a promise with the data
	 */
	function getProfile() {
		return $q(function(resolve, reject) {
			restService.wimTvRestAuthGET(
				'/api/user/me',
				function (response) {
					if(response.data && response.data.userCode) {
						// enrich with userCustomization info
						userCustomizationService.getUser(response.data.userCode).then(function (userCustomization) {
                            response.data.userCustomization = userCustomization.data.data;
                          	resolve(response);
						}).catch(reject);
					} else {
						// without userCustomization info
						resolve(response);
					}
				},
				function (response) {
					reject(response);
				});
		});
	}

	/**
	 * Get overview info of logged user's profile, useful for showing in private page but not in profile ones.
	 *
	 * @returns the promise of the data
     */
	function getMeOverview() {
		return $q(function(resolve, reject) {
			restService.wimTvRestAuthGET(
				'/api/user/me/overview',
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		});
	}

	/**
	 * Gets overview info of a passed user
	 * @param userId: the user to get
	 * @returns the promise of the data
	 */
	function getUser(userId) {
		return $q(function(resolve, reject) {
			restService.wimTvRestAuthGET(
				'/api/public/user/' + userId + '/overview',
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				});
		});
	}

	/**
	 * Simple but effective method of updating the user
	 * @param profile to update with updated fields
	 * @returns the promise of the just updated user
	 */
	function updateProfile(profile) {
		return $q(function (resolve, reject) {
			restService.wimTvRestAuthPOST(
				'/api/user/me',
				profile,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				}
			);
		})
	}


	/**
	 * Activates the user's email
	 * @param token: activation Token
	 * @returns a promise
	 */
	function activateEmail(token) {
		return $q(function(resolve, reject) {
			restService.wimTvRestGET(
				"/api/public/user/activate?token=" + token,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				}
			);
		});
	}


	/**
	 * change logged user's password.
	 * @param user: info and updated password
	 * @returns a promise with the just updated user
	 */
	function changePassword(user) {
		return $q(function (resolve, reject) {
			restService.wimTvRestAuthPOST(
				'/api/user/me/password/update',
				user,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				}
			);
		})
	}

	/**
	 * Activates a license checking for the succeded payment
	 *
	 * @param trackingId payment tracking id
	 * @param packageName package name
	 * @param success success callback
     * @param failure failure callback
	 */
	function activateLicense(trackingId,packageName,success,failure) {
		console.log("activateLicense: " + trackingId + " " + packageName);
		var data = {
			trackingId: trackingId
		};
		restService.wimTvRestAuthPOST(
			"/api/license/" + packageName +"/activate",
			data,
			success,
			failure
		);
	}


	/**
	 * Downgrades logged user to free license.
	 * @param success
	 * @param failure
	 */
	function downgrade(success,failure) {
		restService.wimTvRestAuthPOST(
			"/api/license/downgrade",
			null,
			success,
			failure
		);
	}

	/**
	 * curl 'http://www.wim.tv:8080/api/license/Professional/subscribe' -i -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -H 'Accept-Language: it' -H 'X-Wimtv-timezone: 3600000' -d '{
	 * "embedded" : false,
	 * "mobile" : false,
	 * "returnUrl" : "http://www.wim.tv/license/accepted",
	 * "cancelUrl" : "http://www.wim.tv/license/rejected"
	 *
	 * @param packageName package name
	 * @param options
	 * @param resolve callback
     * @param reject callback
	 *
     */
	function requestPacketPay(packageName,options,resolve, reject) {
		restService.wimTvRestPOST(
			"/api/license/" + packageName + "/subscribe",
			options,
			function (response) {
				config.setPacketPaymentData(response.data.trackingId,packageName);		// save the trackingId
				// window.location = response.data.url;				// redirects to paypal payments: delegated to invoker
				resolve(response);
			},
			function (response) {
				reject(response);
			});
	}


	/**
	 * Search for user
     */
	function search(filters) {
		return $q(function (resolve, reject) {
			if (!filters.pageSize) {
				filters.pageSize = 10;
			}
			if (!filters.pageIndex) {
				filters.pageIndex = 0;
			}
			restService.wimTvRestAuthPOST(
				'/api/public/search/users',
				filters,
				function (response) {
					resolve(response);
				},
				function (response) {
					reject(response);
				}
			);
		});
	}

}]);