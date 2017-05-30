angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimbox', {
		url: "/wimbox",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbox.html",
		controller: "wimboxController",
		controllerAs: "vm"

	})
		.state('stateWimboxLoadVideo', {
			url: "/wimboxLoadVideo",
			templateUrl: wimtv_plugin_path + "private/app/partials/stateWimbox.html",
			controller: "wimboxController"
		})
});

// Main Page
angular.module("wimtvApp").controller('wimboxController', ['$scope', '$rootScope', '$http', '$timeout', '$interval', '$httpParamSerializerJQLike', '$filter', '$location', '$uibModal', 'wimBoxService', 'config', 'uploadQueue', 'userService',
	function ($scope, $rootScope, $http, $timeout, $interval, $httpParamSerializerJQLike, $filter, $location, $uibModal, wimBoxService, config, uploadQueue, userService) {
		var vm = this;

		vm.openModal = openEditModal;
		vm.openDeleteModal = openDeleteModal;
		vm.openPublishOnMarketModal = openPublishOnMarketModal;
		vm.getVideos = getVideos;
		vm.refreshVideos = refreshVideos;
		vm.selectedFiles = selectedFiles;
		vm.upload = upload;
		vm.publishVideo = publishVideo;
		vm.checkAndUpdateTags = checkAndUpdateTags;
		vm.isPublishable = isPublishable;
		vm.preview = preview;
		vm.removeTag = removeTag;
		vm.clearUploadData = clearUploadData;
		vm.download = download;

		vm.querySearchString = "";

		vm.licenseTypes = config.licenseTypes;
		vm.ccTypes = config.ccTypes;

		// used to track how many failures we got loading videos
		var loadFailures;

		init();

		/**
		 * Listener on event "paypalUpdated" triggered when the user updates the paypal email address located in the top of the privatePage.html ('monetisation')
		 */
		$scope.$on('paypalUpdated',function (event, data) {
			checkPaypalAndFinance();
		});

		function init() {
			vm.uploadCollapse = $location.search().upload == 'true';
			vm.pageIndex = 1;

			checkPaypalAndFinance();
			checkUserLimit();

			loadFailures = 0;			// reset to 0.
			getVideos();
			// get videos interval: ONLY UPDATE VIDEOS IF WE ARE NOT PUBLISHING A VIDEO. Todo: improve stability
			var interval = window.setInterval(function () {
				if(!isPublishingVideo()) {
					$timeout(refreshVideos, 50);
				}
			}, 25000);
			$scope.$on('$locationChangeStart', function () {
				window.clearInterval(interval);
			});
		}


		/**
		 * Ohi! Reverse logic here: "isPublishable" is - in truth - "isPublishDisabled"
		 * @param video
		 * @param form
		 * @returns {*}
		 */
		function isPublishable(video, form) {
			if (video.publishData.licenseType === "FREE" || video.publishData.licenseType === "CREATIVE_COMMONS") {
				return false;
			} else {
				return vm.missingPaypal || vm.missingBillingInfo || form.$invalid || !form.$dirty;
			}
		}

		function checkPaypalAndFinance() {
			config.checkPaypalAndFinance(userService, callback);
			function callback(response) {
				vm.missingPaypal = response.paypal;
				vm.missingBillingInfo = response.billing;
			}
		}

		function checkUserLimit() {
			userService.getMeOverview().then(success).catch(failure);

			function success(response) {
				// storage is over 100%. Unable to upload anything
				if(response && response.data && parseInt(response.data.storagePercent) >= 100) {
					vm.limitReached = true;
				}
			}
			function failure(response) {
				config.warning("lang.userprofile.ERROR.GET", response);
			}
		}

		/**
		 * Edit Modal handling
		 * @param video
		 */
		function openEditModal (video) {
			var modal = $uibModal.open({
				templateUrl: 'editModal.html',
				controller: 'wimboxModalController',
				controllerAs: 'vm',
				resolve: {
					object: function () {
						return video;
					}
				}
			});
			modal.result.then(function (data) {
				config.success('lang.wimbox.EDIT.FINISH');
				refreshVideos();
			}).catch(function (data) {
				refreshVideos();
			});
		}

		function openPublishOnMarketModal(video) {
			var modal = $uibModal.open({
				templateUrl: 'marketModal.html',
				controller: 'wimboxModalController',
				controllerAs: 'vm',
				resolve: {
					object: function () {
						return video;
					}
				}
			});
			modal.result.then(function (data) {
				config.success('lang.wimbox.EDIT.FINISH');
				refreshVideos();
			}).catch(function (data) {
				refreshVideos();
			});
		}

		function removeTag(video, tag) {
			delete video.metadata.tag[tag]
		}

		function preview(video) {
			var modal = $uibModal.open({
				templateUrl: 'videoPreview.html',
				controller: 'wimboxModalController',
				controllerAs: 'vm',
				resolve: {
					object: function () {
						return video;
					}
				}
			});
			modal.result.then(function (data) {
				// nothing to do
			}).catch(function (data) {
				// todo: handle errors;
				// nothing to do here too?
			});
		}

		/**
		 * Simple videos reload
		 */
		function refreshVideos() {
			vm.refreshing = true;
			// config.info("lang.wimbox.GETTING-VIDEOS");
			var pagination = {
				pageIndex: vm.pageIndex-1,
				pageSize: 10,
				queryString: vm.querySearchString
			};
			wimBoxService.searchForContents(pagination, success, failure);

			function success(response) {
				loadFailures = 0;
				vm.videosLoaded = true;
				vm.videosError = false;
				if (vm.videos && (response.data['items'].length > vm.videos.length)) {
					$scope.newVideo = response.data['items'].length - vm.videos.length;
					$timeout(function () {
						$scope.newVideo = 0;
					}, 10000);
				}

				vm.videos = response.data['items'];
				vm.totalItems = response.data.totalCount;
				$timeout(function () {
					vm.refreshing = false;
				}, 1250);
			}
			function failure(response) {
				console.log("Error loading videos");
				if(loadFailures > 10) {
					config.error('lang.wimbox.GETPAGE.ERROR', response);
					vm.videosLoaded = true;
					vm.videosError = true;
					$timeout(function () {
						vm.refreshing = false;
					}, 1250)
				} else {
					loadFailures++;
				}
			}
		}

		/**
		 * Get List of videos
		 */
		function getVideos() {
			loadFailures = 15;		// force to a high number to get an error in case of problems
			$timeout(refreshVideos, 100);
		}

		/**
		 * Delete Modal handling
		 * @param object
		 */
		function openDeleteModal(object) {
			var modal = $uibModal.open({
				templateUrl: 'removeModal.html',
				controller: 'wimboxModalController',
				controllerAs: 'vm',
				resolve: {
					object: function () {
						return object;
					}
				}
			});

			modal.result.then(function (data) {
				wimBoxService.deleteVideo(data.boxId, success, failure);
				function success(response) {
					if(response.status >= 200 && response.status < 300) {
						config.success("lang.wimbox.DELETE.FINISH");
					} else {
						config.error("lang.wimbox.DELETE.ERROR", response);
					}
					getVideos();
				}
				function failure(response) {
					config.error("lang.wimbox.DELETE.ERROR", response);
				}
			});
		}

		function selectedFiles() {
			// for (var k in vm.videosToUpload) {
			// 	if(vm.videosToUpload.hasOwnProperty(k) && vm.videosToUpload[0].type.indexOf('video') === -1) {
			// 		delete vm.videosToUpload[k];
			// 	}
			// }
		}

		function upload(videosToUpload) {
			vm.uploading = true;

			// TO the top of the page...
			$("html, body").animate({
				scrollTop: 0
			}, 600);

			for (var k in videosToUpload) {
				if (videosToUpload.hasOwnProperty(k)) {
					if(!videosToUpload[k].metadata) {
						videosToUpload[k].metadata = {};
					}
					// fix tag data
					if(videosToUpload[k].metadata.tag) {
						videosToUpload[k].metadata.tag = Object.keys(videosToUpload[k].metadata.tag);
					}
					// delete videosToUpload[k].metadata.tags;
					delete videosToUpload[k].metadata.tagText;

					// analytics event tracking
					config.track('UploadStart');

					// uploading using uploadQueue
					uploadQueue(videosToUpload[k]).then(success).catch(failure);
				}
			}
			function success(response) {
				if(response.status >= 200 && response.status < 300) {
					config.success('lang.wimbox.UPLOAD.FINISH');
					// analytics event tracking
					config.track('UploadSuccess');

					if(response.queue_length === 0) {
						clearUploadData();
						// LAST element uploaded
						vm.uploading = false;
						vm.uploadCollapse = false;
						getVideos();
					}
				} else {
					failure(response);
				}
			}
			function failure(response) {
				// analytics event tracking
				config.track('UploadError');

				clearUploadData();
				config.error('lang.wimbox.UPLOAD.ERROR', response);
			}
		}
		
		function download(video) {
			config.globalLoading(true);
			wimBoxService.downloadVideo(video, success, failure);

			function success(response) {
				config.success('lang.wimbox.DOWNLOAD.SUCCESS');
				$timeout(function () {
					var url = $rootScope.wimtvHost + "/download/content/" + video.contentId + "?token="+response.data.token;
					document.getElementById('iframe-downloader').src = url;
					config.globalLoading(false);
				}, 250);
			}
			function failure(response) {
				config.error('lang.wimbox.DOWNLOAD.ERROR', response);
				config.globalLoading(false);
			}
		}
		
		function clearUploadData() {
			vm.videosToUpload = null;
		}

		/**
		 * cycle through videos to check if there's a publishing video
		 */
		function isPublishingVideo() {
			for (var k in vm.videos) {
				if(vm.videos.hasOwnProperty(k) && vm.videos[k].publish) {
					return true;
				}
			}
			return false;
		}

		function publishVideo (video) {
			wimBoxService.publishVideo(video.boxId, checkPublishData(video.publishData),
				function (response) {
					config.success("lang.wimbox.PUBLISH.FINISH");
					video.publish = false;
					// analytics publishing event
					config.track('VideoPublishSuccess');
					refreshVideos();
				},
				function (response) {
					config.error('lang.wimbox.PUBLISH.ERROR', response);
					config.track('VideoPublishError');
					video.publish = false;
					refreshVideos();
				}
			);
		}

		function checkPublishData(publishData) {
			if(publishData.licenseType !== "PAY_PER_VIEW") {
				delete publishData.pricePerView;
			}
			if(publishData.licenseType !== "CREATIVE_COMMONS") {
				delete publishData.ccType;
			}
			return publishData;
		}

		function checkAndUpdateTags($event, metadata) {
			if($event.keyCode === 9 || $event.keyCode === 13) {			// tab or enter pressed
				if (!metadata.tag) {
					metadata.tag = {};
				}
				if (metadata.tagText.length > 0 ) {
					var tag = metadata.tagText.substr(0, metadata.tagText.length);
					metadata.tag[tag] = tag;
					metadata.tagText = "";
				} else {
					console.log("nodata to push");
				}
				$event.preventDefault();
				$event.stopPropagation();
			}
		}
	}]);

// Modal
angular.module("wimtvApp").controller('wimboxModalController', ['$scope','$uibModalInstance','object','wimBoxService','marketPlaceService','commonService','config', function($scope, $uibModalInstance, object, wimBoxService, marketPlaceService, commonService,config) {
	var vm = this;
	vm.confirm = confirm;
	vm.close = close;
	vm.video = object;
	vm.updateVideo = updateVideo;
	vm.deleteVideo = deleteVideo;
	vm.changeImage = changeImage;
	vm.playBoxItem = playBoxItem;
	vm.checkAndUpdateTags = checkAndUpdateTags;
	vm.removeTag = removeTag;
	vm.thumbnailId = null;

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}

	function deleteVideo(box) {
		confirm(box);
	}

	function updateVideo() {
		if (vm.thumbnailId != null) {
			vm.video.thumbnailId = vm.thumbnailId;
		}
		wimBoxService.updateVideo(vm.video,
			function success(response) {
				confirm(vm.video);
			},
			function error(response) {
				config.error("lang.wimbox.NOT-SAVED",response);
			}
		);
	}

	function removeTag(video, tag) {
		var index = video.tags.indexOf(tag);
		video.tags.splice(index, 1);
	}

	function checkAndUpdateTags($event, video) {
		if($event.keyCode === 9 || $event.keyCode === 13) {			// tab or enter pressed
			if (!video.tags) {
				video.tags = [];
			}
			if (video.tagText.length > 0 ) {
				var tag = video.tagText.substr(0, video.tagText.length);
				if(video.tags.indexOf(tag) === -1) {
					video.tags.push(tag);
				}
				video.tagText = "";
			} else {
				console.log("nodata to push");
			}
			$event.preventDefault();
			$event.stopPropagation();
		}
	}


	function playBoxItem(boxId) {
		wimBoxService.playBoxItem(boxId, success, failure);
		function success(response) {
			// playing is delegated to resource Service: nothing to do
		}
		function failure(response) {
			config.error("lang.wimvod.PREVIEW-ERROR", response);
		}
	}

	function changeImage(file, errFiles) {
		commonService.uploadImage(file, errFiles, vm.video);
	}

}]);
