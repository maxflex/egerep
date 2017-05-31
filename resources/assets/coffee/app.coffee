angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap', 'angular-ladda', 'svgmap'])
    .config [
        '$compileProvider'
        ($compileProvider) ->
            $compileProvider.aHrefSanitizationWhitelist /^\s*(https?|ftp|mailto|chrome-extension|sip):/
	]
    .filter 'cut', ->
        (value, wordwise, max, nothing = '', tail = '…') ->
            return nothing if !value or value is ''
            max = parseInt(max, 10)
            return value if !max
            return value if value.length <= max
            value = value.substr(0, max)
            if wordwise
                lastspace = value.lastIndexOf(' ')
                if lastspace != -1
                    #Also remove . and , so its gives a cleaner result.
                    if value.charAt(lastspace - 1) == '.' or value.charAt(lastspace - 1) == ','
                        lastspace = lastspace - 1
                    value = value.substr(0, lastspace)
            value + tail
    .filter 'hideZero', ->
        (item) ->
            if item > 0 then item else null
    .run ($rootScope, $q, PusherService) ->
        PusherService.bind 'IncomingRequest', (data) ->
            request_count = $('#request-count')
            request_counter = $('#request-counter')
            animate_speed = 7000
            request_counter.removeClass('text-success').removeClass('text-danger').css('opacity', 1)
            if data.delete
                request_count.text(parseInt(request_count.text()) - 1)
                request_count.animate({'background-color': '#158E51'}, animate_speed / 2).animate({'background-color': '#777'}, animate_speed / 2)
                request_counter.text('-1').addClass('text-success').animate({opacity: 0}, animate_speed)
            else
                request_count.text(parseInt(request_count.text()) + 1)
                request_count.animate({'background-color': '#A94442'}, animate_speed / 2).animate({'background-color': '#777'}, animate_speed / 2)
                request_counter.text('+1').addClass('text-danger').animate({opacity: 0}, animate_speed)

        PusherService.bind 'AttachmentCountChanged', (data) ->
            attachment_count   = $('#attachment-count')
            attachment_counter = $('#attachment-counter')
            animate_speed = 7000
            attachment_counter.removeClass('text-success').removeClass('text-danger').css('opacity', 1)
            if data.delete
                attachment_count.text(parseInt(attachment_count.text()) - 1)
                attachment_count.animate({'background-color': '#A94442'}, animate_speed / 2).animate({'background-color': '#777'}, animate_speed / 2)
                attachment_counter.text('-1').addClass('text-danger').animate({opacity: 0}, animate_speed)
            else
                attachment_count.text(parseInt(attachment_count.text()) + 1)
                attachment_count.animate({'background-color': '#158E51'}, animate_speed / 2).animate({'background-color': '#777'}, animate_speed / 2)
                attachment_counter.text('+1').addClass('text-success').animate({opacity: 0}, animate_speed)

        # отвечает за загрузку данных
        $rootScope.dataLoaded = $q.defer()
        # конец анимации front-end загрузки и rebind маск
        $rootScope.frontendStop = (rebind_masks = true) ->
            $rootScope.frontend_loading = false
            $rootScope.dataLoaded.resolve(true)
            rebindMasks() if rebind_masks

        $rootScope.range = (min, max, step) ->
          step = step or 1
          input = []
          i = min
          while i <= max
            input.push i
            i += step
          input

          # skip_values – какие значения в enum пропускать
          # allowed – пользователю рарзешено выбирать значения из [skip_values]
          # recursion – функция была запущена рекурсивно (внизу)
        $rootScope.toggleEnum = (ngModel, status, ngEnum, skip_values = [], allowed = [], recursion = false) ->
            # если установлено значение, которое пропускается для обычных пользователей,
            # то запрещать его смену
            return if not recursion and (parseInt(ngModel[status]) in skip_values or (isNaN(parseInt(ngModel[status])) and skip_values.indexOf(ngModel[status]) isnt -1)) and not allowed

            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status].toString()
            status_id++
            status_id = 0 if status_id > (statuses.length - 1)
            ngModel[status] = statuses[status_id]
            # if in skip_values
            $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed, true) if ((isNaN(parseInt(ngModel[status])) and skip_values.indexOf(ngModel[status]) isnt -1) or status_id in skip_values) and not allowed

        # обновить + ждать ответа от сервера
        # раньше я неправильно понимал алгоритм, поэтому без restricted_fields,
        # freeze_restricted тоже можно было toggleить.
        # при необходимости можно менять и переделать как $scope.enumToggles
        $rootScope.toggleEnumServer = (ngModel, status, ngEnum, Resource, skip_values = [], restricted_fields = [], freeze_restricted = false) ->
            return if ngModel[status] in restricted_fields and freeze_restricted #если запрешено менять значение

            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status].toString()

            loop
                status_id++
                status_id = 0 if status_id > (statuses.length - 1)
                value = if isNaN parseInt ngModel[status] then statuses[status_id] else status_id
                unless value in skip_values or value in restricted_fields
                    break

            update_data = {id: ngModel.id}
            update_data[status] = value

            Resource.update update_data, ->
                ngModel[status] = value

        $rootScope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $rootScope.formatDate = (date, full_year = false) ->
            return '' if not date
            moment(date).format "DD.MM.YY" + (if full_year then "YY" else "")

        $rootScope.shortenYear = (date) ->
            return '' if not date
            # 11.12.2015 => 11.12.15
            # 11-12-2016 => 11.12.16
            date.replace /(\d{2}[\-\.]{1}\d{2}[\-\.]{1})\d{2}(\d{2})/, '$1$2'


        $rootScope.formatTimestamp = (timestamp, full_year = false) ->
            timestamp = +(timestamp + '000') if typeof timestamp is 'string'
            return '' if not timestamp
            moment(timestamp).format "DD.MM.YY в HH:mm" + (if full_year then "YY" else "")

        $rootScope.dialog = (id) ->
            $("##{id}").modal 'show'
            return

        $rootScope.closeDialog = (id) ->
            $("##{id}").modal 'hide'
            return

        $rootScope.ajaxStart = ->
            ajaxStart()
            $rootScope.saving = true

        $rootScope.ajaxEnd = ->
            ajaxEnd()
            $rootScope.saving = false

        $rootScope.findById = (object, id) ->
            _.findWhere(object, {id: parseInt(id)})

        # prop2 – второй уровень вложенности
        $rootScope.total = (array, prop, prop2 = false) ->
            sum = 0
            $.each array, (index, value) ->
                v = value[prop]
                v = v[prop2] if prop2
                sum += v
            sum

        $rootScope.deny = (ngModel, prop) ->
            ngModel[prop] = +(!ngModel[prop])

        $rootScope.formatBytes = (bytes) ->
          if bytes < 1024
            bytes + ' Bytes'
          else if bytes < 1048576
            (bytes / 1024).toFixed(1) + ' KB'
          else if bytes < 1073741824
            (bytes / 1048576).toFixed(1) + ' MB'
          else
            (bytes / 1073741824).toFixed(1) + ' GB'
