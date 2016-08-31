angular
.module 'Egerep'
.controller 'NotificationsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, Approved) ->
	bindArguments($scope, arguments)
	$rootScope.frontend_loading = true

	$scope.addDays = (date, days) ->
		moment(date).add({day : days})

	$scope.pastDate = (date) ->
		if date and (Date.now() - new Date(date)) >= 0 then true else false

	refreshCounts = ->
		$timeout ->
			$('.selectpicker option').each (index, el) ->
				$(el).data 'subtext', $(el).attr 'data-subtext'
				$(el).data 'content', $(el).attr 'data-content'
			$('.selectpicker').selectpicker 'refresh'

			$('.attachment-filters button').css 'background', 'none'
			$('.attachment-filters select > option[value!=""]:selected').parent('select').siblings('button').css('background', '#dceee5')
		, 100

	$scope.filter = ->
		$.cookie("notifications", JSON.stringify($scope.search), { expires: 365, path: '/' });
		$scope.current_page = 1
		$scope.pageChanged()

	$scope.changeState = (state_id) ->
		$rootScope.frontend_loading = true
		$scope.attachments = []
		$scope.current_page = 1
		loadAttachments($scope.current_page)
		window.history.pushState(state_id, '', 'notifications/' + state_id.toLowerCase());

	$timeout ->
		$scope.search = if $.cookie("notifications") then JSON.parse($.cookie("notifications")) else {}
		loadAttachments $scope.page
		$scope.current_page = $scope.page

	$scope.pageChanged = ->
		$rootScope.frontend_loading = true
		$rootScope.attachments = []
		loadAttachments $scope.current_page
		paginate('notifications', $scope.current_page)

	loadAttachments = (page) ->
		params = '?page=' + page

		$http.get "api/notifications/get#{ params }"
		.then (response) ->
			$scope.data = response.data.data
			$scope.attachments = response.data.data.data
			$scope.counts = response.data.counts
			$rootScope.frontend_loading = false
			refreshCounts()
