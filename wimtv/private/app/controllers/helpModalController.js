angular.module("wimtvApp").controller('helpModalController', ['$scope', '$uibModalInstance', 'config', 'resourceService', 'params', function ($scope, $uibModalInstance, config, resourceService, params) {
	var vm = this;
	vm.confirm = confirm;
	vm.close = close;

	vm.params = params;

	// default videos - english
	var videoMap = {
		'box': 		'c41437b3-cb11-40e9-8fb1-a93075a50915',
		'vod': 		'204499f7-36df-4739-b07d-fdf206f6468f',
		'live': 	'71197dee-ec3b-4f9c-84b0-48ca6262e301',
		'trade': 	'260608d0-15cb-400a-b529-e93c71346242',
		'bundle': 	'599d0e67-2f23-440e-9e8b-a50a4972d4e0',
		'cast': 	'f0b1d665-c662-4eef-8f72-971264d547fe',
		'bridge': 	'108f9c8f-85fc-4c09-99ad-4990250cb8e8'
	};
	if(params.lang === 'it') {
		// italian videos
		videoMap = {
			'box': 		'0039dac8-ccfa-434a-89aa-e77122edb37d',
			'vod': 		'aea95e03-3d09-454f-9d75-07e148e5af4b',
			'live': 	'2de59cc4-ee6d-44e6-9f32-0087e43492ec',
			'trade':	'ae8c7f96-c8bf-4576-be93-6ad3adc6316f',
			'bundle': 	'30ca05be-fcf5-4424-96fa-06dba329d691',
			'cast': 	'9f4fa867-01b9-4539-b4ca-193907514f11',
			'bridge': 	'294975a5-9e42-47d5-823b-868f9756b67d'
		}
	}
	play();

	function confirm(obj) {
		$uibModalInstance.close(obj);
	}
	function close(origin) {
		$uibModalInstance.dismiss(origin);
	}
	
	function play(){

		vm.eventId = videoMap[params.type];

		resourceService.play('vod', vm.eventId, success, failure);
		function success(response) {
			console.log("OK");
		}
		function failure(response) {
			console.log("KO");
		}
	}
}]);
