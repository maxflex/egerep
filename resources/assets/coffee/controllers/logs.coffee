angular
    .module 'Egerep'
    .controller 'LogsGraph', ($rootScope, $scope, $timeout, $http, LogPeriods, UserService) ->
        bindArguments($scope, arguments)

        newDateString = (days) ->
            moment().add(days, 'd').toDate()

        $timeout ->
            $scope.search = {period: '1'}
            $timeout ->
                $('.selectpicker').selectpicker('refresh')
            , 300
            # $scope.search = {period: '3', user_ids: [69]}
            # $scope.filter()
            $scope.chart = new Chart document.getElementById("myChart").getContext('2d'),
                type: 'line'
                data:
                    datasets: []
                options:
                    tooltips:
                        callbacks:
                            title: (tooltipItem, data) ->
                                moment(tooltipItem[0].xLabel).format('HH:mm')
                    scales:
                        xAxes: [
                            type: 'time'
                            time:
                                displayFormats:
                                    unit: 'minute'
                                    minute: 'HH:mm'
                                    hour: 'MM.DD HH:00'
                                    millisecond: 'MM.DD HH:00'
                        ]
                        yAxes: [
                            ticks:
                                # stepSize: 1
                                beginAtZero: true
                            display: true,
                            scaleLabel:
                                display: true
                                labelString: 'действий'
                        ]


        $scope.filter = ->
            $scope.loading = true
            $http.get "api/logs/graph?" + $.param($scope.search)
            .then (response) ->
                console.log(response)
                $timeout ->
                    $scope.chart.options.tooltips.callbacks.title = (tooltipItem, data) ->
                        # если выбраны сутки, то по часам
                        moment(tooltipItem[0].xLabel).format(if $scope.search.period is '3' then 'MM.DD HH:00' else 'HH:mm')
                    $scope.chart.data.datasets = response.data
                    $scope.chart.update()
                $scope.loading = false


    .controller 'LogsIndex', ($rootScope, $scope, $timeout, $http, UserService, LogTypes) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.$watch 'search.table', (newVal, oldVal) ->
            $scope.search.column = null if ((newVal && oldVal) || (oldVal && not newVal))

        $scope.toJson = (data)->
            JSON.parse(data)

        $scope.refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'
            , 100

        $scope.filter = ->
            $.cookie("logs", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $scope.keyFilter = (event) ->
            $scope.filter() if event.keyCode is 13

        $timeout ->
            $scope.search = if $.cookie("logs") then JSON.parse($.cookie("logs")) else {}
            load $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('logs', $scope.current_page)

        load = (page) ->
            params = '?page=' + page

            $http.get "api/logs#{ params }"
            .then (response) ->
                console.log response
                $scope.counts = response.data.counts
                $scope.data = response.data.data
                $scope.logs = response.data.data.data
                $rootScope.frontend_loading = false
                $scope.refreshCounts()
