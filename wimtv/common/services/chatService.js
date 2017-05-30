angular.module("wimtvApp").factory('chatService', ['Pubnub', 'userCustomizationService', 'config',
    function(Pubnub, userCustomizationService, config) {

    /**
     * Service interface
     */
    return {
        sendMessage: sendMessage,
        subscribe: subscribe,
        history: history,
        enableChat : enableChat,
        isChatEnabled : isChatEnabled
    };

    function history(channelId,callback) {
        Pubnub.history({
            channel : channelId,
            count : 100, // 100 is the default
            reverse : false // false is the default
        },callback);
    }

    function subscribe(scope, channelId, eventListener) {
        Pubnub.subscribe({
            channels  : [channelId],
            withPresence: true,
            triggerEvents: ['message', 'presence', 'status']
        });
        scope.$on(Pubnub.getMessageEventNameFor(channelId), eventListener);
    }


    function sendMessage(messageContent, channel, senderUUID, successCallback) {
        var thumbnailId = config.getThumbnailId();
        var pageTitle = config.getPageTitle();
        if (!messageContent || messageContent === '') {
            return;
        }
        Pubnub.publish(
            {
                channel: channel,
                message: {
                    thumbnailId : thumbnailId,
                    pageTitle: pageTitle,
                    content: messageContent,
                    sender_uuid: senderUUID,
                    date: new Date()
                }
            },
            function (status, response) {
                console.log("sendMessage status:" + JSON.stringify(status));
                if (status.error) {
                    // handle error
                } else {
                    if(successCallback) {
                        successCallback(status);
                    }
                }
            }
        );
    }


    function enableChat(userId,chatId,isEnabled) {
        var data = { "enabled" :  isEnabled };
        return userCustomizationService.setProperty(userId,chatId,'chat',data);
    }

    function isChatEnabled(contentId,callBackResponse) {
        var json = userCustomizationService.getProperty(contentId).then(function(response) {
            if(response) {
                var json = response.data;
                if (json) {
                    var datas = json.data;
                    if (datas) {
                        if (datas.length > 0) {
                            for (var i = 0; i < datas.length; i++) {
                                var d = datas[i];
                                if (d.propertyKey == "chat") {
                                    var props = d.propertyValues;
                                    var isEnabled = props.enabled;
                                    callBackResponse(isEnabled);
                                    return;
                                }
                            }
                            callBackResponse(true);
                            return;
                        }
                    }
                }
            }
            callBackResponse(false);
        });

    }


}]);