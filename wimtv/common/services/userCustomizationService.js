angular.module("wimtvApp").factory('userCustomizationService', ['genericRestService', 'config', function (genericRestService, config) {

	// DEV: peer.wim.tv:9083
	// PROD: usercustomization.wim.tv:8080

	var baseUrl = config.isDebugMode(window.location.hostname) ? "https://peer.wim.tv:8443/user-customization" : "https://new.wim.tv/user-customization";

	return {
		getUser: function (userId) {
			var url = baseUrl + '/rest/property/user/' + userId;
			return genericRestService.GET(url);
		},
		
		setProperty: function (userId, objectId, propertyKey, propertyValues) {
			var url = baseUrl + '/rest/property';
			var data = {
				user: userId,
				objectId: objectId,
				propertyKey: propertyKey,
				propertyValues: propertyValues
			};
			return genericRestService.POST(url, data);
		},

        getProperty: function(objectId) {
            var url = baseUrl + "/rest/property/object/" + objectId;
            var response = genericRestService.GET(url);
            return response;
        }
	}

}]);