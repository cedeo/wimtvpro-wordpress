angular.module("wimtvApp").factory('commonService', ['restService', '$rootScope', '$timeout', 'Upload', 'config', function(restService, $rootScope, $timeout, Upload, config) {

	return {
		uploadImage: uploadImage
	};

	function uploadImage (file, errFiles, object, callback, errorCallback, uploadingCallback) {
		var headers = {
			'Accept': 'application/json',
			'X-Wimtv-timezone': (new Date().getTimezoneOffset() * 60 * 1000)
		};
		headers['Authorization'] = "Bearer " + config.tokens.private;

		if (file) {
			file.upload = Upload.upload({
				url: $rootScope.wimtvUrl + '/api/thumbnail',
				data: {thumbnail: file},
				headers: headers
			});

			file.upload.then(success, failure, progressing);
			function success(response) {
				if(response.data && response.data.thumbnailId) {
					object.thumbnailId = response.data.thumbnailId;
					if(callback) {
						callback(response);
					}
				} else {
					failure(response);
				}
			}
			function failure(response) {
				errorCallback(response);
			}
			function progressing (response) {
				if(uploadingCallback) {
					uploadingCallback(response);
				}
			}
		}
	}





}]);