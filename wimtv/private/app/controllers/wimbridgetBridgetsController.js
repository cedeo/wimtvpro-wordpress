angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimbridgetBridgets', {
		url: "/wimbridge/bridgeteditor/:videoId",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbridgetBridgets.html",
		controller: "wimbridgetBridgetsController as vm",
		params : {
					programId: null,
					title: null
				}
	})
});

angular.module("wimtvApp").controller("wimbridgetBridgetsModalController",
	['bridgetEditorService', '$scope', '$uibModalInstance', 'object', 'config',
	function(bridgetEditorService, $scope, $uibModalInstance, object, config){
		var vm = this;
		vm.confirm = confirm;
		vm.close = close;
		vm.bridgetDelete = bridgetDelete;
		vm.bridgetsDeleteAll = bridgetsDeleteAll;
		vm.bridget = object;

		function confirm(obj) {
			$uibModalInstance.close(obj);
		}

		function close(origin) {
			$uibModalInstance.dismiss(origin);
		}

		function bridgetDelete(bridget, close){
			bridgetEditorService.bridgetDelete(bridget._id, bridgetDeleteSuccess, bridgetDeleteError);

			// Callback success
			function bridgetDeleteSuccess(res){
				config.success('lang.wimbridget.bridget.DELETE.FINISH');
				if (close) vm.close();
			}

			// Callback error
			function bridgetDeleteError(err){
				config.error('lang.wimbridget.bridget.DELETE.ERROR');
			}
		}

		function bridgetsDeleteAll(){
			if (vm.bridget.length == 0) {
				vm.close();
				return;
			}
			var singleBridget = vm.bridget[vm.bridget.length-1];
			bridgetEditorService.bridgetDelete(singleBridget._id, bridgetDeleteSuccess, bridgetDeleteError);

			// Callback success
			function bridgetDeleteSuccess(res){
				vm.bridget.pop();
				config.success('lang.wimbridget.bridget.DELETE.FINISH');
				bridgetsDeleteAll();
			}

			// Callback error
			function bridgetDeleteError(err){
				config.error('lang.wimbridget.bridget.DELETE.ERROR');
			}
		}
	}]);

angular.module("wimtvApp").controller('wimbridgetBridgetsController', ['$scope', '$timeout', 'bridgetEditorService', 'config', '$stateParams', 'bridgetAssetService', '$interval', '$uibModal', function ($scope, $timeout, bridgetEditorService, config, $stateParams, bridgetAssetService, $interval, $uibModal) {
	var vm = this;
	vm.sourceContent = null;
	vm.loadedRangeSlider = false;
	vm.bridget = bridgetReset(); // new bridget
	vm.bridgets = [];
	vm.markers = [];
	vm.actualShot = 0;
	vm.bridgetLayouts = [{
			name: "Video",
			type: "video"
		}, {
			name: "Image",
			type: "image"
		}, {
			name: "Slide left",
			type: "slide_left"
		}, {
			name: "Slide right",
			type: "slide_right"
		}
	];

	vm.toggleAdd = toggleAdd;

	// creation player Methods
	vm.nextShot = nextShot;
	vm.prevShot = prevShot;
	vm.playBetween = playBetween;
	vm.userPlayer = "0";
	vm.previewLink = window.location.origin + '/#/bridget/'+vm.userPlayer+'?videoId=';

	// bridget editor Methods
	vm.bridgetCreate = bridgetCreate;
	vm.bridgetPatch = bridgetPatch;
	vm.bridgetList = bridgetList;
	vm.initEditing = initEditing;
	vm.setDestination = setDestination;
	vm.checkLayout = checkLayout;
	vm.videoRefreshing = false;
	vm.imageRefreshing = false;
	vm.imageList = imageList;
	vm.videoList = videoList;
	vm.openModal = openModal;

	// Event listener for span-time change
	vm.timeChange = timeChange;

	init();

	function init() {
		videoRead($stateParams.videoId);
		videoList();
		imageList();
		startRefreshing();
	}

	function startRefreshing(){
		$scope.refreshScore = function() {
			videoList();
			imageList();
		}
		var promise = $interval($scope.refreshScore, 6000);
		$scope.$on('$destroy',function(){
			if(promise)
			$interval.cancel(promise);
		});
	}

	// init videojs player
	$scope.$on('vjsVideoReady', function (e, data) {
		vm.player = data.player;
		vm.player.markers({
			markerStyle: {
				 'width':'2px',
				 'background-color': 'red'
			},
			markers: []
		});

		vm.player.on( 'loadedmetadata', function() {
			vm.player.currentTime(0);
			setShots();
			vm.player.rangeslider({hidden:false});
		});

		vm.player.one('playing', function () {
			vm.player.pause();
			vm.player.muted(false);
			vm.player.volume(0.5);
		});

		vm.player.on("loadedRangeSlider",function() {
			nextShot(true);
			vm.loadedRangeSlider = true;
		});

		vm.player.on('sliderchange', function () {
			var values = vm.player.getValueSlider();
			$timeout(function () {
				var start = Math.ceil(values.start * 100) / 100;
				var end = Math.ceil(values.end * 100) / 100;
				var duration = Math.ceil(vm.player.duration() * 100) / 100;
				if (vm.adding){
					if (start >= 0 && vm.bridget.newStart!== '') {vm.bridget.newStart = start};
					if (end <= duration && vm.bridget.newEnd!== '') {vm.bridget.newEnd = end};
				}
				vm.bridgets.forEach(function(bridget){
					if (bridget.editing){
						if (start != 0 && bridget.newStart !== '') bridget.newStart = start;
						if (end != duration && bridget.newEnd !== '') bridget.newEnd = end;
					}
				});
			}, 100);
		});
	});

	function toggleAdd() {
		vm.adding = !vm.adding;
		var values = vm.player.getValueSlider();
		var start = Math.ceil(values.start * 100) / 100;
		var end = Math.ceil(values.end * 100) / 100;
		vm.bridget.newStart = start;
		vm.bridget.newEnd = end;
	}

	function videoRead(videoId){
		bridgetAssetService.videoRead(videoId, videoReadSuccess, videoReadError);

		// Callback success
		function videoReadSuccess(res){
			vm.sourceContent = res.data;
			vm.previewLink = vm.previewLink + vm.sourceContent.id;
			setVjsMedia(vm.sourceContent);
			bridgetList(vm.sourceContent);
		}

		// Callback error
		function videoReadError(err){
		}
	}

	function videoList() {
		vm.videoRefreshing = true;
		bridgetAssetService.videoList(videoListCreate, videoListError);

		function videoListCreate(response) {
			vm.videoRefreshing = false;
			if (!angular.equals(response.data, vm.videos)){
				vm.videos = response.data;
			}
			vm.videosLoaded = true;
		}
		function videoListError(response) {
			vm.videoRefreshing = false;
			console.log('videoListError', response);
		}
	}

	function imageList(){
		vm.imageRefreshing = true;
		bridgetAssetService.imageList(imageListSuccess, imageListError);

		// Callback success
		function imageListSuccess(res){
			vm.imageRefreshing = false;
			if (!angular.equals(res.data, vm.images)){
				vm.images = res.data;
			}
			vm.imagesLoaded = true;
		}

		// Callback error
		function imageListError(err){
			console.log('imageListError', err);
			vm.imageRefreshing = false;
		}
	}
	/**
	 * Videojs Methods implementation
	 */
	function setVjsMedia(video){
		var wimbridgetFilesPath = config.getWimBridgetHost() + "public/";
		var mimetype = video.mimesubtype || "mp4";
		vm.vjsMedia = {
			sources: [{
				src: video.asset_url,
				type: 'video/'+ mimetype
			}],
			poster: config.getBridgetThumbnail(video)
		};
	}

	function setShots(){
		var shotDetector = vm.sourceContent.shots ? JSON.parse(vm.sourceContent.shots) : [];
		var shots = [];
		shotDetector.forEach(function(shot){
			var shotTime = (Number(shot[0]) / 1000);
			if (shotTime < vm.player.duration()){
				shots.push({
					time: shotTime,
					text: shotTime
				});
				vm.markers.push(shotTime);
			} else {
				console.error("Il tempo dato dallo shot detector supera la durata del video.");
			}
		});
		vm.markers.sort(function(a,b) {
			return a - b;
		});
		vm.player.markers.reset(shots);
	}

	function timeChange(start, end) {
		var duration = Math.ceil(vm.player.duration() * 100) / 100;
		if (start < 0) start = 0.01;
		if (start > duration) start = duration - 0.01;
		if (end < 0) end = 0.01;
		if (end > duration) end = duration - 0.01;
		vm.player.setValueSlider(start, end);
	}

	function nextShot(firstTime){
		if (!firstTime && (vm.player.duration() > vm.markers[vm.actualShot +1])){
			vm.actualShot = vm.actualShot + 1;
		}
		var inizio = vm.markers[vm.actualShot];
		var fine = vm.markers[vm.actualShot +1] || vm.player.duration();

		vm.player.setValueSlider(inizio, fine);
		vm.player.currentTime(inizio);
	}

	function prevShot(){
		if (0 <= vm.markers[vm.actualShot -1]){
			vm.actualShot = vm.actualShot - 1;
		}
		var inizio = vm.markers[vm.actualShot] || 0;
		var fine = vm.markers[vm.actualShot +1];

		vm.player.setValueSlider(inizio, fine);
		vm.player.currentTime(inizio);
	}

	function playBetween(){
		var values = vm.player.getValueSlider();
		vm.player.playBetween(values.start, values.end);
	}

	/**
	 * bridget editor Methods implementation
	*/

	function bridgetDecorator(bridget){
		var valueSlider = vm.player.getValueSlider();
		var decorated = {
			title: bridget.title,
			description: bridget.description,
			tags: bridget.tags,
			program: vm.sourceContent.id,
			start: valueSlider.start + 0.21,
			duration: (valueSlider.end - valueSlider.start - 0.21),
			destinations: getBridgetDestinations(bridget.destinations),
			layout_type: bridget.layout_type
		};
		if (bridget.file) decorated['file'] = bridget.file;
		if (bridget.id) decorated['id'] = bridget.id;
		return decorated;
	}

	function getBridgetDestinations(destinations){
		if (!destinations) return [];
		var newdestinations = [];
		destinations.forEach(function(dest){
			newdestinations.push(dest.id || dest);
		});
		return newdestinations;
	}

	function bridgetCreate(bridget){
		var newbridget = bridgetDecorator(bridget);
		if (!newbridget.title || newbridget.title == "") {
			config.error('lang.wimbridget.bridget.CREATE.TITLE-ERROR');
			return;
		}
		bridgetEditorService.bridgetCreate(getFormData(newbridget), bridgetCreateSuccess, bridgetCreateError);

		// Callback success
		function bridgetCreateSuccess(res){
			bridgetList(vm.sourceContent);
			vm.bridget = bridgetReset();
			config.success('lang.wimbridget.bridget.CREATE.FINISH');
		}

		// Callback error
		function bridgetCreateError(err){
			config.error('lang.wimbridget.bridget.CREATE.ERROR');
		}
	}

	function bridgetPatch(bridget){
		bridget = bridgetDecorator(bridget);
		bridgetEditorService.bridgetPatch(getFormData(bridget), bridgetPatchSuccess, bridgetPatchError);

		function bridgetPatchSuccess(res){
			bridgetList(vm.sourceContent);
			config.success('lang.wimbridget.bridget.PATCH.FINISH');
		}
		function bridgetPatchError(err){
			config.error('lang.wimbridget.bridget.PATCH.ERROR');
		}
	}

	function bridgetList(sourceContent){
		bridgetEditorService.bridgetList(sourceContent.id, bridgetListSuccess, bridgetListError);

		// Callback success
		function bridgetListSuccess(res){
			vm.bridgets = res.data;
			for (var k in vm.bridgets) {
				if(vm.bridgets.hasOwnProperty(k)) {
					vm.bridgets[k].verboseDestination = vm.bridgets[k].destinations[0];
				}
			}
		}

		// Callback error
		function bridgetListError(err){
			config.error('lang.wimbridget.bridget.LIST.ERROR');
		}
	}

	function initEditing(bridget){
		var start = Math.ceil(bridget.start * 100) / 100;
		var end = Math.floor((bridget.start + bridget.duration) * 100) / 100;
		timeChange(start, end);
	}

	function bridgetReset(){
		vm.adding = false;
		return {
			title: '',
			description: '',
			tags: '',
			newStart: 0,
			newEnd: 0,
			verboseDestination: null
		};
	}

	function getFormData(obj){
		var formData = new FormData();
		// clono l'oggetto da manipolare
		var data = angular.extend({}, obj);
		for (var key in data) {
			formData.append(key, data[key]);
		}
		return formData;
	}

	function setDestination(bridgetItem, bridget){
		bridget.destinations = bridgetItem ? [bridgetItem._id] : [];
		bridget.verboseDestination = bridgetItem;
		if(bridget.verboseDestination) {
			bridget.verboseDestination.type = bridgetItem.type.toUpperCase();
		}
	}

	function checkLayout(bridget, type){
		if (bridget.destinations && bridget.destinations.length > 0) return false;
		switch (bridget.layout_type) {
			case 'video':
				if (type == 'video') return true;
				break;
			case 'image':
			case 'slide_left':
			case 'slide_right':
				if (type == 'image') return true;
				break;
			default:
				return false;
		}
	}

	/**
	 * Edit Modal handling
	 * @param video
	 */
	function openModal (bridget, templateUrl) {
		var modal = $uibModal.open({
			templateUrl: templateUrl,
			controller: 'wimbridgetBridgetsModalController',
			controllerAs: 'vm',
			resolve: {
				object: function () {
					return bridget;
				}
			}
		});
		modal.result.then(function (data) {
			bridgetList(vm.sourceContent);
		}).catch(function (data) {
			bridgetList(vm.sourceContent);
		});
	}
}]);
