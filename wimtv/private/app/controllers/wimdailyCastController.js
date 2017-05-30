angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimdailyCast', {
		url: "/wimcast/{id}/{date}",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimDailyCast.html",
		controller: "wimdailyCastController as vm"
	})
});
angular.module("wimtvApp").controller("wimdailyCastController", ['$scope', '$timeout', '$stateParams', 'castChannelService', 'castStreamService', 'wimBoxService', 'dailyProgrammingService', 'channelService', 'commonService', 'config',
	function ($scope, $timeout, $stateParams, castChannelService, castStreamService, wimBoxService, dailyProgrammingService, channelService, commonService, config) {

		var vm = this;
		vm.saveProgramming = saveProgramming;
		vm.loadBoxVideos = loadBoxVideos;
		vm.add = add;
		vm.createLive = createLive;
		vm.getStreamPath = getStreamPath;
		vm.changeCastThumbnail = changeCastThumbnail;
		vm.draggable = draggable;
		vm.editDelete = editDelete;
		vm.streamEdit = streamEdit;
		vm.streamDelete = streamDelete;
		init();

		function init() {
			console.log("init");
			vm.boxPageIndex = 1;
			vm.boxPageSize = 12;
			vm.date = $stateParams.date;
			calculatePrevNextDay();
			vm.showTab = 'video';
			vm.newCast = {};
			loadBoxVideos();
			getCastChannel($stateParams.id);
			getStreams();
		}

		function calculatePrevNextDay() {
			var date = moment(vm.date, "YYYYMMDD");
			date.add(1, "days");
			vm.tomorrowParsed = date.format("DD/MM/YYYY");
			vm.tomorrowLink = location.href.substr(0,location.href.length - 8) + date.format("YYYYMMDD");
			date.subtract(2, "days");
			vm.yesterdayParsed = date.format("DD/MM/YYYY");
			vm.yesterdayLink = location.href.substr(0,location.href.length - 8) + date.format("YYYYMMDD");
		}


		/**
		 * gets the channel from the id.
		 * @param id
		 */
		function getCastChannel(id) {
			castChannelService.privateGet(id, success, failure);

			function success(response) {
				vm.castChannel = response.data;
				getDailyProgramming();
			}

			function failure(response) {
				config.error('lang.wimcast.CAST-ERROR', response);
			}
		}

		/**
		 * Initializes the wimdailycastprogramming bar
		 */
		function initCastBar() {
			// DOM element where the Timeline will be attached
			var container = document.getElementById('dailyProgramming');

			container.innerHTML = "";

			var date = moment(vm.date, "YYYYMMDD");
			var endDate = moment(vm.date, "YYYYMMDD").hours(date.hours()+12);

			var options = {
				// no custom options?
				selectable: true,
				editable: true,
				start: vm.timelineWindow ? vm.timelineWindow.start : date,
				end: vm.timelineWindow ? vm.timelineWindow.end : endDate,
				navigationButtons: true
			};

			vm.timeline = Timeline.init(container, vm.date, options);

		}

		/**
		 * Loads the daily programming of the daily
		 */
		function getDailyProgramming() {
			dailyProgrammingService.privateGet(vm.castChannel.channelId, vm.date, success, failure);
			function success(response) {
				vm.dailyInfo = response.data;
				initCastBar();
				vm.programs = response.data.programs;
				var programs = response.data.programs;
				for (var k in programs) {
					if(programs.hasOwnProperty(k) && programs[k].itemType !== 'BREAK') {
						// exclude break
						var item = {
							start: config.timeFromCustomDate(programs[k].startDate),
							end: config.timeFromCustomDate(programs[k].endDate),
							startDate: programs[k].startDate,
							endDate: programs[k].endDate,
							sourceType: programs[k].itemType,
							programId: programs[k].programId
						};
						item.duration = item.end - item.start;

						switch (programs[k].itemType) {
							case "PRERECORDED":
								item.content = programs[k].boxContent.title;
								item.videoType = "Video on Demand";
								item.sourceId = programs[k].boxContent.boxId;
								item.thumbnailId = programs[k].boxContent.thumbnailId;
								item.thumbnailUrl = config.getThumbnailUrl(item.thumbnailId);
								break;
							case "LIVE":
								item.content = programs[k].stream.name;
								item.videoType = "Live";
								item.sourceId = programs[k].stream.streamId;
								item.thumbnailId = programs[k].stream.thumbnailId;
								item.thumbnailUrl = config.getThumbnailUrl(item.thumbnailId);
								break;
						}
						Timeline.addItem(item);
					}
				}
			}
			function failure(response) {
				config.error('lang.wimcast.CAST-ERROR', response);
			}
		}

		/**
		 * Main saving function.
		 * Filled with logic and problems.
		 */
		function saveProgramming() {
			var orderedItems = orderDailyProgrammingEvents(vm.timeline.itemsData);
			// var filledItems = fillTheSpace(vm.date, orderedItems);

			vm.timelineWindow = vm.timeline.getWindow();

			dailyProgrammingService.saveEvents(vm.castChannel.channelId, vm.date, {programs: orderedItems}, success, failure);

			function success(response) {
				config.success('lang.wimcast.MONTHLY-SUCCESS');
				getDailyProgramming();
			}
			function failure(response) {
				config.warning('lang.wimcast.MONTHLY-ERROR', response);
				console.log(response);
			}
		}

		/**
		 * gets all wimbox videos available for the webtv
		 */
		function loadBoxVideos() {
			var pagination = {
				pageIndex: vm.boxPageIndex - 1,
				pageSize: vm.boxPageSize,
				queryString: ""
			};
			wimBoxService.searchForContents(pagination,success,failure);
			function success(response) {
				vm.boxVideos = response.data;
				vm.boxLoaded = true;
			}
			function failure(response) {
				config.error('lang.wimbundle.single.UNABLE-FETCHING-VIDEOS', response);
			}
		}

		/**
		 * Handles the dragging function
		 */
		function draggable() {
			$timeout(function () {
				var elements = $('.draggable');
				elements.draggable({
					revert: true,
					revertDuration: 500,
					containment: $('#drag-container'),
					start: function (event, ui) {
						$(this).addClass("dragging");
					},
					stop: function (event, ui) {
						$(this).removeClass("dragging");
					}
				});
				var timelineElement = $('#dailyProgramming');
				timelineElement.droppable({
					accept: ".draggable",
					activate: function (event, ui) {
						timelineElement.addClass('triggered');
					},
					deactivate: function (event, ui) {
						timelineElement.removeClass('triggered');
					},
					drop: function (event, ui) {
						var pxPos = event.clientX - $(this).offset().left;
						add('video', {
							sourceType: ui.draggable.find('.type').text(),
							content: ui.draggable.find('.hidden.title').text(),
							duration: ui.draggable.find('.duration').text(),
							start: new Date(), // it'll be overwritten
							sourceId: ui.draggable.find('.boxId').text()
						}, pxPos);
					}
				});
			}, 1000);
		}

		/**
		 * Add smth to the Timeline daily cast programming
		 * @param type		could be PRERECORDED, TODO: other cases
		 * @param object	if PRERECORDED, is the video object to be added
		 * @param pxPos		the X position in pixels triggered by the drop
		 */
		function add(type, object, pxPos) {
			switch (object.sourceType) {
				case 'PRERECORDED':
					object.videoType = "Video on Demand";
					break;
				case 'LIVE':
					object.videoType = "Live";
					break;
			}
			Timeline.add(object, pxPos);
		}

		/**
		 * Orders by start date an array of items.
		 * @param items
		 * @returns {Array}
		 */
		function orderDailyProgrammingEvents(items) {
			var ordered = [];
			// builds a map with key based on time.
			items.forEach(function (item) {
				ordered.push({
					startDate: {
						date: moment(item.start).format('DD/MM/YYYY'),
						time: moment(item.start).format('HH:mm:ss')
					},
					sourceId: item.sourceId,
					description: item.description,
					title: item.content,
					tags: item.tags,	// TODO: add
					endDate: {
						date: moment(item.end).format('DD/MM/YYYY'),
						time: moment(item.end).format('HH:mm:ss')
					},
					programId: item.programId, // TODO: missing
					// thumbnailId: item.thumbnailId,
					programType: item.sourceType
				});
			}, {order: "start"});

			return ordered;
		}

		/**
		 * fills a list with breaks. Also optimize content for the insertion
		 * @param date	the starting point (00:00 of this date)
		 * @param items
		 * @returns {Array}
		 */
		function fillTheSpace(date, items) {
			var fillFrom = moment(date).unix()*1000;
			var filledList = [];
			for(var k in items) {
				if(items.hasOwnProperty(k)) {
					var aBreak = fillingBreak(fillFrom, items[k].start);
					if(aBreak) {
						filledList.push(aBreak);
					}
					var item = {
						sourceId: items[k].sourceId,
						sourceType: items[k].sourceType
					};
					if(item.sourceType === "LIVE") { // add duration only for live
						item.duration = Math.floor((items[k].end - items[k].start) / 1000);
					}
					filledList.push(item);
					fillFrom = items[k].end;
				}
			}
			return filledList;
		}

		function fillingBreak(fillFrom, fillTo) {
			var duration = Math.floor((fillTo - fillFrom) / 1000);
			if(duration === 0) {
				return null;
			}
			return {
				sourceType: "BREAK",
				duration: duration
			}
		}


		function createLive() {
			getStreamPath(vm.newCast, function () {
				castStreamService.create(vm.newCast, success, failure);

				function success(response) {
					vm.createNewLive = false;
					vm.newCast = {};
					getStreams();
					draggable();
					config.success("lang.wimchannel.MESSAGES.SUCCESSFUL-ADD-LIVE");
				}

				function failure(response) {
					config.error("lang.wimchannel.MESSAGES.FAILURE-ADD-LIVE", response);
				}
			});
		}
		
		function getStreamPath(live, successCallback) {
			channelService.getChannelName(live.name, success, failure);

			function success(response) {
				live.streamPath = response.data.streamPath;
				if(successCallback) {
					successCallback();
				}
			}
			function failure(response) {
				config.error("lang.wimchannel.MESSAGES.UNABLE-TO-FETCH-STREAM-URL", response);
			}
		}

		function getStreams() {
			var filters = {
				pageIndex: 0,
				pageSize: 20,
				userCode: vm.webTv
			};
			castStreamService.search(filters, success, failure);

			function success(response) {
				vm.streams = response.data;
			}
			function failure(response) {
				config.error('lang.wimcast.CAST-LIVE-ERROR', response);
				console.log(response);
			}
		}

		function changeCastThumbnail(file, errFiles, cast) {
			cast.updatingThumbnail = true;
			commonService.uploadImage(file, errFiles, cast, success, error, uploading);
			function success(response) {
				cast.thumbnailId = response.data.thumbnailId;
				$timeout(hideUpdating, 1500);
			}

			function uploading(response) {
				cast.updatingThumbnail = true;
				$timeout(hideUpdating, 2500);
			}

			function error(response) {
				cast.updatingThumbnailError = true;
			}

			function hideUpdating() {
				cast.updatingThumbnail = false;
			}
		}

		/**
		 * handles the edit or delete view of a stream
		 * @param action	editing or deleting
		 * @param video		object
		 * @param status	true for enabling, false for reverting to original condition
		 */
		function editDelete(action, video, status) {
			if(status) {
				video[action] = angular.copy(video);
			} else {
				video[action] = null;
			}
		}

		/**
		 * Edits a stream video
		 * @param editedVideo
		 */
		function streamEdit(editedVideo) {
			castStreamService.update(editedVideo, success, failure);

			function success(response) {
				console.log(response);
				editDelete('editing', editedVideo, false);
				getStreams();
				config.success('lang.wimchannel.MESSAGES.SUCCESSFUL-EDIT-LIVE');
			}
			function failure(response) {
				config.error('lang.wimchannel.MESSAGES.FAILURE-EDIT-LIVE', response);
			}
		}

		function streamDelete(video) {
			castStreamService.remove(video.streamId, success, failure);
			function success(response) {
				console.log(response);
				editDelete('deleting', video, false);
				getStreams();
				config.success('lang.wimchannel.MESSAGES.SUCCESSFUL-DELETE-LIVE');
			}
			function failure(response) {
				config.error('lang.wimchannel.MESSAGES.FAILURE-DELETE-LIVE', response);
			}
		}
		
}]);
