angular
.module 'Egerep'
.config (calendarConfig) ->
	calendarConfig.i18nStrings.weekNumber = ''
.controller 'VocationsIndex', ($rootScope, $scope, $timeout, $http, Vocation, UserService) ->
	bindArguments($scope, arguments)
	$scope.calendarView = 'month'
	$scope.calendarDate = moment().toDate()

	$scope.getTitle = ->
		moment($scope.calendarDate).format 'MMMM YYYY'

	$scope.toggleVocation = (calendarDate, calendarCell) ->
		return if $scope.show
		endsAt = moment(calendarDate).add(10, 'hours').toDate()
		startsAt = moment(calendarDate).add(19, 'hours').toDate()
		# try to delete existing first
		index = false
		vocation = _.find $scope.vocation.data, (e, i) ->
			index = i
			(moment(calendarDate).format('YYYY-MM-DD') is moment(e.startsAt).format('YYYY-MM-DD')) and (e.user_id == $scope.user.id)
		if vocation isnt undefined
			$scope.vocation.data.splice(index, 1)
		else
			$scope.vocation.data.push
				title: $scope.user.login
				color:
					primary: $scope.user.color
				startsAt: endsAt
				endsAt: startsAt
				user_id: $scope.user.id

	$scope.chooseTime = (calendarEvent) ->
		return if $scope.show
		return if calendarEvent.user_id != $scope.user.id
		$scope.d = calendarEvent
		$('#choose-time').modal 'show'
		return false

	$scope.editTime = ->
		$('#choose-time').modal 'hide'
		return false

	$scope.create = ->
		ajaxStart()
		$scope.saving = true
		Vocation.save $scope.vocation, (response) ->
			ajaxEnd()
			redirect "vocations/#{response.id}"

	$scope.edit = ->
		ajaxStart()
		$scope.saving = true
		Vocation.update
			id: $scope.vocation.id
		, $scope.vocation, ->
			ajaxEnd()
			$scope.saving = false

	$scope.getApprovedUsers = (v) ->
		v.approved_by.split(',').map(Number)

	$scope.approved = (user_id) ->
		user_id.toString() in $scope.vocation.approved_by

	$scope.approve = (user_id) ->
		return if user_id != $scope.user.id
		user_id = user_id.toString()
		if $scope.approved(user_id)
			$scope.vocation.approved_by = _.reject $scope.vocation.approved_by, (e) ->
				e == user_id
		else
			$scope.vocation.approved_by.push user_id

	$scope.remove = ->
		bootbox.confirm "Вы уверены, что хотите удалить заявку ##{$scope.vocation.id}?", (result) ->
			if result is true
				ajaxStart()
				Vocation.delete {id: $scope.vocation.id}, ->
					redirect 'vocations'
