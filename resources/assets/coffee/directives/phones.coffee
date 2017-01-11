angular.module('Egerep').directive 'phones', ->
    restrict: 'E'
    templateUrl: 'directives/phones'
    scope:
        entity: '='
    controller: ($scope, $timeout, $rootScope, PhoneService, UserService, $interval) ->
        $scope.PhoneService = PhoneService
        $scope.UserService  = UserService

        $scope.is_playing_stage = 'stop'
        $scope.isOpened = false;

#console.log $scope.entityType

        # level depth
        $rootScope.dataLoaded.promise.then (data) ->
            $scope.level = if $scope.entity.phones and $scope.entity.phones.length then $scope.entity.phones.length else 1

        $scope.nextLevel = ->
            $scope.level++

        $scope.phoneMaskControl = (event) ->
            el = $(event.target)
            # grabs string phone_2 from object model.phone2
            # so it can be accessible by key
            phone_id = el.attr('ng-model').split('.')[1]
            $scope.entity[phone_id] = $(event.target).val()

        $scope.isFull = (number) ->
            return false if number is undefined or number is ""
            !number.match(/_/)

        # отправить смс
        $scope.sms = (number) ->
            $('#sms-modal').modal 'show'
            $rootScope.sms_number = number

        # информация по api
        $scope.info = (number) ->

            $scope.api_number = number
            $scope.mango_info = null
            $('#api-phone-info').modal 'show'
            if $scope.isOpened == false
                $('#api-phone-info')
                    .on 'hidden.bs.modal', () ->
                        $scope.isOpened = true
                        if $scope.audio
                            $scope.audio.pause()
                            $scope.audio = null
                            $scope.is_playing_stage = 'stop'
                            $scope.is_playing = null

            PhoneService.info(number).then (response) ->
                ##console.log response.data
                $scope.mango_info = response.data

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.time = (seconds) ->
            moment(0).seconds(seconds).format("mm:ss")

        $scope.getNumberTitle = (number) ->
            #console.log number, $scope.api_number
            return 'текущий номер' if number is PhoneService.clean($scope.api_number)
            number

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
