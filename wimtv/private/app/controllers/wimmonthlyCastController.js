angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateWimmonthlyCast', {
		url: "/wimcast/{id}",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateWimMonthlyCast.html",
		controller: "wimmonthyCastController as vm"
	})
});
angular.module("wimtvApp").controller("wimmonthyCastController", ['$rootScope', '$stateParams', '$timeout', 'castChannelService', 'uiCalendarConfig', 'config', function ($rootScope, $stateParams, $timeout, castChannelService, uiCalendarConfig, config) {

	var vm = this;
	vm.copy = copy;
	vm.paste = paste;
	vm.deleteDays = deleteDays;
	init();

	function init() {
		config.globalLoading(true);
		getCastChannel($stateParams.id);
		vm.calendarOptions = calendarOptions();
	}


	function refreshEvents() {
		$timeout(function () {
			uiCalendarConfig.calendars.month.fullCalendar('rerenderEvents');
		}, 250);
	}

	/**
	 * Gets the Cast Channel. Save it in vm.castChannel
	 * @param id
	 */
	function getCastChannel(id) {
		castChannelService.privateGet(id, success, failure);

		function success(response) {
			vm.castChannel = response.data;
		}

		function failure(response) {
			config.error("lang.wimcast.CAST-ERROR", response);
		}
	}


	function copyCast() {
		if(!vm.selected) {
			return false;
		}

		var filters = {
			channelId: $stateParams.id,
			startDate: vm.selection.start.format("YYYYMMDD"),
			dayCount: vm.selection.end.diff(vm.selection.start, "days")
		};
		castChannelService.calendarDelete(filters, deleteSuccess, failure);

		function deleteSuccess(response) {
			castChannelService.calendarCopy(vm.selected, success, failure);

			function success(response) {
				refreshEvents();
				config.success("lang.wimcast.MONTHLY-SUCCESS");
				vm.selection = null;
				vm.copying = false;
				vm.selected = null;
			}
		}

		function failure(response) {
			config.error("lang.wimcast.MONTHLY-ERROR", response);
			vm.deleting = false;
			vm.selection = null;
			vm.copying = false;
			vm.selected = null;
		}
	}

	function getCalendar(start) {
		var date = start.format("YYYYMMDD");
		var filters = {
			channelId: $stateParams.id,
			startDate: date,
			dayCount: 42
		};
		castChannelService.calendarGet(filters, success, failure);

		function success(response) {
			for (var k = 0; k < response.data.dailyProgrammings.length; k++) {
				var item = response.data.dailyProgrammings[k];
				var date = moment(item.targetDate, "DD/MM/YYYY").format('YYYYMMDD');
				var element = $('#cell-date-' + date);
				element.removeClass("day-cell-active day-cell-inactive");
				element.addClass(item.programs.length > 0 ? "day-cell-active" : "day-cell-inactive");
			}
			config.globalLoading(false);
		}
		function failure(response) {
			config.error("lang.wimcast.CAST-ERROR", response);
			config.globalLoading(false);
		}
	}

	function deleteDays() {
		var filters = {
			channelId: $stateParams.id,
			startDate: vm.selection.start.format("YYYYMMDD"),
			dayCount: vm.selection.end.diff(vm.selection.start, "days")
		};
		castChannelService.calendarDelete(filters, success, failure);

		function success(response) {
			refreshEvents();
			config.success("lang.wimcast.MONTHLY-SUCCESS");
			vm.deleting = false;
			vm.selection = null;
		}

		function failure(response) {
			config.error("lang.wimcast.MONTHLY-ERROR", response);
			vm.deleting = false;
		}
	}

	function copy() {
		vm.selected = {
			sourceChannelId: $stateParams.id,
			destChannelId: $stateParams.id,
			sourceStartDate: vm.selection.start.format("DD/MM/YYYY"),
			sourceDayCount: vm.selection.end.diff(vm.selection.start, 'days')
		};
		vm.selection = null;
		vm.copying = true;
	}

	function paste() {
		// pasting
		vm.selected.destStartDate = vm.selection.start.format("DD/MM/YYYY");
		vm.selected.destDayCount = vm.selection.end.diff(vm.selection.start, "days");
		vm.enableSelection = false;
		copyCast();
	}

	/**
	 * Generates options for the Calendar
	 * @returns object formatted as specified in fullcalendar docs.
	 */
	function calendarOptions() {
		return {
			// dayClick: dayClick,
			locale: $rootScope.currentLang,
			height: 500,
			dayRender: dayRender,
			eventAfterAllRender: viewRender,
			selectable: true,
			select: select,
			unselect: unselect,
			header: {
				left:   'title',
				right:  'today prev,next'
			}
		}
	}

	// CALENDAR EVENT HANDLERS

	/**
	 * Triggered when a selection is made.
	 * Used for copying data.
	 * @param start
	 * @param end
	 * @param event
	 * @param view
	 * @param resource
	 * @returns {null}
	 */
	function select(start, end, event, view, resource) {
		vm.deleting = false;
		// copying
		vm.selection = {
			start: start,
			end: end
		};
	}


	/**
	 * Triggered when a selection is being dismissed
	 * @param view
	 * @param event
	 */
	function unselect(view, event) {
		console.log("unselecting");
	}

	/**
	 * Triggered when a day is clicked
	 * @param date
	 * @param event
	 * @param view
	 */
	function dayClick(date, event, view) {
		window.location.href = window.location.href + "/" + date.format('YYYYMMDD');
	}

	/**
	 * Rendered of a day. Used for generate custom ids of days.
	 * @param date
	 * @param cell
	 */
	function dayRender(date, cell) {
		var formatDate = date.format('YYYYMMDD');
		cell.html('' +
			'<div class="day-cell-container" style="z-index: 12 !important" id="cell-date-' + formatDate + '">' +
			'<a href="#/wimcast/' + $stateParams.id + '/' + formatDate + '" style="z-index: 14;" class="btn btn-default btn-calendar"><i class="fa fa-edit"></i> ' + date.format("DD") + '</a>' +
			'<span class="cell-align-right"></span>' +
			'</div>');
	}

	/**
	 * Renderer of the main view.
	 * @param view
	 * @param element
	 */
	function viewRender(view, element) {
		getCalendar(view.start);
	}

}]);
