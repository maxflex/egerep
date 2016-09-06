angular
.module 'Egerep'
.factory 'Archives', ($resource) ->
	$resource 'api/archives/:id', {},
		update:
			method: 'PUT'
.controller 'ArchivesIndex', ($rootScope, $scope, $timeout, $http, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility, AttachmentErrors, ArchiveStates, Checked) ->
	bindArguments($scope, arguments)
	$rootScope.frontend_loading = true

	refreshCounts = ->
		$timeout ->
			$('.selectpicker option').each (index, el) ->
				$(el).data 'subtext', $(el).attr 'data-subtext'
				$(el).data 'content', $(el).attr 'data-content'
			$('.selectpicker').selectpicker 'refresh'
		, 100

	$scope.filter = ->
		$.cookie('archives', JSON.stringify($scope.search), { expires: 365, path: '/' });
		$scope.current_page = 1
		$scope.pageChanged()

	$scope.changeState = (state_id) ->
		$rootScope.frontend_loading = true
		$scope.archives = []
		$scope.current_page = 1
		loadArchives $scope.current_page
		window.history.pushState(state_id, '', 'archives/' + state_id.toLowerCase());

	$timeout ->
		$scope.search = if $.cookie('archives') then JSON.parse($.cookie('archives')) else {}
		loadArchives $scope.page
		$scope.current_page = $scope.page

	$scope.pageChanged = ->
		$rootScope.frontend_loading = true
		$rootScope.archives = []
		loadArchives $scope.current_page
		paginate('archives', $scope.current_page)

	loadArchives = (page) ->
		params = '?page=' + page

		$http.get "api/archives#{ params }"
		.then (response) ->
			$scope.data = response.data.data
			$scope.archives = response.data.data.data
			$scope.counts = response.data.counts
			$rootScope.frontend_loading = false
			refreshCounts()
