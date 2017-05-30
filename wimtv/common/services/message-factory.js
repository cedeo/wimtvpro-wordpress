angular.module('wimtvApp')
    .factory('MessageService', ['$rootScope','Pubnub', '$pubnubChannel',
        function MessageServiceFactory($rootScope,Pubnub, $pubnubChannel) {
            // We create an extended $pubnubChannel channel object that add an additional sendMessage method
            // that publish a message with a predefined structure.
            var Channel = $pubnubChannel.$extend({
                sendMessage: function(messageContent) {
                    var currentUser = $rootScope.loggedUser;
                    return this.$publish({
                        uuid: (Date.now() + currentUser.username), // Add a uuid for each message sent to keep track of each message sent.
                        content: messageContent,
                        sender_uuid: currentUser.username,
                        date: Date.now()
                    })
                }
            });

            return Channel('messages-channel', {
                autoload: 20,
                presence: true
            });
        }
    ]);