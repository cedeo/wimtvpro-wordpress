var EndPointConfig = (function () {

	/**
	 * Configuration of all deployment.
	 */

	var PRODUCTION_HOSTNAME = "https://new.wim.tv";

	var PEER_HOSTNAME = "https://new.wim.tv";




		// Production. You don't say?
	var PRODUCTION_ENDPOINTS = {
		hostname: 				PRODUCTION_HOSTNAME,
		wimtvServer: 			PRODUCTION_HOSTNAME + "/wimtv-server",
		userCustomization: 		PRODUCTION_HOSTNAME + "/user-customization",
		vsa: 					PRODUCTION_HOSTNAME + "/vsa"
	};
		// Peer aka Test/Development
	var PEER_ENDPOINTS = {
		hostname: 				PEER_HOSTNAME,
		wimtvServer: 			PEER_HOSTNAME + "/wimtv-server",
		userCustomization: 		PEER_HOSTNAME + "/user-customization",
		vsa: 					PEER_HOSTNAME + "/vsa"
	};

	/**
	 * Logical code: do not touch. This shouldn't be never touched. Thanks.
	 */
	var data;
	function init() {
		// builds all data
		data = {};
		getIsDebug();

		generateEndPoints();
	}

	function getIsDebug() {
		data.isDebug = false;
	}

	function generateEndPoints() {
		generateHostname();
		generateWimtvServer();
		generateUserCustomization();
		generateVsa();
	}

	function generateHostname() {
		data.hostname = data.isDebug ? PEER_ENDPOINTS.hostname: PRODUCTION_ENDPOINTS.hostname;
	}

	function generateWimtvServer() {
		data.wimtvServer = data.isDebug ? PEER_ENDPOINTS.wimtvServer : PRODUCTION_ENDPOINTS.wimtvServer;
	}

	function generateUserCustomization() {
		data.userCustomization = data.isDebug ? PEER_ENDPOINTS.userCustomization : PRODUCTION_ENDPOINTS.userCustomization;
	}
	
	function generateVsa() {
		data.vsa = data.isDebug ? PEER_ENDPOINTS.vsa : PRODUCTION_ENDPOINTS.vsa;
	}

	return function () {
		if(!data) {
			init();
		}
		return data;
	}
}());