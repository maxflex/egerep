angular
    .module 'Egerep'
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

            $scope.chart = new Chart document.getElementById('graph').getContext('2d'),
                type: 'line'
                options:
                    scales:
                        xAxes: [
                            type: 'time'
                            time:
                                displayFormats:
                                    minute: 'HH:mm'
                                    hour: 'MM.DD HH:00'
                                    millisecond: 'MM.DD HH:00'
                        ]
                        yAxes: [
                            ticks:
                                beginAtZero: true
                                userCallback: (label, index, labels) -> return label if Math.floor(label) is label
                            display: true,
                            scaleLabel:
                                display: true
                                labelString: 'действий'
                        ]

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            load $scope.current_page
            paginate('logs', $scope.current_page)

        $scope.showGraph = ->
            $rootScope.dialog('log-graph')
            $http.get('api/logs/graph').then (response) ->
                console.log(response)
                $timeout ->
                    $scope.chart.data.labels    = response.data.labels
                    $scope.chart.data.datasets  = [response.data.datasets]
                    $scope.chart.update()
                # response.data.forEach (d) ->
                #     data.push
                #         date: moment(d).toDate()
                #         value: 1
                # MG.data_graphic
                #     chart_type: 'histogram'
                #     data: data
                #     full_width: true,
                #     height: 300,
                #     target: '#graph',
                #     x_accessor: 'date',
                #     y_accessor: 'value'

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
