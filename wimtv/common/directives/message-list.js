angular.module('wimtvApp').directive('messageList', function() {
    return {
        restrict: "E",
        replace: true,
        templateUrl: wimtv_plugin_path + 'common/directives/message-list.html',
        link: function(scope, element, attrs, ctrl) {
            var element = angular.element(element)
            var init = function() {};
            init();
        },
        controller: function($chatService) {
            $chatService.sendMessage();
        }
    };
});
