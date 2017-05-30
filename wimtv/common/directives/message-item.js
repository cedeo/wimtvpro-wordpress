angular.module('wimtvApp').directive('messageItem', function() {
    return {
        restrict: "E",
        templateUrl: wimtv_plugin_path + 'common/directives/message-item.html',
        scope: {
            senderUuid: "@",
            content: "@",
            date: "@"
        }
    }
});