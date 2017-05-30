angular.module('wimtvApp').directive('messageForm', function() {
    return {
        restrict: "E",
        replace: true,
        templateUrl: wimtv_plugin_path + 'common/directives/message-form.html',
        scope: {
            channel : '=channel',
        },
        controller: function($scope, $rootScope) {
            $scope.uuid = $rootScope.loggedUser;
            $scope.messageContent = '';

            $scope.sendMessage = function() {
              
            }
        }
    };
});