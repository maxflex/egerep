angular.module 'Egerep'
    .controller 'AddToList', ($scope, Genders, Grades, Subjects, TutorStates, Destinations, TutorService, PhoneService, RequestList) ->
        bindArguments($scope, arguments)

        # transparent marker opacity
        TRANSPARENT_MARKER = 0.3

        angular.element(document).ready ->
            $scope.list = new RequestList($scope.list)

        $scope.find = ->
            TutorService.getFiltered $scope.search
                .then (response) ->
                    $scope.tutors = response.data
                    showTutorsOnMap()
                    findIntersectingMetros()
                    repaintChosen()

        showTutorsOnMap = ->
            unsetAllMarkers()
            $scope.marker_id = 1

            # временный список репетиторов
            $scope.tutor_list = []

            $scope.markers = []
            $scope.tutors.forEach (tutor) ->
                console.log tutor.markers
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

        showClientOnMap = ->
            $scope.client.markers.forEach (marker) ->
                # Создаем маркер
                new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, 'white')
                new_marker.metros = marker.metros
                new_marker.setMap($scope.map)

        unsetAllMarkers = ->
            return if $scope.markers is undefined
            $scope.markers.forEach (marker) ->
                marker.setMap null

        findIntersectingMetros = ->
            if $scope.search.destination is 'r_k'
                # find intersecting markers
                $scope.markers.forEach (marker) ->
                    marker.intersecting = false
                    $scope.client.markers.forEach (client_marker) ->
                        client_marker.metros.forEach (client_metro) ->
                            if client_metro.station_id.toString() in marker.tutor.svg_map
                                marker.intersecting = true
                                return
                    # marker.metros.forEach (metro) ->
                    #     return if marker.intersecting
                    #     $scope.client.markers.forEach (client_marker) ->
                    #         client_marker.metros.forEach (client_metro) ->
                    #             if client_metro.station_id is metro.station_id
                    #                 marker.intersecting = true
                # paint non-intersecting with half opacity
                $scope.markers.forEach (marker) ->
                    marker.setOpacity(TRANSPARENT_MARKER) if not marker.intersecting



        bindTutorMarkerEvents = (marker) ->
            google.maps.event.addListener marker, 'click', (event) ->
                if marker.tutor in $scope.tutor_list
                    $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id)
                else
                    $scope.hovered_tutor = null
                    $scope.tutor_list.push marker.tutor
                $scope.$apply()

            google.maps.event.addListener marker, 'mouseover', (event) ->
                return if marker.tutor in $scope.tutor_list
                $scope.hovered_tutor = marker.tutor
                $scope.$apply()

            google.maps.event.addListener marker, 'mouseout', (event) ->
                $scope.hovered_tutor = null
                $scope.$apply()

            google.maps.event.addListener marker, 'dblclick', (event) ->
                tutor_id = parseInt(marker.tutor.id)
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

            showClientOnMap()
