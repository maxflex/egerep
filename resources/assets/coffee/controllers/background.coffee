angular
.module 'Egerep'
.controller 'Background', ($scope, $timeout, Background, UnderModer) ->
	bindArguments($scope, arguments)

	$scope.editBackgroundModal = (date) ->
		$scope.modal_background = _.clone($scope.backgrounds[date])
		$('#edit-background').modal('show')

	$scope.editBackground = ->
		$.each $scope.backgrounds, (date, background) ->
			if background.id == $scope.modal_background.id
				$scope.backgrounds[date] = $scope.modal_background
				return
		Background.update({id: $scope.modal_background.id}, $scope.modal_background)
		$('#edit-background').modal('hide')
		return

	$scope.loadImage = (date) ->
		$scope.date = date
		$('#fileupload').fileupload
			formData:
				date: date
		$('#fileupload').click()
		return

	$scope.remove = (date) ->
		Background.delete({id: $scope.backgrounds[date].id})
		delete $scope.backgrounds[date]

	$scope.pageChanged = ->
		ajaxStart()
		redirect("background?page=#{$scope.current_page}")

	$timeout ->
		$scope.today_date = moment().format("YYYY-MM-DD")

		# загрузка файла договора
		$('#fileupload').fileupload
			maxFileSize: 10000000, # 10 MB
			send: ->
				NProgress.configure({ showSpinner: true })
			,
			# во время загрузки
			progress: (e, data) ->
				NProgress.set(data.loaded / data.total)
			,
			# всегда по окончании загрузки (неважно, ошибка или успех)
			always: ->
				NProgress.configure({ showSpinner: false })
				ajaxEnd()
			,
			done: (i, response) ->
				if response.result.hasOwnProperty('error')
					notifyError(response.result.error)
					return
				$scope.backgrounds[$scope.date] = response.result
				$scope.$apply()
