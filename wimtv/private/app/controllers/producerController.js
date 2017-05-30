angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateProducer', {
		url: "/producer?hostId?url",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateProducer.html",
		controller: "producerController",
		controllerAs: "vm",
		params: {
			hostId: null,
			url: null
		}
	})});

angular.module("wimtvApp").controller('producerController',
    ['$scope','$rootScope','$location', 'liveService', 'userService',
        function ($scope,$rootScope, $location, liveService, userService) {

	var vm = this;
    vm.showChat = false;
	vm.startWebProducer = startWebProducer;
	vm.preview = preview;

    function setChatChannel() {
        var hostId = $location.search().hostId;
        liveService.privateGet(hostId,
            function(response) {
                $rootScope.chatVodId = response.data.eventId;
                vm.showChat = true;
            },
            function(failure) {
				// TODO: handle
            });
    }
    setChatChannel();

	function startWebProducer() {
		var hostId = $location.search().hostId;

		$scope.liveParam = {
			hostId: hostId,
			src: 'http://peer.wim.tv:38080/wimtv-webapp/rest/liveStreamEmbed/' + $location.search().hostId + '/player?&insecureMode=OFF&autostart=false'
		};
		liveService.privateGet(hostId, success, failure);
		function success(response) {

			// $scope.channelUrl = response.data.channel.url;
			// $scope.path = response.data.channel.streamPath;
			var channelUrl = response.data.channel.streamingBaseUrl + "/" + response.data.channel.streamPath;
			LiveProducer.startWebProducer($scope.liveParam.hostId, function() {
				// service call
				userService.getProfile().then(success).catch(failure);

				function success(response) {
					LiveProducer.startProd (response.data.userCode, channelUrl, response.data.features.livePassword);
				}

				function failure(response) {
					config.error('lang.wimlive.PRODUCER.ERROR', response);
				}
			});

		}
		function failure(response) {
			config.error('lang.wimlive.PRODUCER.ERROR', response);
		}
	}

	function preview() {
		vm.showPreview = !vm.showPreview;
		if(vm.showPreview) {
			var channelId = $location.search().channelId;
			liveService.play(channelId);
		}
	}
}]);