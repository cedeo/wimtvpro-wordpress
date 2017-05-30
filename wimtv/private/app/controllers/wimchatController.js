angular.module("wimtvApp").config(function ($stateProvider) {
    $stateProvider.state('stateWimChat', {
        url: "/chat_console/{channelId}",
        templateUrl: wimtv_plugin_path + "private/app/partials/stateChat.html",
        controller: "wimchatController",
        controllerAs: "vm"
    })
});

angular.module("wimtvApp").controller('wimchatController', ['$scope','$rootScope', '$stateParams','chatService', function($scope,$rootScope,$stateParams,chatService) {

    $scope.isChatEnabled = false;


    function init() {
        $rootScope.chatVodId = $stateParams.channelId;
        var chatId = $rootScope.chatVodId;
        var user = $rootScope.loggedUser.username;
        chatService.isChatEnabled(chatId,
        function(isEnabled) {
            $scope.isChatEnabled = isEnabled;
        });

    }

    init();

    // enableChat(userId,contentId,isEnabled)
    $scope.toggleEnableChat = function() {
        var isChatEnabledRequest = !$scope.isChatEnabled;
        var chatId = $rootScope.chatVodId;
        var user = $rootScope.loggedUser.username;
        chatService.enableChat(user,chatId,isChatEnabledRequest).then(
          function(response) {
              var data = response.data;
              if (data) {
                  var d = data.data;
                  if (d) {
                      var propertyValues = d.propertyValues;
                      if (propertyValues) {
                          var newValue = response.data.data.propertyValues.enabled;
                          $scope.isChatEnabled = newValue;
                          return;
                      }
                  }
              }
              $scope.isChatEnabled = false;

          }
        );
    }
}]);

