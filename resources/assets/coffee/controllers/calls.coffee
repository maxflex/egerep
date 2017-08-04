angular
.module 'Egerep'
.controller 'CallsIndex', ($rootScope, $scope, $timeout, $http, UserService, CallStatuses, $interval) ->
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
        $scope.search = {}
        load $scope.page
        $scope.current_page = $scope.page

    $scope.pageChanged = ->
        $rootScope.frontend_loading = true
        load $scope.current_page
        paginate('calls', $scope.current_page)


    load = (page) ->
        params = _.clone($scope.search)
        params.page = page

        $http.get "api/calls?" + $.param(params)
        .then (response) ->
            console.log response
            # $scope.counts = response.data.counts
            $scope.data = response.data.data
            $scope.calls = response.data.data.data
            $rootScope.frontend_loading = false
            # $scope.refreshCounts()

    recodringLink = (recording_id) ->
        api_key   = 'goea67jyo7i63nf4xdtjn59npnfcee5l'
        api_salt  = 't9mp7vdltmhn0nhnq0x4vwha9ncdr8pa'
        timestamp = moment().add(5, 'minute').unix()

        sha256 = new jsSHA('SHA-256', 'TEXT')
        sha256.update(api_key + timestamp + recording_id + api_salt)
        sign = sha256.getHash('HEX')

        return "https://app.mango-office.ru/vpbx/queries/recording/link/#{recording_id}/play/#{api_key}/#{timestamp}/#{sign}"

    $scope.intervalStart = () ->
        $scope.interval = $interval ->
          if $scope.audio
              $scope.current_time = angular.copy $scope.audio.currentTime
              $scope.prc = (($scope.current_time * 100) /  $scope.audio.duration).toFixed(2)
              $scope.stop() if parseInt($scope.prc) == 100
        , 10

    $scope.intervalCancel = () ->
        $interval.cancel $scope.interval

    # инициализируем аудио
    $scope.initAudio = (recording_id) ->
        $scope.stop() if $scope.is_playing
        $scope.audio = new Audio recodringLink(recording_id)
        $scope.current_time = 0
        $scope.prc = 0
        $scope.is_playing_stage = 'start'
        $scope.is_playing = recording_id

    # ставим на паузу
    $scope.pause = ->
        $scope.intervalCancel()
        $scope.audio.pause() if $scope.audio
        $scope.is_playing_stage = 'pause'

    # воспроизводим звук
    $scope.play = (recording_id) ->
        $scope.initAudio(recording_id) if not $scope.isPlaying(recording_id)
        if $scope.is_playing_stage is 'play'
            $scope.pause()
        else
            $scope.audio.play()
            $scope.is_playing_stage = 'play'
            $scope.intervalStart()

    # указатель воспроизведения
    $scope.isPlaying = (recording_id) ->
        $scope.is_playing is recording_id

    # полная остановка процесса воспроизведения
    $scope.stop = ->
        $scope.prc = 0
        $scope.is_playing = null
        $scope.audio.pause()
        $scope.audio = null
        $scope.is_playing_stage = 'stop'
        $scope.intervalCancel()

    # прокрутка
    $scope.setCurentTime = (e) ->
        width = angular.element e.target
                .width()
        $scope.prc = (e.offsetX * 100) / width;
        time = ($scope.audio.duration * $scope.prc) / 100
        $scope.audio.currentTime = time

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