angular
.module 'Egerep'
#
#   LIST CONTROLLER
#
.controller "CallsMissed", ($scope, $http, PhoneService) ->
	bindArguments($scope, arguments)

	$scope.deleteCall = (call) ->
		ajaxStart()
		$http.delete "calls/" + call.entry_id, {}
		.then (response) ->
			ajaxEnd()
			$scope.calls = _.without $scope.calls, call