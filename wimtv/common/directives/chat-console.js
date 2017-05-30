angular.module('wimtvApp').directive('chatConsole', function() {
	return {
		restrict: "E",
		replace: true,
		templateUrl: 'common/directives/chat-console.html',
		link: function(scope, element, attrs, ctrl) {
			scope.messageList = angular.element(element).find('.message-list');
		},
		controller: ['$scope', '$rootScope', '$timeout', 'chatService', function($scope, $rootScope, $timeout, chatService) {
			$scope.messageContent = '';
			$scope.messages = [];
			$scope.isKeyboardEnabled = true;
			var sender;
			if (!$rootScope.loggedUser) {
				//sender = "public"
				$scope.isKeyboardEnabled = false;
			} else {
				sender = $rootScope.loggedUser.username;
			}
			// Subscribe to channel where channel name is vodId of related video
			var channelChatId = $rootScope.chatVodId;

			chatService.history(channelChatId,
				function(status,response) {
					$scope.messages = response.messages.map(function(m) {
						return m.entry;
					});
					updateScroll(50);
					updateScroll(100);
				}
			);

			chatService.subscribe($scope, channelChatId ,
				function (ngEvent, m) {
					$scope.$apply(function () {
						$scope.messages.push(m.message);
						$scope.messageList.scrollTop($scope.messageList[0].scrollHeight+25);
						updateScroll(120);
					});
				}
			);

			function updateScroll(timeout) {
				$timeout(function () {
					$scope.messageList.scrollTop($scope.messageList[0].scrollHeight);
				}, timeout);
			}



			// Send Message
			$scope.sendMessage = function() {
				chatService.sendMessage($scope.messageContent, $rootScope.chatVodId, sender, success);
				function success(status) {
					$scope.messageContent = '';
				}
			};

			$scope.isAuthor = function (userCode) {
				var split = window.location.href.split("/");
				if(split && split.length > 5 && split[5] === userCode) {
					return "author";
				}
				return "";
			}
		}]
	};
});