angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap', 'ngAnimate', 'ui.sortable', 'ui.bootstrap', 'angular-ladda'])
    .config [
        '$compileProvider'
        ($compileProvider) ->
            $compileProvider.aHrefSanitizationWhitelist /^\s*(https?|ftp|mailto|chrome-extension|sip):/
	]
    .run ($rootScope, $q) ->
        $rootScope.laroute = laroute

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
          # allowed_user_ids – пользователи, которым разрешено выбирать значения
          # recursion – функция была запущена рекурсивно (внизу)
        $rootScope.toggleEnum = (ngModel, status, ngEnum, skip_values = [], allowed_user_ids = [], recursion = false) ->
            # если установлено значение, которое пропускается для обычных пользователей,
            # то запрещать его смену
            return if not recursion and parseInt(ngModel[status]) in skip_values and $rootScope.$$childHead.user.id not in allowed_user_ids

            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status].toString()
            status_id++
            status_id = 0 if status_id > (statuses.length - 1)
            ngModel[status] = statuses[status_id]
            # if in skip_values
            $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed_user_ids, true) if status_id in skip_values and $rootScope.$$childHead.user.id not in allowed_user_ids

        $rootScope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $rootScope.formatDate = (date, full_year = false) ->
            return '' if not date
            moment(date).format "DD.MM.YY" + (if full_year then "YY" else "")

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

        $rootScope.formatBytes = (bytes) ->
          if bytes < 1024
            bytes + ' Bytes'
          else if bytes < 1048576
            (bytes / 1024).toFixed(1) + ' KB'
          else if bytes < 1073741824
            (bytes / 1048576).toFixed(1) + ' MB'
          else
            (bytes / 1073741824).toFixed(1) + ' GB'
