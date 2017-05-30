angular.module("wimtvApp").directive("profileThumbnail", ['commonService', 'config', '$timeout', function (commonService, config, $timeout) {
	return {
		restrict: 'E',
		scope: {
			object: "=object",
			editable: "=editable"
		},
		templateUrl: wimtv_plugin_path + 'common/directives/profile-thumbnail.html',
		link: function (scope, element, attributes) {
			scope.changeThumbnail = changeCastThumbnail;
			scope.getThumbnail = config.getThumbnailUrl;
			scope.defaultThumbnail = config.defaultThumbnail;

			function changeCastThumbnail(file, errFiles, object) {
				scope.updatingThumbnail= true;
				commonService.uploadImage(file, errFiles, object, success, error, uploading);
				function success(response) {
					object.thumbnailId = response.data.thumbnailId;
					$timeout(hideUpdating, 1500);
				}
				function uploading(response) {
					scope.updatingThumbnail = true;
					$timeout(hideUpdating, 2500);
				}
				function error(response) {
					scope.updatingThumbnailError = true;
					config.error('lang.userprofile.CHANGEIMAGE.ERROR', response);
				}
				function hideUpdating() {
					scope.updatingThumbnail = false;
				}
			}
		}
	}
}]);