angular
.module 'Egerep'
.controller 'SmsIndex', ($scope, $rootScope, $timeout, $http) ->
	bindArguments($scope, arguments)

	$rootScope.frontend_loading = true

	$scope.pageChanged = ->
		$scope.filter($scope.current_page)
		paginate('sms', $scope.current_page)

	$scope.filter = (page = null) ->
		$rootScope.frontend_loading = true

		params = {page: (page || $scope.current_page), search: $scope.search, is_secret: $scope.is_secret}

		# update repetitors
		# @todo: why ugly params? maybe use $http.post instead?
		$http.get "api/sms/list?" + $.param(params)
			.then (response) ->
				$rootScope.frontendStop()
				$scope.data = response.data
				$scope.sms = $scope.data.data

	$timeout ->
		setTimeout ->
			$('.phone-masked').attr('placeholder', 'отправить СМС')
		, 1000
		$scope.filter($scope.page)
		$scope.current_page = $scope.page
