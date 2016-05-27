angular
    .module 'Egerep'

    #
    #   LIST CONTROLLER
    #
    .controller "DebtIndex", ($rootScope, $scope, $timeout, $http, Tutor) ->

        $rootScope.frontend_loading = true

        $timeout ->
            loadPage $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadPage $scope.current_page
            paginate('debt', $scope.current_page)

        loadPage = (page) ->
            params = '?page=' + page

            $http.get "api/debt#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.tutors = $scope.data.data
                console.log $scope.tutors

        $scope.blurComment = (tutor) ->
            tutor.is_being_commented = false
            tutor.debt_comment = tutor.old_debt_comment

        $scope.focusComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment

        $scope.startComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment
            $timeout ->
                $("#list-comment-#{tutor.id}").focus()

        $scope.saveComment =  (event, tutor) ->
            if event.keyCode is 13
                Tutor.update
                    id: tutor.id
                    debt_comment: tutor.debt_comment
                , (response) ->
                    tutor.old_debt_comment = tutor.debt_comment
                    $(event.target).blur()

    .controller 'DebtMap', ($scope, $timeout, TutorService, Tutor) ->
        bindArguments($scope, arguments)

        # transparent marker opacity
        TRANSPARENT_MARKER = 0.3

        # differentiate single & double click
        clicks = 0

        # marker clusterer
        markerClusterer = undefined

        # mode: 'map' | 'list'
        $scope.mode = 'map'

        # loading map
        $scope.loading = false

        # search params
        $scope.search = {}

        $scope.blurComment = (tutor) ->
            tutor.is_being_commented = false
            tutor.debt_comment = tutor.old_debt_comment

        $scope.focusComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment

        $scope.startComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment
            $timeout ->
                $("#list-comment-#{tutor.id}").focus()

        $scope.saveComment =  (event, tutor) ->
            if event.keyCode is 13
                Tutor.update
                    id: tutor.id
                    debt_comment: tutor.debt_comment
                , (response) ->
                    tutor.old_debt_comment = tutor.debt_comment
                    $(event.target).blur()

        angular.element(document).ready ->
            $('.map-tutor-list').droppable()

        $scope.find = ->
            $scope.loading = true
            TutorService.getDebtMap {search: $scope.search}
                .then (response) ->
                    $scope.tutors = response.data
                    showTutorsOnMap()
                    $scope.loading = false

        # determine whether tutor had already been added
        $scope.added = (tutor_id) ->
            tutor_id in $scope.list.tutor_ids

        # rebind draggable
        rebindDraggable = ->
            $('.temporary-tutor').draggable
                containment: 'window'
                revert: (valid) ->
                    return true if valid
                    $scope.tutor_list = removeById($scope.tutor_list, $scope.dragging_tutor.id)
                    $scope.$apply()

        # remember dragging tutor
        $scope.startDragging = (tutor) ->
            $scope.dragging_tutor = tutor

        showTutorsOnMap = ->
            unsetAllMarkers()
            $scope.marker_id = 1

            # временный список репетиторов
            $scope.tutor_list = []

            $scope.markers = []
            $scope.tutors.forEach (tutor) ->
                tutor.markers.forEach (marker) ->
                    # Создаем маркер
                    new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type)
                    new_marker.metros = marker.metros
                    new_marker.tutor = tutor

                    # Добавляем маркер на карту
                    new_marker.setMap($scope.map)

                    # Добавляем ивент удаления маркера
                    bindTutorMarkerEvents(new_marker)
                    $scope.markers.push new_marker
            # @todo: consider using Marker Clusterer
            markerClusterer = new MarkerClusterer $scope.map, $scope.markers,
                gridSize: 10
                # maxZoom: 12
                imagePath: 'img/maps/clusterer/m'

        showClientOnMap = ->
            $scope.client.markers.forEach (marker) ->
                # Создаем маркер
                new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, 'white')
                new_marker.metros = marker.metros
                new_marker.setMap($scope.map)

        unsetAllMarkers = ->
            # unset markers
            if $scope.markers isnt undefined
                $scope.markers.forEach (marker) ->
                    marker.setMap null
            # unset clusterer
            if markerClusterer isnt undefined
                markerClusterer.clearMarkers()

        findIntersectingMetros = ->
            if $scope.search.destination is 'r_k'
                # find intersecting markers
                $scope.markers.forEach (marker) ->
                    marker.intersecting = false
                    $scope.client.markers.forEach (client_marker) ->
                        client_marker.metros.forEach (client_metro) ->
                            if client_metro.station_id.toString() in marker.tutor.svg_map
                                marker.intersecting = true
                                marker.tutor.intersecting = true
                                return
                # paint non-intersecting with half opacity
                $scope.markers.forEach (marker) ->
                    marker.setOpacity(TRANSPARENT_MARKER) if not marker.intersecting

        # получить репетиторов, которые выезжают на ближайшую станцию метро клиента
        $scope.intersectingTutors = ->
            _.where($scope.tutors, { intersecting: true })

        # получить репетиторов, которые НЕ доезжают до ближайшей станции
        $scope.notIntersectingTutors = ->
            _.filter $scope.tutors, (tutor) ->
                _.isUndefined(tutor.intersecting)


        bindTutorMarkerEvents = (marker) ->
            # double click custom handler with delay
            google.maps.event.addListener marker, 'click', (event) ->
                # single click
                if marker.tutor in $scope.tutor_list
                    $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id)
                else
                    $scope.hovered_tutor = null
                    $scope.tutor_list.push marker.tutor
                $scope.$apply()
                rebindDraggable()

            google.maps.event.addListener marker, 'mouseover', (event) ->
                return if marker.tutor in $scope.tutor_list
                $scope.hovered_tutor = marker.tutor
                $scope.$apply()

            google.maps.event.addListener marker, 'mouseout', (event) ->
                $scope.hovered_tutor = null
                $scope.$apply()

        # add or remove tutor from list
        $scope.addOrRemove = (tutor_id) ->
            tutor_id = parseInt(tutor_id)
            if tutor_id in $scope.list.tutor_ids
                $scope.list.tutor_ids = _.without($scope.list.tutor_ids, tutor_id)
            else
                $scope.list.tutor_ids.push(tutor_id)
            repaintChosen()
            $scope.list.$update()

        repaintChosen = ->
            $scope.markers.forEach (marker) ->
                if marker.tutor.id in $scope.list.tutor_ids and not marker.chosen
                    marker.chosen = true
                    marker.setOpacity(1)
                    marker.setIcon ICON_BLUE
                if marker.tutor.id not in $scope.list.tutor_ids and marker.chosen
                    marker.chosen = false
                    marker.setOpacity if marker.intersecting then 1 else TRANSPARENT_MARKER
                    marker.setIcon getMarkerType(marker.type)


        $scope.$on 'mapInitialized', (event, map) ->
            # Запоминаем карту после инициалицации
            $scope.gmap = map

            # generate recommended search bounds
            INIT_COORDS =
                lat: 55.7387
                lng: 37.6032
            $scope.RECOM_BOUNDS = new (google.maps.LatLngBounds)(new (google.maps.LatLng)(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new (google.maps.LatLng)(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5))
            $scope.geocoder = new (google.maps.Geocoder)

            # Зум и центр карты по умолчанию
            $scope.gmap.setCenter new (google.maps.LatLng)(55.7387, 37.6032)
            $scope.gmap.setZoom 11
