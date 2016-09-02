angular
.module 'Egerep'
.controller 'ContractIndex', ($scope, $http, UserService) ->
	bindArguments($scope, arguments)

.controller 'ContractEdit', ($scope, $http, $timeout, UserService) ->
	bindArguments($scope, arguments)

	$scope.save = ->
		ajaxStart()
		this.saving = true
		$scope.contract_html = $scope.editor.getValue()
		$http.post "contract",
			contract_html: $scope.contract_html
			contract_date: $scope.contract_date
		.then (response) ->
			ajaxEnd()
			this.saving = false

	angular.element(document).ready ->
		$timeout ->
			$scope.editor = ace.edit 'editor'
			$scope.editor.getSession().setMode 'ace/mode/html'
		, 300
