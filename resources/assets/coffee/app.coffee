angular.module("Egerep", ['ngSanitize', 'ngResource', 'ngMaterial', 'ngMap'])
    .run ($rootScope) ->
        $rootScope.laroute = laroute

        $rootScope.range = (min, max, step) ->
          step = step or 1
          input = []
          i = min
          while i <= max
            input.push i
            i += step
          input

        $rootScope.toggleEnum = (ngModel, status, ngEnum) ->
            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status]
            status_id++
            status_id = 0 if status_id > (statuses.length - 1)
            ngModel[status] = statuses[status_id]

        $rootScope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $rootScope.dialog = (id) ->
            $("##{id}").modal 'show'

        $rootScope.ajaxStart = ->
            ajaxStart()
            $rootScope.saving = true

        $rootScope.ajaxEnd = ->
            ajaxEnd()
            $rootScope.saving = false
