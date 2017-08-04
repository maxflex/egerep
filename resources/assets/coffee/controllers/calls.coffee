angular
.module 'Egerep'
.controller 'CallsIndex', ($rootScope, $scope, $timeout, $http, UserService, LogTypes) ->
    bindArguments($scope, arguments)
    $rootScope.frontend_loading = true

    # $scope.refreshCounts = ->
    #     $timeout ->
    #         $('.selectpicker option').each (index, el) ->
    #             $(el).data 'subtext', $(el).attr 'data-subtext'
    #             $(el).data 'content', $(el).attr 'data-content'
    #         $('.selectpicker').selectpicker 'refresh'
    #     , 100


    $scope.formatTimestamp = (timestamp) -> moment.unix(timestamp).format('DD.MM.YY HH:mm')

    $scope.callDuration = (call) ->
        return '' if call.answer is '0'
        seconds = call.finish - call.answer
        format = 's сек'
        format = 'm мин ' + format if seconds > 60
        format = 'H час ' + format if seconds > 3600
        # format = if minutes >= 60 then 'H час m мин' else 'm мин'
        moment.duration(seconds, 'seconds').format(format)

    $scope.filter = ->
        # $.cookie("calls", JSON.stringify($scope.search), { expires: 365, path: '/' });
        $scope.current_page = 1
        $scope.pageChanged()

    $scope.keyFilter = (event) ->
        $scope.filter() if event.keyCode is 13

    $timeout ->
        # $scope.search = if $.cookie("logs") then JSON.parse($.cookie("logs")) else {}
        load $scope.page
        $scope.current_page = $scope.page

    $scope.pageChanged = ->
        $rootScope.frontend_loading = true
        load $scope.current_page
        paginate('calls', $scope.current_page)


    load = (page) ->
        params = '?page=' + page

        $http.get "api/calls#{ params }"
        .then (response) ->
            console.log response
            # $scope.counts = response.data.counts
            $scope.data = response.data.data
            $scope.calls = response.data.data.data
            $rootScope.frontend_loading = false
            # $scope.refreshCounts()
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