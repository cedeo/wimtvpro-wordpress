angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimcast', {
		url: "/wimcast?createNew",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimcast.html",
		controller: "wimcastController as vm"
	})
});
angular.module("wimtvApp").controller("wimcastController", ['$scope', '$timeout', '$stateParams', 'castChannelService', 'commonService', 'config', function ($scope, $timeout, $stateParams, castChannelService, commonService, config) {

	var vm = this;
	vm.saveChannel = saveChannel;
	vm.getCastChannels = getCastChannels;
	vm.getStreamPath = getStreamPath;
	vm.delete = deleteCastChannel;
	vm.editing = editing;
	vm.edit = edit;
	init();


	function init() {
		console.log("init");
		vm.channelPage = 1;
		vm.newCast = {};
		if($stateParams.createNew) {
			vm.createNew = true;
		}

		getCastChannels();
	}

	/**
	 * save new Channel
	 */
	function saveChannel() {
		castChannelService.create(vm.newCast, success, failure);

		function success(response) {
			vm.createNew = false;
			vm.newCast = {};
			getCastChannels();
			config.success("lang.wimcast.CAST-NEW-SUCCESS");
		}
		function failure(response) {
			config.error("lang.wimcast.CAST-NEW-ERROR", response);
		}
	}

	/**
	 * Deletes a channel
	 * @param castChannel
	 */
	function deleteCastChannel(castChannel) {
		castChannelService.remove(castChannel.channelId, success, failure);

		function success(response) {
			getCastChannels();
			config.success("lang.wimcast.CAST-DELETE-SUCCESS");
		}
		function failure(response) {
			config.error("lang.wimcast.CAST-DELETE-ERROR", response);
		}
	}

	/**
	 * Handles the editing form
	 */
	function editing(cast, editing) {
		cast.editing = editing ? angular.copy(cast) : null;
	}

	/**
	 * Edits a channel
	 */
	function edit(cast) {
		castChannelService.update(cast.editing, success, failure);

		function success(response) {
			getCastChannels();
			config.success("lang.wimcast.MONTHLY-SUCCESS");
		}

		function failure(response) {
			config.error("lang.wimcast.MONTHLY-ERROR", response);
		}

	}

	/**
	 * Generates a new streamPath
	 * @param castChannel the origin's channel
	 */
	function getStreamPath(castChannel) {
		castChannelService.generateStreamPath(castChannel.name, success, failure);

		function success(response) {
			vm.newCast.streamPath = response.data.streamPath;
		}
		function failure(response) {
			config.error("lang.wimchannel.MESSAGES.UNABLE-TO-FETCH-STREAM-URL", response);
		}
	}

	/**
	 * Main search function
	 */
	function getCastChannels() {
		var filters = {
			queryString: vm.querySearchString,
			pageIndex: vm.channelPage-1,
			pageSize: 10
		};
		castChannelService.privateSearch(filters, success, failure);

		function success(response) {
			vm.channels = response.data.items;
			vm.totalChannels = response.data.totalItems;
		}
		function failure(response) {
			config.error("lang.wimcast.CASTS-ERROR", response);
		}
	}

}]);
