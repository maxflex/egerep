angular
    .module 'Egerep'



    #
    #   LIST CONTROLLER
    #
    .controller "ClientsIndex", ($scope, $rootScope, $timeout, $http, Client) ->
        $rootScope.frontend_loading = true

        $scope.pageChanged = ->
            load $scope.current_page
            paginate('clients', $scope.current_page)

        load = (page) ->
            $rootScope.frontend_loading = true
            params = '?page=' + page
            params += "&global_search=#{ $scope.global_search }" if $scope.global_search

            # update repetitors
            # @todo: why ugly params? maybe use $http.post instead?
            $http.get "api/clients#{ params }"
                .then (response) ->
                    $rootScope.frontendStop()
                    $scope.data = response.data
                    $scope.clients = $scope.data.data

        $timeout ->
            load $scope.page
            $scope.current_page = $scope.page

    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "ClientsForm", ($scope, $rootScope, $timeout, $interval, $http, Client, Request, RequestList, User, RequestStates, Subjects, Grades, Attachment, ReviewStates, ArchiveStates, AttachmentStates, ReviewScores, Archive, Review, ApiService, UserService, Recommendations, RecommendationTypes, AttachmentService) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.getRecommendation = (tutor) ->
            month = moment().format 'M'
            # не 10 класс?
            if $scope.client.grade isnt 10
                if month >= 7 && month <= 10
                    if tutor.meeting_count >= 2
                        return Recommendations[1]
                    else
                        if tutor.meeting_count == 1
                            return Recommendations[2]
                        else
                            if tutor.active_clients_count >= 2
                                return Recommendations[3]
                            else
                                return Recommendations[4]
                else
                    if month >= 11 or month <= 2
                        if tutor.meeting_count >= 1
                            return Recommendations[5]
                        else
                            if tutor.active_clients_count >= 2
                                return Recommendations[6]
                            else
                                return Recommendations[7]
                    else
                        if tutor.meeting_count >= 2
                            return Recommendations[8]
                        else
                            return Recommendations[9]
            else
                if tutor.meeting_count >= 2
                    return Recommendations[10]
                else
                    if tutor.meeting_count == 1
                        return Recommendations[11]
                    else
                        if tutor.active_clients_count >= 2
                            return Recommendations[12]
                        else
                            return Recommendations[13]

        $scope.is_dragging_teacher = false
        $scope.sortableOptions =
            tolerance: 'pointer'
            activeClass: 'drag-active'
            helper: 'clone'
            appendTo: 'body'
            # drag: (event, ui) ->
            #     ui.helper.offset(ui.position)
            start: (e, ui) ->
                $scope.is_dragging_teacher = true
                $scope.$apply()
            stop: (e, ui) ->
                $scope.is_dragging_teacher = false
                $scope.$apply()
                saveSelectedList()

        # Save everything
        $scope.edit = ->
            $scope.ajaxStart()
            filterMarkers()
            $scope.client.$update()
                .then (response) ->
                    $scope.ajaxEnd()


        # get teacher
        $timeout ->
            $('.teacher-remove-droppable').droppable
                tolerance: 'pointer'
                hoverClass: 'drop-hover'
                drop: (e, ui) ->
                    tutor_id = $(ui.draggable).data 'id'
                    $timeout ->
                        $scope.selected_list.tutor_ids = _.without($scope.selected_list.tutor_ids, tutor_id.toString())
                        saveSelectedList()

            $scope.users = User.query()

            $http.get 'api/tutors/list'
                .success (tutors) ->
                    $scope.tutors = tutors

            if $scope.id > 0
                $scope.client = Client.get {id: $scope.id}, (client) ->
                    $scope.selected_request = if $scope.request_id then _.findWhere(client.requests, {id: $scope.request_id}) else client.requests[0]
                    sp 'list-subjects', 'выберите предмет'
                    $scope.parseHash()
                    $rootScope.frontendStop()

        saveSelectedList = ->
            # tutor_ids = []
            # $.each $scope.selected_list.tutors, (index, tutor) ->
            #     tutor_ids.push tutor.id
            # $scope.selected_list.tutor_ids = tutor_ids
            RequestList.update $scope.selected_list

        $scope.getTutorList = ->
            tutors = []
            $.each $scope.selected_list.tutor_ids, (index, tutor_id) ->
                tutors.push findById($scope.selected_list.tutors, tutor_id)
            tutors

        # Если в ссылке указан хэш, то это #id_списка#id_стыковки
        $scope.parseHash = ->
            values = window.location.hash.split('#')
            values.shift()
            if values[0]
                $scope.selected_list = findById($scope.selected_request.lists, values[0])
            if values[1] and $scope.selected_list
                $scope.selected_attachment = findById($scope.selected_list.attachments, values[1])

        $scope.toggleArchive = ->
            if $scope.selected_attachment.archive
                Archive.delete $scope.selected_attachment.archive, ->
                    delete $scope.selected_attachment.archive
            else
                Archive.save
                    attachment_id: $scope.selected_attachment.id
                , (response) ->
                    $scope.selected_attachment.archive = response

        $scope.toggleReview = ->
            if $scope.selected_attachment.review
                Review.delete $scope.selected_attachment.review, ->
                    delete $scope.selected_attachment.review
            else
                Review.save
                    attachment_id: $scope.selected_attachment.id
                , (response) ->
                    $scope.selected_attachment.review = response

        $scope.attachmentExists = (tutor_id) ->
            attachment_exists = false
            $.each $scope.client.requests, (index, request) ->
                return if attachment_exists
                $.each request.lists, (index, list) ->
                    $.each list.attachments, (index, attachment) ->
                        attachment_exists = true if parseInt(attachment.tutor_id) is parseInt(tutor_id)
            attachment_exists

        $scope.selectAttachment = (attachment) ->
            $scope.selected_attachment = attachment

        $scope.addList = ->
            $scope.dialog('add-subject')

        $scope.setList = (list) ->
            $scope.selected_list = list
            $scope.showListMap() if $scope.list_map
            delete $scope.selected_attachment

        $scope.listExists = (subject_id) ->
            _.findWhere($scope.selected_request.lists, {subject_id: parseInt(subject_id)}) isnt undefined

        $scope.selectRequest = (request) ->
            $scope.selected_request = request
            delete $scope.selected_list

        $scope.addListSubject = ->
            RequestList.save
                request_id: $scope.selected_request.id
                subjects: $scope.list_subjects
            , (data) ->
                $scope.selected_request.lists.push data
                $scope.selected_list = data

            delete $scope.list_subjects
            spRefresh 'list-subjects'
            $('#add-subject').modal 'hide'
            return

        $scope.addListTutor = ->
            $scope.selected_list.tutor_ids.push $scope.list_tutor_id
            RequestList.update
                id: $scope.selected_list.id
                tutor_ids: $scope.selected_list.tutor_ids
            , ->
            # $scope.client.lists[$scope.selected_list_id].push $scope.list_tutor_id
                delete $scope.list_tutor_id
                $('#add-tutor').modal 'hide'

        $scope.newAttachment = (tutor_id) ->
            Attachment.save
                grade: $scope.client.grade
                tutor_id: tutor_id
                subjects: $scope.selected_list.subjects
                request_list_id: $scope.selected_list.id
                client_id: $scope.client.id
            , (new_attachment) ->
                $scope.selected_attachment = new_attachment
                $scope.selected_list.attachments.push new_attachment

        $scope.addRequest = ->
            new_request = new Request
                client_id: $scope.id

            new_request.$save()
                .then (data) ->
                    $scope.client.requests.push(data)
                    $scope.selected_request = data
                    unsetSelected(false, true, true)

        $scope.removeRequest = ->
            bootbox.confirm 'Вы уверены, что хотите удалить заявку?', (response) ->
                if response is true
                    Request.delete {id: $scope.selected_request.id}, ->
                        $scope.client.requests = removeById $scope.client.requests, $scope.selected_request.id
                        unsetSelected(true, true, true)

        $scope.transferRequest = ->
            $('#transfer-request').modal 'show'

        $scope.transferRequestGo = ->
            $('#transfer-request').modal 'hide'
            $http.post "api/requests/transfer/#{$scope.selected_request.id}",
                client_id: $scope.transfer_client_id
            .then (response) ->
                console.log response
                if response.data isnt '' then location.reload() else bootbox.alert('Клиент не существует')

        # Снять выбор с выбранной комбинации
        unsetSelected = (request = false, list = false, attachment = false) ->
            $scope.selected_request = null if request
            $scope.selected_list = null if list
            $scope.selected_attachment = null if attachment

        $scope.removeList = ->
            bootbox.confirm 'Вы уверены, что хотите удалить список?', (response) ->
                if response is true
                    RequestList.delete {id: $scope.selected_list.id}, ->
                        $scope.selected_request.lists = removeById $scope.selected_request.lists, $scope.selected_list.id
                        delete $scope.selected_list
                        unsetSelected(false, true, true)

        $scope.removeAttachment = ->
            bootbox.confirm 'Вы уверены, что хотите удалить стыковку?', (response) ->
                if response is true
                    Attachment.delete {id: $scope.selected_attachment.id}, ->
                        $scope.selected_list.attachments = removeById $scope.selected_list.attachments, $scope.selected_attachment.id
                        delete $scope.selected_attachment
                        unsetSelected(false, false, true)

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
            sp 'attachment-subjects', 'выберите предмет' if oldVal is undefined
            spRefresh 'attachment-subjects' if oldVal isnt undefined
            rebindMasks()


        #
        # ПОСЛЕ ЗАГРУЗКИ КАРТЫ
        #
        $scope.marker_id = 1
        $scope.map_number = 0

        filterMarkers = ->
            new_markers = []
            $.each $scope.client.markers, (index, marker) ->
                new_markers.push _.pick(marker, 'lat', 'lng', 'type', 'metros')
            $scope.client.markers = new_markers


        $scope.$on 'mapInitialized', (event, map) ->
            map.number = $scope.map_number
            if $scope.map_number is 0
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
            else
                # Запоминаем карту после инициалицации
                $scope.gmap2 = map

                # Зум и центр карты по умолчанию
                $scope.gmap2.setCenter new (google.maps.LatLng)(55.7387, 37.6032)
                $scope.gmap2.setZoom 11
            $scope.map_number++

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
            marker = newMarker($scope.marker_id++, event.latLng, $scope.gmap)

            # Добавляем маркер в маркеры
            $scope.client.markers.push(marker)

            # Добавляем маркер на карту
            marker.setMap($scope.gmap)

            # Ищем ближайшие станции метро к маркеру
            ApiService.metro 'closest',
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
                            map: $scope.gmap
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
                    # @todo: сделать так, чтобы type и metros и еще дургие можно было передавать массивом в последнем параметре
                    new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap, marker.type)
                    new_marker.metros = marker.metros

                    # Добавляем маркер на карту
                    new_marker.setMap($scope.gmap)
                    console.log 'adding marker', $scope.gmap

                    # Добавляем ивент удаления маркера
                    $scope.bindMarkerDelete(new_marker)
                    $scope.bindMarkerChangeType(new_marker)
                    markers.push new_marker
                $scope.client.markers = markers

        # Сохранить метки
        $scope.saveMarkers = ->
            $('#gmap-modal').modal 'hide'





        #
        # КАРТА СПИСКА
        #
        $scope.listMap = ->
            $scope.list_map = not $scope.list_map
            $scope.showListMap()
            $timeout ->
                $('html, body').animate
                    scrollTop: $("#list-map").offset().top
                , 300

        # determine whether tutor had already been added
        $scope.added = (tutor_id) ->
            tutor_id in $scope.tutor_ids

        # rebind draggable
        rebindDraggable = ->
            $('.temporary-tutor').draggable
                containment: 'window'
                revert: (valid) ->
                    return true if valid
                    $scope.tutor_list   = removeById($scope.tutor_list, $scope.dragging_tutor.id)
                    $scope.tutor_ids    = _.without($scope.tutor_ids, $scope.dragging_tutor.id)
                    $scope.$apply()
                    repaintChosen()

        # remember dragging tutor
        $scope.startDragging = (tutor) ->
            $scope.dragging_tutor = tutor

        showTutorsOnMap = ->
            unsetAllMarkers()
            $scope.marker_id2 = 1
            # временный список репетиторов
            $scope.tutor_list = []
            # отображать только метки с выбранным типом
            bounds = new (google.maps.LatLngBounds)
            # есть отображаемые маркеры
            markers_count = 0
            $scope.markers2 = []

            # Показываем карту
            google.maps.event.trigger $scope.gmap2, 'resize'

            # Зум и центр карты по умолчанию
            $scope.gmap2.setCenter new (google.maps.LatLng)(55.7387, 37.6032)
            $scope.gmap2.setZoom 11

            $scope.selected_list.tutors.forEach (tutor) ->
                tutor.markers.forEach (marker) ->
                    markers_count++
                    bounds.extend(new google.maps.LatLng(marker.lat, marker.lng))

                    # Создаем маркер
                    new_marker = newMarker($scope.marker_id2++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap2, marker.type)
                    new_marker.metros = marker.metros
                    new_marker.tutor = tutor

                    # Добавляем маркер на карту
                    new_marker.setMap($scope.gmap2)

                    # Добавляем ивент удаления маркера
                    bindTutorMarkerEvents(new_marker)
                    $scope.markers2.push new_marker

            # если отображаемые маркеры есть, делаем зум на них
            if markers_count > 0
                $scope.gmap2.fitBounds bounds
                $scope.gmap2.panToBounds bounds
                $scope.gmap2.setZoom 11

            $scope.gmap2.panBy(150, 0)

        showClientOnMap = ->
            $scope.client.markers.forEach (marker) ->
                # Создаем маркер
                new_marker = newMarker($scope.marker_id2++, new google.maps.LatLng(marker.lat, marker.lng), $scope.gmap2, 'white')
                new_marker.metros = marker.metros
                new_marker.setMap($scope.gmap2)

        unsetAllMarkers = ->
            # unset markers
            if $scope.markers2 isnt undefined
                $scope.markers2.forEach (marker) ->
                    marker.setMap null

        bindTutorMarkerEvents = (marker) ->
            # double click custom handler with delay
            google.maps.event.addListener marker, 'click', (event) ->
                # single click
                if marker.tutor in $scope.tutor_list
                    $scope.tutor_list = removeById($scope.tutor_list, marker.tutor.id)
                else
                    $scope.hovered_tutor = null
                    $scope.tutor_list.push marker.tutor
                $scope.addOrRemove(marker.tutor.id)
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
            if tutor_id in $scope.tutor_ids
                $scope.tutor_ids = _.without($scope.tutor_ids, tutor_id)
            else
                $scope.tutor_ids.push(tutor_id)
            repaintChosen()

        repaintChosen = ->
            $scope.markers2.forEach (marker) ->
                if marker.tutor.id in $scope.tutor_ids and not marker.chosen
                    marker.chosen = true
                    marker.setIcon ICON_BLUE
                if marker.tutor.id not in $scope.tutor_ids and marker.chosen
                    marker.chosen = false
                    marker.setIcon getMarkerType(marker.type)

        $scope.showListMap = ->
            $timeout ->
                showTutorsOnMap()
                showClientOnMap()
