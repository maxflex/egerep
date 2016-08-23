angular.module 'Egerep'
    .controller 'TutorsSelect', ($scope, Genders, Grades, Subjects, TutorStates, Destinations, TutorService, PhoneService, RequestList, YesNo, Tutor) ->
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

        $scope.tutors = []
        $scope.list_tutor_ids = []

        angular.element(document).ready ->
            $scope.list = new RequestList($scope.list)
            $('.map-tutor-list').droppable()

        $scope.blurComment = (tutor) ->
            tutor.is_being_commented = false
            tutor.list_comment = tutor.old_list_comment

        $scope.focusComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_list_comment = tutor.list_comment

        $scope.startComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_list_comment = tutor.list_comment
            $timeout ->
                $("#list-comment-#{tutor.id}").focus()

        $scope.saveComment =  (event, tutor) ->
            if event.keyCode is 13
                Tutor.update
                    id: tutor.id
                    list_comment: tutor.list_comment
                , (response) ->
                    tutor.old_list_comment = tutor.list_comment
                    $(event.target).blur()

        $scope.getHours = (minutes) ->
            Math.floor(minutes / 60)

        $scope.getMinutes = (minutes) ->
            minutes % 60

        $scope.find = ->
            $scope.loading = true
            TutorService.select {search: $scope.search}
                .then (response) ->
                    $scope.tutors = response.data
                    showTutorsOnMap()
                    repaintChosen()
                    $scope.loading = false

        # determine whether tutor had already been added
        $scope.added = (tutor_id) ->
            tutor_id in $scope.list_tutor_ids.map(Number)

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
                if tutor.markers isnt undefined
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

        unsetAllMarkers = ->
            # unset markers
            if $scope.markers isnt undefined
                $scope.markers.forEach (marker) ->
                    marker.setMap null
            # unset clusterer
            if markerClusterer isnt undefined
                markerClusterer.clearMarkers()

        bindTutorMarkerEvents = (marker) ->
            # double click custom handler with delay
            google.maps.event.addListener marker, 'click', (event) ->
                clicks++
                if clicks is 1
                    setTimeout ->
                        if clicks is 1
                            # single click
                            if marker.tutor in $scope.tutor_list
                                $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id)
                            else
                                $scope.hovered_tutor = null
                                $scope.tutor_list.push marker.tutor
                            $scope.$apply()
                            rebindDraggable()
                        else
                            # double click
                            $scope.addOrRemove(marker.tutor.id)
                        clicks = 0
                    , 250

            google.maps.event.addListener marker, 'dblclick', (event) ->
                clicks++

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
            if tutor_id in $scope.list_tutor_ids.map(Number)
                $scope.list_tutor_ids = _.without($scope.list_tutor_ids.map(Number), tutor_id)
            else
                $scope.list_tutor_ids.push(tutor_id)
            repaintChosen()
            $scope.list.$update()

        repaintChosen = ->
            $scope.markers.forEach (marker) ->
                if marker.tutor.id in $scope.list_tutor_ids.map(Number) and not marker.chosen
                    marker.chosen = true
                    marker.setOpacity(1)
                    marker.setIcon ICON_BLUE
                if marker.tutor.id not in $scope.list_tutor_ids.map(Number) and marker.chosen
                    marker.chosen = false
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
