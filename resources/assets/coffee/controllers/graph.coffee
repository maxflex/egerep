angular
    .module 'Egerep'
    .controller 'GraphController', ($scope, $timeout, $http, $rootScope, SvgMap) ->
        bindArguments($scope, arguments)

        $scope.map_loaded = false

        angular.element(document).ready ->
            $timeout ->
                SvgMap.show()

                SvgMap.el().find('#stations > g > g').each (index, el) ->
                    $(el).on 'mouseenter', ->
                        $scope.hovered_station_id = parseInt($(@).attr('id').replace(/[^\d]/g, ''))
                        $scope.$apply()
                    $(el).on 'mouseleave', ->
                        $scope.hovered_station_id = undefined
                        $scope.$apply()

                SvgMap.map.options.clickCallback = (id) ->
                    if SvgMap.map.getSelected().length > 2
                        SvgMap.map.deselectAll()
                        SvgMap.map.select id
                    $scope.selected = SvgMap.map.getSelected()

                $scope.map_loaded = true
            , 500

        $scope.$watch 'selected', (newVal, oldVal) ->
            return if newVal is undefined
            if newVal.length is 2
                $scope.new_distance = getDistance(newVal[0], newVal[1])

        $scope.$watch 'hovered_station_id', (newVal, oldVal) ->
            if newVal isnt undefined
                found_distances = _.filter $scope.distances, (distance) ->
                    distance.from is newVal or distance.to is newVal

                # 1. objects in js are copied by ref
                # 2. _.clone - shallow copy
                $scope.found_distances = _.map found_distances, _.clone

                angular.forEach $scope.found_distances, (distance) ->
                     if distance.from isnt newVal
                         from_buffer = distance.from
                         distance.from = newVal
                         distance.to = from_buffer


        $scope.save = ->
            from = $scope.selected[0]
            to = $scope.selected[1]
            $rootScope.ajaxStart()
            $http.post 'graph/save',
                from: from
                to: to
                distance: $scope.new_distance
            .then ->
                distance = getDistanceObject(from, to)
                if distance is undefined
                    $scope.distances.push
                        from: from
                        to: to
                        distance: $scope.new_distance
                else
                    distance.distance = $scope.new_distance
                $rootScope.ajaxEnd()


        $scope.delete = ->
            from = Math.min($scope.selected[0], $scope.selected[1])
            to = Math.max($scope.selected[0], $scope.selected[1])

            $rootScope.ajaxStart()
            $http.post 'graph/delete',
                from: from
                to: to
            .then ->
                $rootScope.ajaxEnd()
                $scope.distances = _.without($scope.distances, _.findWhere($scope.distances, {from: from, to: to}))
                $scope.selected = []
                SvgMap.map.deselectAll()

        getDistance = (from, to) ->
            distance = getDistanceObject(from, to)
            if distance is undefined then undefined else distance.distance

        getDistanceObject = (from, to) ->
            from = Math.min(from, to)
            to = Math.max(from, to)
            _.find($scope.distances, {from: from, to: to})
