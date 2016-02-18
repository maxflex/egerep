angular
    .module 'Egerep'



    #
    #   LIST CONTROLLER
    #
    .controller "ClientsIndex", ($scope, $timeout, Client) ->
        $scope.clients = Client.query()



    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "ClientsForm", ($scope, $rootScope, $timeout, $interval, $http, Client, Request, User, RequestState, Subjects, Grades) ->
        $scope.RequestState = RequestState
        $scope.Subjects = Subjects
        $scope.Grades = Grades
        $rootScope.frontend_loading = true

        # @костыль
        $scope.fake_user =
            id: 0
            login: 'system'

        # Save everything
        $scope.edit = ->
            $scope.ajaxStart()
            filterMarkers()
            $scope.client.$update()
                .then (response) ->
                    $scope.ajaxEnd()


        # get teacher
        $timeout ->
            $scope.users = User.query()


            $http.get 'api/tutors/list'
                .success (tutors) ->
                    $scope.tutors = tutors

            if $scope.id > 0
                $scope.client = Client.get {id: $scope.id}, (client) ->
                    $scope.selected_request = if $scope.request_id then _.findWhere(client.requests, {id: $scope.request_id}) else client.requests[0]
                    # set the default list
                    if client.subject_list isnt null
                        $scope.selected_list_id = client.subject_list[0]
                        # set the default attachment, if any
                        # @temporary
                        # if client.attachments[$scope.selected_list_id]
                        #     $scope.selected_attachment = client.attachments[$scope.selected_list_id][0]
                    $rootScope.frontendStop()

        $scope.attachmentExists = (subject_id, tutor_id) ->
            return false if $scope.client.attachments[subject_id] is undefined
            _.findWhere($scope.client.attachments[subject_id], {tutor_id: tutor_id}) isnt undefined

        $scope.selectAttachment = (tutor_id) ->
            $scope.selected_attachment = _.findWhere $scope.client.attachments[$scope.selected_list_id],
                tutor_id: tutor_id

        $scope.setList = (subject_id) ->
            console.log subject_id
            $scope.selected_list_id = subject_id

        $scope.selectRequest = (request) ->
            $scope.selected_request = request

        $scope.toggleUser = ->
            # @костыль
            if not $scope.selected_request.user
                $scope.selected_request.user = $scope.fake_user

            new_user = _.find $scope.users, (user) ->
                user.id > $scope.selected_request.user.id
            # if toggeled to the last user, start the loop over | SYSTEM USER INSTEAD
            # new_user = $scope.users[0] if new_user is undefined
            $scope.selected_request.user = new_user
            $scope.selected_request.user_id = new_user.id

        $scope.getUser = (user_id) ->
            _.findWhere $scope.users,
                id: parseInt(user_id)

        $scope.addListSubject = ->
            $scope.client.subject_list.push($scope.list_subject_id)
            $scope.client.lists[$scope.list_subject_id] = []
            $scope.selected_list_id = $scope.list_subject_id

            delete $scope.list_subject_id
            $('#add-subject').modal 'hide'

        $scope.addListTutor = ->
            $scope.client.lists[$scope.selected_list_id].push $scope.list_tutor_id
            delete $scope.list_tutor_id
            $('#add-tutor').modal 'hide'

        $scope.newAttachment = (tutor_id, subject_id) ->
            $scope.client.attachments[subject_id] = [] if not $scope.client.attachments[subject_id]

            new_attachment =
                tutor_id: tutor_id
                client_id: $scope.id

            $scope.client.attachments[subject_id].push new_attachment
            $scope.selected_attachment = new_attachment

        $scope.addRequest = ->
            new_request = new Request
                client_id: $scope.id

            new_request.$save()
                .then (data) ->
                    $scope.client.requests.push(data)
                    $scope.selected_request = data

        $scope.removeRequest = ->
            Request.delete {id: $scope.selected_request.id}, ->
                $scope.client.requests = removeById $scope.client.requests, $scope.selected_request.id
                $scope.selected_request = $scope.client.requests[0]


        # parse textarea for tutor IDS
        $scope.$watch 'selected_request.comment', (newVal, oldVal) ->
            return if newVal is undefined and oldVal is undefined
            newVal = oldVal if newVal is undefined
            $scope.tutor_ids = []
            matches = newVal.match /Репетитор [\d]+/gi
            $.each matches, (index, match) ->
                tutor_id = match.match /[\d]+/gi
                $scope.tutor_ids.push parseInt(tutor_id)

        # refresh selectpicker on $selected_attachment update
        $scope.$watch 'selected_attachment', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'attachment-subjects', 'предмет' if oldVal is undefined
            spRefresh 'attachment-subjects' if oldVal isnt undefined

        $scope.$watch 'client.grades', (newVal, oldVal) ->
            console.log newVal, oldVal, 'grades'
            return if newVal is undefined
            sp 'client-grades', 'укажите класс' if oldVal is undefined
            spRefresh 'client-grades' if oldVal isnt undefined







        #
        # ПОСЛЕ ЗАГРУЗКИ КАРТЫ
        #
        $scope.marker_id = 1

        filterMarkers = ->
            new_markers = []
            $.each $scope.client.markers, (index, marker) ->
                new_markers.push _.pick(marker, 'lat', 'lng', 'type')
            $scope.client.markers = new_markers

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
            if $scope.client.markers.length
                # отображать только метки с выбранным типом
                bounds = new (google.maps.LatLngBounds)
                # есть отображаемые маркеры
                markers_count = 0
                # отображаем маркеры по одному
                $.each $scope.client.markers, (index, marker) ->
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
            $scope.client.markers.push(marker)

            # Добавляем маркер на карту
            marker.setMap($scope.gmap)

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
                $.each $scope.client.markers, (index, m) ->
                    console.log 'id', t.id, m.id
                    if m isnt undefined and t.id == m.id
                        $scope.client.markers.splice index, 1

        $scope.bindMarkerChangeType = (marker) ->
            google.maps.event.addListener marker, 'click', (event) ->
                if @type == 'green'
                    @type = 'red'
                    @setIcon ICON_RED
                else if @type == 'red'
                    @type = 'blue'
                    @setIcon ICON_BLUE
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
                $.each $scope.client.markers, (index, marker) ->
                    # Создаем маркер
                    marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type)

                    # Добавляем маркер на карту
                    marker.setMap($scope.map)

                    # Добавляем ивент удаления маркера
                    $scope.bindMarkerDelete(marker)
                    $scope.bindMarkerChangeType(marker)
                    markers.push marker
                $scope.client.markers = markers

        # Сохранить метки
        $scope.saveMarkers = ->
            $('#gmap-modal').modal 'hide'
