angular
    .module 'Egerep'
    .run ($rootScope) ->
        $rootScope.genders =
            male: 'Мужской'
            female: 'Женский'



    #
    #   LIST CONTROLLER
    #
    .controller "TutorsIndex", ($scope, $rootScope, $timeout, $http, Tutor) ->
        $rootScope.frontend_loading = true

        $http.get 'api/tutors'
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.tutors = $scope.data.data

        $scope.$watch 'current_page', (newVal, oldVal) ->
            return if newVal is undefined
            $http.get 'api/tutors?page=' + newVal
                .then (response) ->
                    $rootScope.frontendStop()
                    $scope.data = response.data
                    $scope.tutors = $scope.data.data


    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "TutorsForm", ($scope, $rootScope, $timeout, $interval, Tutor, SvgMap, Subjects, Grades, ApiService) ->
        $scope.SvgMap   = SvgMap
        $scope.Subjects = Subjects
        $scope.Grades   = Grades
        $rootScope.frontend_loading = true

        # get tutor
        $timeout ->
            if $scope.id > 0
                $scope.tutor = Tutor.get {id: $scope.id}, ->
                    $rootScope.frontendStop()

        # @todo: ЗАМЕНИТЬ НА ДИРЕКТИВУ <ng-select> (уже сделано, но глючная. надо доделать)
        # refresh selectpicker on update
        $scope.$watch 'tutor.subjects', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'tutor-subjects', 'предмет' if oldVal is undefined
            spRefresh 'tutor-subjects' if oldVal isnt undefined

        # refresh selectpicker on update
        $scope.$watch 'tutor.grades', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'tutor-grades', 'классы' if oldVal is undefined
            spRefresh 'tutor-grades' if oldVal isnt undefined

        $scope.svgSave = ->
            $scope.tutor.svg_map = SvgMap.save()

        $scope.yearDifference = (year) ->
            moment().format("YYYY") - year

        $scope.add = ->
            $scope.saving = true
            Tutor.save $scope.tutor, (tutor) ->
                window.location = laroute.route 'tutors.edit',
                    tutors: tutor.id

        $scope.edit = ->
            $scope.saving = true
            filterMarkers()

            $scope.tutor.$update()
                .then (response) ->
                    $scope.saving = false









        #
        # ПОСЛЕ ЗАГРУЗКИ КАРТЫ
        #
        $scope.marker_id = 1

        filterMarkers = ->
            new_markers = []
            $.each $scope.tutor.markers, (index, marker) ->
                new_markers.push _.pick(marker, 'lat', 'lng', 'type', 'metros')
            $scope.tutor.markers = new_markers

        $scope.$on 'mapInitialized', (event, map) ->
            # Запоминаем карту после инициалицации
            $scope.gmap = map
            # Добавляем существующие метки
            $scope.loadMarkers()
            # generate recommended search bounds
            INIT_COORDS =
                lat: 55.7387
                lng: 37.6032
            $scope.RECOM_BOUNDS = new (google.maps.LatLngBounds)(new (google.maps.LatLng)(INIT_COORDS.lat - 0.5, INIT_COORDS.lng - 0.5), new (google.maps.LatLng)(INIT_COORDS.lat + 0.5, INIT_COORDS.lng + 0.5))
            $scope.geocoder = new (google.maps.Geocoder)
            # События добавления меток
            google.maps.event.addListener map, 'click', (event) ->
                $scope.gmapAddMarker event

        # Показать карту
        $scope.showMap = ->
            $('#gmap-modal').modal 'show'
            # Показываем карту
            google.maps.event.trigger $scope.gmap, 'resize'

            # Зум и центр карты по умолчанию
            $scope.gmap.setCenter new (google.maps.LatLng)(55.7387, 37.6032)
            $scope.gmap.setZoom 11

            # Обнуляем значение поиска
            $('#map-search').val ''

            # Удаляем все маркеры поиска
            if $scope.search_markers and $scope.search_markers.length
                $.each $scope.search_markers, (i, marker) ->
                    marker.setMap null
                $scope.search_markers = []

            # Если уже есть добавленные маркеры
            if $scope.tutor.markers.length
                # отображать только метки с выбранным типом
                bounds = new (google.maps.LatLngBounds)
                # есть отображаемые маркеры
                markers_count = 0
                # отображаем маркеры по одному
                $.each $scope.tutor.markers, (index, marker) ->
                    markers_count++
                    # отображаемые маркеры есть
                    # marker.setVisible true
                    bounds.extend marker.position
                # если отображаемые маркеры есть, делаем зум на них
                if markers_count > 0
                    $scope.gmap.fitBounds bounds
                    $scope.gmap.panToBounds bounds
                    $scope.gmap.setZoom 11

        $scope.gmapAddMarker = (event) ->
            # Создаем маркер
            # var marker = newMarker($scope.marker_id++, $scope.marker_type, event.latLng)
            marker = newMarker($scope.marker_id++, event.latLng, $scope.map)

            # Добавляем маркер в маркеры
            $scope.tutor.markers.push(marker)

            # Добавляем маркер на карту
            marker.setMap($scope.gmap)

            # Ищем ближайшие станции метро к маркеру
            ApiService.exec 'metro',
                lat: marker.lat
                lng: marker.lng
            .then (response) ->
                marker.metros = response.data

            # Добавляем ивент удаления маркера
            $scope.bindMarkerDelete(marker)
            $scope.bindMarkerChangeType(marker)

        # Добавляем ивент удаления маркера
        $scope.bindMarkerDelete = (marker) ->
            google.maps.event.addListener marker, 'dblclick', (event) ->
                t = this
                # удаляем маркер с карты
                t.setMap null
                # удаляем маркер из коллекции
                $.each $scope.tutor.markers, (index, m) ->
                    console.log 'id', t.id, m.id
                    if m isnt undefined and t.id == m.id
                        $scope.tutor.markers.splice index, 1

        $scope.bindMarkerChangeType = (marker) ->
            google.maps.event.addListener marker, 'click', (event) ->
                if @type == 'green'
                    @type = 'red'
                    @setIcon ICON_RED
                else
                    @type = 'green'
                    @setIcon ICON_GREEN

        # Поиск по карте
        $scope.searchMap = (address) ->
            $scope.geocoder.geocode {
                address: address + ', московская область'
                bounds: $scope.RECOM_BOUNDS
            }, (results, status) ->
                if status == google.maps.GeocoderStatus.OK
                    # максимальное кол-во результатов
                    max_results = 3
                    # масштаб поиска
                    search_result_bounds = new (google.maps.LatLngBounds)
                    $.each results, (i, result) ->
                        return if i >= max_results

                        search_result_bounds.extend result.geometry.location
                        # границы карты в зависимости от поставленных меток
                        search_marker = new (google.maps.Marker)(
                            map: $scope.map
                            position: result.geometry.location
                            icon: ICON_SEARCH)

                        google.maps.event.addListener search_marker, 'click', (event) ->
                            @setMap null
                            $scope.gmapAddMarker event
                        $scope.search_markers = initIfNotSet($scope.search_markers)
                        $scope.search_markers.push search_marker

                    # если отображаемые маркеры есть, делаем зум на них
                    if results.length > 0
                        $scope.gmap.fitBounds search_result_bounds
                        $scope.gmap.panToBounds search_result_bounds
                        if results.length == 1
                            $scope.gmap.setZoom 12
                    else
                        $('#map-search').addClass('has-error').focus()

        # Запуск поиска по карте
        $scope.gmapsSearch = ($event) ->
            if $event.keyCode == 13 or $event.type == 'click'
                # prevent empty
                if $('#map-search').val() == ''
                    $('#map-search').addClass('has-error').focus()
                else
                    $('#map-search').removeClass 'has-error'
                $scope.searchMap $('#map-search').val()

        # Загрузить метки
        $scope.loadMarkers = ->
            $rootScope.dataLoaded.promise.then ->
                markers = []
                $.each $scope.tutor.markers, (index, marker) ->
                    # Создаем маркер
                    # @todo: сделать так, чтобы type и metros и еще дургие можно было передавать массивом в последнем параметре
                    new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type)
                    new_marker.metros = marker.metros

                    # Добавляем маркер на карту
                    new_marker.setMap($scope.map)

                    # Добавляем ивент удаления маркера
                    $scope.bindMarkerDelete(new_marker)
                    $scope.bindMarkerChangeType(new_marker)
                    markers.push new_marker
                $scope.tutor.markers = markers

        # Сохранить метки
        $scope.saveMarkers = ->
            $('#gmap-modal').modal 'hide'
