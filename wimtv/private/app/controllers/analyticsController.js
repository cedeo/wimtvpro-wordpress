angular.module("wimtvApp").config(function ($stateProvider) {
	$stateProvider.state('stateAnalytics', {
		url: "/analytics",
		templateUrl: wimtv_plugin_path + "private/app/partials/stateAnalytics.html",
		controller: "analyticsController as vm"
	})
});

angular.module("wimtvApp").controller('analyticsController', ['userService', 'analyticsService', '$location', 'config', function (userService, analyticsService, $location, config) {

	var vm = this;
	vm.applyFilters = applyFilters;
	vm.toggleItem = toggleItem;
	init();

	function init() {
		vm.activePanel = $location.search().activePanel || 'vod';
		vm.dateStart = moment(new Date()).startOf('month').toDate();
		vm.dateEnd = new Date();
		vm.type = "day";

		vm.enabledSeries = ['total'];

		getUser();
		setSettings(true);
	}

	function getUser() {
		userService.getMeOverview().then(success).catch(failure);

		function success(response) {
			vm.profileOverview = response.data;
			vm.bandPercent = vm.profileOverview.bandPercent.replace(',','.');
			vm.storagePercent = vm.profileOverview.storagePercent.replace(',','.');

			getStats(vm.profileOverview.userCode, vm.dateStart.getTime(), vm.dateEnd.getTime());

		}
		function failure(response) {
			config.error("lang.userprofile.GET.ERROR", response);
		}
	}

	function toggleItem(vod) {
		var index = vm.enabledSeries.indexOf(vod.title);

		if(index === -1) {
			vm.enabledSeries.push(vod.title);
		} else {
			vm.enabledSeries.splice(index, 1);
		}

		filterViewStats(vod);
	}
	
	function applyFilters() {
		getStats(vm.profileOverview.userCode, vm.dateStart.getTime(), vm.dateEnd.getTime());
	}

	function getStats(userCode, dateStart, dateEnd) {
		analyticsService.getGlobalStats(userCode, dateStart, dateEnd, refreshGlobalStats);
		analyticsService.getGlobalStreams(userCode, dateStart, dateEnd, refreshStreamStats);
	}

	function refreshGlobalStats(data) {
		vm.globalStats = data;
	}
	function refreshStreamStats(data) {
		vm.viewsStats = data;
		if(vm.dateEnd - vm.dateStart > 172800000 && vm.type === "hour") {
			vm.type = "day";	// more than 1 day: show days
		}
		var builtMap = analyticsService.buildMap(vm.viewsStats, {start: vm.dateStart, end: vm.dateEnd}, vm.type);
		var map = builtMap.vod;
		vm.liveSeries = builtMap.live.series;
		vm.liveValues = builtMap.live.values;

		// Datas
		vm.series = Object.keys(map);
		vm.labels = Object.keys(map.total);
		vm.data = [];
		for (var j = 0; j < vm.series.length; j++) {
			for (var i = 0; i < vm.labels.length; i++) {
				if(!vm.data[j]) {
					vm.data[j] = [];
				}
				vm.data[j].push(map[vm.series[j]][vm.labels[i]]);
			}
		}

		var formatString = 'YYYY-MM-DD';
		if(vm.type === "hour") {
			formatString = 'HH:mm';
		}
		for(var i = 0; i < vm.labels.length; i++) {
			vm.labels[i] = moment(vm.labels[i]*1000).format(formatString)
		}

		filterViewStats();
	}

	function filterViewStats() {
		vm.viewLabels = vm.labels;		// unfiltered.

		vm.viewData = [];
		vm.viewSeries = [];

		if(vm.enabledSeries.length === 0) {
			vm.enabledSeries = ['total'];
		}

		for (var i = 0; i < vm.series.length; i++) {
			if(vm.enabledSeries.indexOf(vm.series[i]) !== -1) {
				vm.viewData.push(vm.data[i]);
				vm.viewSeries.push(vm.series[i]);
			}
		}

		for (var i = 0; i < vm.viewsStats.vod.length; i++) {
			vm.viewsStats.vod[i].enabled = vm.enabledSeries.indexOf(vm.viewsStats.vod[i].title) !== -1;
			vm.enabledTotal = vm.enabledSeries.indexOf('total') !== -1;
		}

		// add total
		var totals = vm.data[vm.series.indexOf('total')];
		var total = 0;
		for (var i = 0; i < totals.length; i++) {
			total += totals[i];
		}
		vm.totalViews = total;
	}

	function setSettings(init) {
		if(init) {
			vm.options = {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					yAxes: [
						{
							id: 'y-axis-1',
							type: 'linear',
							display: true,
							position: 'left',
							ticks: {
								beginAtZero: true   // minimum value will be 0.
							}
						}
					]
				}
			};
		}
	}
}]);
