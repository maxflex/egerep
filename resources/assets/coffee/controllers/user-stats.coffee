angular
	.module 'Egerep'
	#
	#   LIST CONTROLLER
	#
	.controller "UserStats", ($scope, TutorStates, User, UserService) ->
		bindArguments($scope, arguments)
		$scope.state_cnt = Object.keys(TutorStates).length + 2

		$scope.sum = (arr) ->
			_.reduce(
				arr,
				(m, n) ->
					m + n
				, 0)

