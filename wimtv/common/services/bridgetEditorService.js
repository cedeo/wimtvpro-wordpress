angular.module("wimtvApp").factory('bridgetEditorService', ['bridgetRestService', function(bridgetRestService) {
	/**
	 * Service Interface: Bridget -action- service
	 * Management of the bridget.
	 */
	return {
		bridgetCreate: bridgetCreate,
		bridgetRead: bridgetRead,
		bridgetPatch: bridgetPatch,
		bridgetDelete: bridgetDelete,
		bridgetList: bridgetList
	};

	/**
	 * Methods implementation
	 */
	function bridgetCreate(data, callbackSuccess, callbackError){
		var url = 'private/bridgets';
		bridgetRestService.customCall(
			url,
			'POST',
			undefined,
			data,
			null,
			callbackSuccess,
			callbackError
		);
	}

	function bridgetRead(bridgetId, callbackSuccess, callbackError) {
		var url = 'private/bridgets/' + bridgetId;
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}

	function bridgetPatch(data, callbackSuccess, callbackError) {
		var id = data.get("id");
		var url = 'private/bridgets/' + id;
		bridgetRestService.customCall(
			url,
			'PATCH',
			undefined,
			data,
			null,
			callbackSuccess,
			callbackError
		);
	}

	function bridgetDelete(bridgetId, callbackSuccess, callbackError) {
		var url = 'private/bridgets/' + bridgetId;
		bridgetRestService.DELETE(url, callbackSuccess, callbackError);
	}

	function bridgetList(videoId, callbackSuccess, callbackError){
		var url = 'private/programbridgets/'+ videoId;
		bridgetRestService.GET(url, callbackSuccess, callbackError);
	}
}]);
