angular.module("wimtvApp").directive("bridgetAsset", ['$timeout', 'config', function ($timeout, config) {
	return {
		restrict: 'E',
		scope: {
			type: '@',
			asset: '=',
			editable: '@',
			addTo: '@',
			clickAction: '&',
			editCall: '=',
			deleteCall: '='
		},
		link: function (scope, element, attribute) {
			var originalAsset = null;

			scope.edit = function () {
				originalAsset = angular.copy(scope.asset);
				scope.asset.edit = true;
			};

			scope.confirmEdit = function () {
				if (!scope.asset.title || scope.asset.title == "") {
					config.error('lang.wimbridget.asset.EDIT.TITLE-ERROR');
					return;
				}
				scope.asset.edit = false;
				scope.editCall(scope.asset);
			};

			scope.remove = function () {
				scope.deleteCall(scope.asset);
			};

			scope.undoEdit = function () {
				scope.asset.edit = false;
				if(originalAsset) {
					$timeout(function () {
						scope.asset = originalAsset;
					}, 1);
				}
			};

			scope.click = click;

			function click(asset) {
				scope.clickAction({bridgetItem: asset});
			}
		},
		templateUrl: wimtv_plugin_path + 'common/directives/bridget-asset.html'
	}
}]);
