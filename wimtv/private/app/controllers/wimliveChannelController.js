angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimliveListChannel', {
		url: "/wimliveListChannel",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimchannel.html",
		controller: "wimliveChannelController",
		controllerAs: "vm"
	})

});
angular.module("wimtvApp").controller('wimliveModalController', ['$scope', '$filter', '$uibModalInstance', 'commonService', 'config', 'object', function ($scope, $filter, $uibModalInstance, commonService, config, object) {
	var vm = this;

	if (object.eventDate) {
		var d = object.eventDate.date.split('/');
		object.dateStart = new Date(d[2], d[1]-1, d[0]);
	}
	if (object.endDate) {
		d = object.endDate.date.split('/');
		object.dateEnd = new Date(d[2], d[1]-1, d[0]);
	}


	vm.timezones = config.timezones;
	vm.confirm = confirm;
	vm.close = close;
	vm.updateLive = updateLive;
	vm.changeImage = changeImage;
	vm.disabledEditLive = disabledEditLive;
	vm.live = object;

	function disabledEditLive(live) {
		var disabled = !(
		live &&
		live.name &&
		live.dateEnd &&
		live.dateStart &&
		live.endDate &&
		live.endDate.time &&
		live.eventDate &&
		live.eventDate.time);
		// TODO: add check in edit
		// disabled = disabled || ((vm.missingPaypal || vm.missingBillingInfo) && live && live.paymentMode==='PAY_PER_VIEW');
		return disabled;
	}


	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}
	function updateLive(live) {
		// update date
		confirm(live);
	}
	function changeImage (file, errFiles, object) {
		commonService.uploadImage(file, errFiles, object);
	}
}]);

angular.module("wimtvApp").controller('iframeModalController', ['$scope', '$uibModalInstance', 'config', 'channel', function ($scope, $uibModalInstance, config, channel) {
	var vm = this;

	vm.channel = channel;
	vm.close = close;

	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}
}]);

angular.module("wimtvApp").controller('wimliveChannelController', ['$scope', 'restService', '$timeout', '$location', '$filter', '$uibModal', 'channelService', 'liveService', 'commonService', 'userService', 'config', function ($scope, restService, $timeout, $location, $filter, $uibModal, channelService, liveService, commonService, userService, config) {
	var vm = this;
	vm.channels = [];
	vm.errorMessages = [];
	vm.successMessages = [];
	vm.newChannel = {};
	vm.changeImage = changeImage;
	vm.editChannel = editChannel;
	vm.createChannel = createChannel;
	vm.openDeleteChannel = openDeleteChannel;
	vm.getStreamingUrl = getNewUrl;
	vm.showEventList = showEventList;
	vm.createLive = createLive;
	vm.openDate = openDate;
	vm.openIframeDetail = openIframeDetail;
	vm.openUpdateLive = openUpdateLive;
	vm.openDeleteLive = openDeleteLive;
	vm.getChannels = getChannels;
	vm.disabledNewLive = disabledNewLive;
	vm.togglePastEvents = togglePastEvents;
	vm.isPastEvent = isPastEvent;
	vm.querySearchString = "";
	init();
	/**
	 * Listener on event "paypalUpdated" triggered when the user updates the paypal email address located in the top of the privatePage.html ('monetisation')
	 */
	$scope.$on('paypalUpdated',function (event, data) {
		checkPaypalAndFinance();
	});

	function init() {
		// show newLiveCollapse from URL
		vm.isCreateChannelFromVisible = ($location.search()['newchannel']);

		vm.newChannelImage = null;
		vm.newChannelImagePath = null;
		vm.videosLoaded = false;

		checkPaypalAndFinance();
		getChannels();
		checkUserLivePassword();
	}

	function checkUserLivePassword() {
		userService.getProfile().then(success).catch(failure);

		function success(response) {
			if (!response.data.features || !response.data.features.livePassword) {
				config.warning("lang.userprofile.MESSAGES.MISSING-LIVEPASSWORD");
				vm.disableLiveCreating = true;
			} else {
				vm.disableLiveCreating = false;
			}
		}
		function failure(response) {
			config.warning("lang.userprofile.ERROR.GET", response);
		}

	}

	function disabledNewLive(newLive) {
		var disabled = !(
		newLive &&
		newLive.name &&
		newLive.dateEnd &&
		newLive.dateStart &&
		newLive.endDate &&
		newLive.endDate.time &&
		newLive.eventDate &&
		newLive.eventDate.time &&
		newLive.haveRights);
		disabled = disabled || ((vm.missingPaypal || vm.missingBillingInfo) && newLive && newLive.paymentMode==='PAY_PER_VIEW');
		return disabled;
	}


	function checkPaypalAndFinance() {
		config.checkPaypalAndFinance(userService, callback);
		function callback(response) {
			vm.missingPaypal = response.paypal;
			vm.missingBillingInfo = response.billing;
		}
	}


	function openDeleteLive(live) {
		var modal = $uibModal.open({
			templateUrl: 'removeModal.html',
			controller: 'wimliveModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return live;
				}
			}
		});

		modal.result.then(function (data) {
			liveService.remove(data.eventId, success, failure);
			function success(response) {
				if(response.status >= 200 && response.status < 300) {
					config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-DELETE-LIVE");
				} else {
					config.error("lang.wimchannel.MESSAGES.FAILURE-DELETE-LIVE", response);
				}
				getChannels();
			}
			function failure(response) {
				config.error("lang.wimchannel.MESSAGES.FAILURE-DELETE-LIVE", response);
			}
		});
	}

	function openUpdateLive(live) {
		var modal = $uibModal.open({
			templateUrl: 'updateLive.html',
			controller: 'wimliveModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return live;
				}
			}
		});

		modal.rendered.then(function () {
			$('.clockpicker').clockpicker();
		});
		modal.result.then(function (live) {
			live = fixLiveData(live);

			liveService.update(live, success, failure);
			function success(response) {
				getChannels();
				config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-EDIT-LIVE");
			}
			function failure(response) {
				config.error("lang.wimchannel.MESSAGES.FAILURE-EDIT-LIVE", response);
			}
		});
	}

	function openIframeDetail(channel, element) {

		var modal = $uibModal.open({
			templateUrl: 'iframeModal.html',
			controller: 'iframeModalController',
			controllerAs: 'vm',
			resolve: {
				channel: function () {
					return channel;
				}
			}
		});
		modal.rendered.then(function () {
			var target = document.getElementById('embed-code');
			target.innerHTML = config.getIframe('live', channel.channelId);
			target.focus();
			target.select();
		})
	}

	function getChannels(newChannelToOpen) {
		var pagination = {
			pageIndex: vm.pageIndex - 1,
			pageSize: 10,
			queryString: vm.querySearchString
		};
		channelService.privateSearch(pagination,
			function (response) {
				vm.channels = response.data.items;
				vm.videosLoaded = true;
				if (newChannelToOpen) {
					openNewlyCreatedChannel(newChannelToOpen);
				}
				for(var k in vm.channels) {
					if(vm.channels.hasOwnProperty(k)) {
						showEventList(vm.channels[k]);
					}
				}
			},
			function (response) {
				config.error("lang.wimchannel.MESSAGES.FAILURE-GET-CHANNELS", response);
			}
		);
	}

	function createChannel() {
		vm.videosLoaded = false;
		vm.channels = null;
		channelService.add(vm.newChannel, success, failure);
		function success(response) {
			$timeout(function () {
				vm.isCreateChannelFromVisible = false;
				config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-ADD-CHANNEL");
				vm.newChannel = {};
				getChannels(response.data);
			}, 500);
		}
		function failure(response) {
			config.error("lang.wimchannel.MESSAGES.FAILURE-ADD-CHANNEL", response);
			vm.isCreateChannelFromVisible = false;
		}
	}

	function openNewlyCreatedChannel(channel) {
		for (var k in vm.channels) {
			if(vm.channels.hasOwnProperty(k)) {
				var c = vm.channels[k];
				if(c.channelId === channel.channelId) {
					c.showList = true;
				}
			}
		}
	}

	function openDeleteChannel(channel) {
		var modal = $uibModal.open({
			templateUrl: 'removeModal.html',
			controller: 'wimliveModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return channel;
				}
			}
		});

		modal.result.then(function (data) {
			channelService.remove(data.channelId, success, failure);
			function success(response) {
				if(response.status >= 200 && response.status < 300) {
					config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-DELETE-CHANNEL");
				} else {
					config.error("lang.wimchannel.MESSAGES.FAILURE-DELETE-CHANNEL", response);
				}
				getChannels();
			}
			function failure(response) {
				var existLiveEvents = (response.data.exception.indexOf("DataIntegrityViolationException") !== -1);
				if (existLiveEvents == true) {
					config.error("lang.wimchannel.MESSAGES.FAILURE-DELETE-CHANNEL-EXIST-LIVE");
				} else {
					config.error("lang.wimchannel.MESSAGES.FAILURE-DELETE-CHANNEL", response);
				}
			}
		})
	}

	function editChannel(channel) {
		channelService.update(channel, success, failure);

		function success(response) {
			config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-EDIT-CHANNEL");
			getChannels();
		}
		function failure(response) {
			vm.isCreateChannelFromVisible = false;
			config.success("lang.wimchannel.MESSAGES.FAILURE-EDIT-CHANNEL", response);
		}
	}

	function showEventList(channel, pastIncluded) {
		liveService.getLiveEventsByChannelId(channel.channelId, pastIncluded, success, failure);

		function success(response) {
			channel.eventList = response.data.items;
		}
		function failure(response) {
			config.error("lang.wimchannel.MESSAGES.FAILURE-GET-LIVE-LIST", response);
		}
	}

	function togglePastEvents(channel) {
		channel.showPastEvents = !channel.showPastEvents
		showEventList(channel, channel.showPastEvents);
	}

	function isPastEvent(live) {
		var date = moment(live.endDate.date + "-" + live.endDate.time, "DD/MM/YYYY-HH:mm:ss");
		return moment().isAfter(date);
	}

	function fixLiveData(live) {
		live.eventDate.date = $filter('date')(live.dateStart, 'dd/MM/yyyy');
		live.endDate.date = $filter('date')(live.dateEnd, 'dd/MM/yyyy');
		if(live.eventDate.time.length < 7) {
			live.eventDate.time += ":00";
		}
		if(live.endDate.time.length < 7) {
			live.endDate.time += ":00";
		}
		if(!live.paymentMode) {
			live.paymentMode = 'FREE';
		}
		if(live.paymentMode === 'FREE') {
			delete live.pricePerView;
		}
		return live;
	}

	function createLive(channel) {
		var live = channel.newLive;
		live = fixLiveData(live);

		liveService.add(channel.channelId, live, success, failure);

		function success(response) {
			config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-ADD-LIVE");
			channel.createEvent = false;
			channel.newLive = {};
			$timeout(function () {
				showEventList(channel);
			}, 500);
		}
		function failure(response) {
			config.error("lang.wimchannel.MESSAGES.FAILURE-ADD-LIVE", response);
		}
	}

	function getNewUrl(channel) {
		channelService.getChannelName(channel.name, success, failure);
		function success(response) {
			channel.streamPath = response.data.streamPath;
		}
		function failure(response) {
			config.error("lang.wimchannel.MESSAGES.UNABLE-TO-FETCH-STREAM-URL", response);
		}
	}

	function changeImage (file, errFiles, object) {
		commonService.uploadImage(file, errFiles, object);
	}

	function openDate(channel) {
		channel.newLive = channel.newLive? channel.newLive : {};
		channel.newLive.openedStartDate = true;
	}
}]);
