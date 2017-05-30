angular.module('wimtvApp').directive('userAvatar', function() {
    return {
        restrict: "E",
        transclude: true,
        template: "< img src = '{{avatarUrl}}'  class = 'circle' ng-transclude >",
        scope: {
            uuid: "@",
        },
    controller: ['$scope', '$rootScope', function($rootScope) {
        // Generating a uniq avatar for the given uniq string provided using robohash.org service
        $scope.avatarUrl = '//robohash.org/' + $rootScope.loggedUser + '?set=set2&bgset=bg2&size=70x70';
    }]
};
});