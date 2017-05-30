angular.module("wimtvApp").directive("validator", function () {
	return {
		restrict: 'E',
		scope: {
			element: '=element'
		},
		template: '<div ng-if="element.$dirty" ng-messages="element.$error" class="form-error"><span ng-messages-include="form-validator" ></span></div>'
	}
});

angular.module("wimtvApp").directive("match", ['$parse', function ($parse) {
	var directive = {
		link: link,
		restrict: 'A',
		require: '?ngModel'
	};
	return directive;
	function link(scope, elem, attrs, ctrl) {
// if ngModel is not defined, we don't need to do anything
		if (!ctrl) return;
		if (!attrs["match"]) return;

		var first = $parse(attrs["match"]);

		var validator = function (value) {
			var temp = first(scope),
				v = value === temp;
			ctrl.$setValidity('match', v);
			return value;
		};

		ctrl.$parsers.unshift(validator);
		ctrl.$formatters.push(validator);
		attrs.$observe("match", function () {
			validator(ctrl.$viewValue);
		});

	}

}]);
