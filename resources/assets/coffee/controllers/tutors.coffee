angular
    .module 'Egerep'

    #
    #   LIST CONTROLLER
    #
    .controller "TutorsIndex", ($scope, $rootScope, $timeout, $http, Tutor, TutorStates, UserService, PusherService, TutorPublishedStates, TutorErrors, PhoneFields) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.recalcTutorErrors = ->
            $scope.tutor_errors_updating = true
            $http.post 'api/command/model-errors', {model: 'tutors'}

        $scope.state            = localStorage.getItem('tutors_index_state')
        $scope.user_id          = localStorage.getItem('tutors_index_user_id')
        $scope.published_state  = localStorage.getItem('tutors_index_published_state')
        $scope.errors_state     = localStorage.getItem('tutors_index_errors_state')
        $scope.egecentr_source  = localStorage.getItem('tutors_index_egecentr_source')
        $scope.markers_state    = localStorage.getItem('tutors_index_markers_state')

        PusherService.bind 'ResponsibleUserChanged', (data) ->
            if tutor = findById($scope.tutors, data.tutor_id)
                tutor.responsible_user_id = data.responsible_user_id
                $scope.$apply()

        $scope.duplicateClick = (phone) ->
            $scope.global_search = phone
            $timeout ->
                $('#global-search').submit()

        $scope.yearDifference = (year) ->
            moment().format("YYYY") - year

        $scope.changeState = ->
            localStorage.setItem('tutors_index_state', $scope.state)
            loadTutors($scope.current_page)

        $scope.changeUser = ->
            localStorage.setItem('tutors_index_user_id', $scope.user_id)
            loadTutors($scope.current_page)

        $scope.changePublishedSate = ->
            localStorage.setItem 'tutors_index_published_state', $scope.published_state
            loadTutors $scope.current_page

        $scope.changeErrorsState = ->
            localStorage.setItem 'tutors_index_errors_state', $scope.errors_state
            loadTutors $scope.current_page

        $scope.changeSource = ->
            localStorage.setItem 'tutors_index_egecentr_source', $scope.egecentr_source
            loadTutors $scope.current_page

        $scope.changeMarkers = ->
            localStorage.setItem 'tutors_index_markers_state', $scope.markers_state
            loadTutors $scope.current_page

        $timeout ->
            loadTutors($scope.page)
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadTutors($scope.current_page)
            paginate('tutors', $scope.current_page)

        loadTutors = (page) ->
            $rootScope.frontend_loading = true
            params = '?page=' + page
            params += "&global_search=#{ $scope.global_search }" if $scope.global_search
            params += "&state=#{ $scope.state }" if $scope.state isnt null and $scope.state isnt ''
            params += "&user_id=#{ $scope.user_id }" if $scope.user_id
            params += "&published_state=#{ $scope.published_state }" if $scope.published_state isnt null and $scope.published_state isnt ''
            params += "&errors_state=#{ $scope.errors_state }" if $scope.errors_state isnt null and $scope.errors_state isnt ''
            params += "&egecentr_source=#{ $scope.egecentr_source }" if $scope.egecentr_source isnt null and $scope.egecentr_source isnt ''
            params += "&markers_state=#{ $scope.markers_state }" if $scope.markers_state isnt null and $scope.markers_state isnt ''

            # update repetitors
            # @todo: why ugly params? maybe use $http.post instead?
            $http.get "api/tutors#{ params }"
                .then (response) ->
                    $rootScope.frontendStop()
                    $scope.data = response.data
                    $scope.tutors = $scope.data.data

            # update counts
            $http.post "api/tutors/counts",
                state:           $scope.state
                user_id:         $scope.user_id
                published_state: $scope.published_state
                errors_state:    $scope.errors_state
                egecentr_source: $scope.egecentr_source
                markers_state: $scope.markers_state
            .then (response) ->
                $scope.state_counts     = response.data.state_counts
                $scope.user_counts      = response.data.user_counts
                $scope.published_counts = response.data.published_counts
                $scope.errors_counts    = response.data.errors_counts
                $scope.source_counts    = response.data.source_counts
                $scope.marker_counts    = response.data.marker_counts
                $timeout ->
                    # потому что data кэшируется
                    # @todo: add issue at github
                    # @link: https://github.com/silviomoreto/bootstrap-select/issues/293
                    $('#change-state option, #change-user option, #change-published option, #change-errors option, #change-source option, #change-markers option').each (index, el) ->
                        $(el).data 'subtext', $(el).attr 'data-subtext'
                        $(el).data 'content', $(el).attr 'data-content'

                    $('#change-state, #change-user, #change-published, #change-errors, #change-source, #change-markers').selectpicker 'refresh'
                    $rootScope.frontend_loading = false

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

    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "TutorsForm", ($scope, $rootScope, $timeout, Tutor, SvgMap, Subjects, Grades, ApiService, TutorStates, Genders, Workplaces, Branches, BranchService, TutorService, $http, Marker) ->
        bindArguments($scope, arguments)

        $rootScope.frontend_loading = true
        $scope.form_changed = false

        # страница полностью загружена (включая все изменения маркеров)
        $scope.fully_loaded = false

        $scope.mergeTutor = ->
            $('#merge-tutor').modal 'show'

        $scope.mergeTutorGo = ->
            $('#merge-tutor').modal 'hide'
            $http.post "api/tutors/merge",
                tutor_id: $scope.tutor.id
                new_tutor_id: $scope.new_tutor_id
            .then (response) ->
                if response.data is 'false'
                    bootbox.alert 'Существуют задублированные клиенты'
                else
                    bootbox.alert 'Информация перенесена'

        $scope.deleteTutor = ->
            bootbox.confirm 'Вы уверены, что хотите удалить преподавателя?', (result) ->
                if result is true
                    ajaxStart()
                    $scope.tutor.$delete ->
                        history.back()

        # разбить "1 класс, 2 класс, 3 класс" на "1-3 классы"
        $scope.shortenGrades = ->
            a = $scope.tutor.grades
            return if a.length < 1
            limit = a.length - 1
            combo_end = -1
            pairs = []
            i = 0
            while i <= limit
                combo_start = parseInt(a[i])

                if combo_start > 11
                    i++
                    combo_end = -1
                    pairs.push Grades[combo_start]
                    continue

                if combo_start <= combo_end
                    i++
                    continue

                j = i
                while j <= limit
                    combo_end = parseInt(a[j])
                    # если уже начинает искать по студентам
                    break if combo_end >= 11
                    break if parseInt(a[j + 1]) - combo_end > 1
                    j++
                if combo_start != combo_end
                    pairs.push combo_start + '–' + combo_end + ' классы'
                else
                    pairs.push combo_start + ' класс'
                i++
            $timeout ->
                $('#sp-tutor-grades').parent().find('.filter-option').html pairs.join ', '
            return

        $scope.deletePhoto = ->
            bootbox.confirm 'Удалить фото преподавателя?', (result) ->
                if result is true
                    $scope.tutor.$deletePhoto ->
                        $scope.tutor.has_photo_cropped = false
                        $scope.tutor.has_photo_original = false

        $scope.saveCropped = ->
            $('#photo-edit').cropper('getCroppedCanvas').toBlob (blob) ->
                formData = new FormData
                formData.append 'croppedImage', blob
                formData.append 'tutor_id', $scope.tutor.id
                ajaxStart()
                $.ajax 'upload/cropped',
                    method: 'POST'
                    data: formData
                    processData: false
                    contentType: false
                    dataType: 'json'
                    success: (response) ->
                        ajaxEnd()
                        $scope.tutor.has_photo_cropped = true
                        $scope.photo_cropped_size = response
                        $scope.picture_version++
                        $scope.$apply()
                        $scope.closeDialog('change-photo')

        bindCropper = ->
            $('#photo-edit').cropper 'destroy'
            $('#photo-edit').cropper
                aspectRatio: 4 / 5
                # minContainerHeight: 700
                # minContainerWidth: 700
                # minCropBoxWidth: 240
                # minCropBoxHeight: 300
                preview: '.img-preview'
                viewMode: 1
                zoomable: false
                zoomOnWheel: false
                crop: (e) ->
                    width   = $('#photo-edit').cropper('getCroppedCanvas').width
                    quality = Math.round(width / 240 * 100)
                    $scope.quality = if quality > 100 then 100 else quality
                    $scope.$apply()
                built: ->
                    $scope.cropper_built = true
                    $scope.$apply()


        $scope.picture_version = 1;
        bindFileUpload = ->
        	# загрузка файла договора
        	$('#fileupload').fileupload
        		formData:
        			tutor_id: $scope.tutor.id
        		maxFileSize: 10000000, # 10 MB
        		# начало загрузки
        		send: ->
        			NProgress.configure({ showSpinner: true })
        		,
        		# во время загрузки
        		progress: (e, data) ->
        		    NProgress.set(data.loaded / data.total)
        		,
        		# всегда по окончании загрузки (неважно, ошибка или успех)
        		always: ->
        		    NProgress.configure({ showSpinner: false })
        		    ajaxEnd()
        		,
        		done: (i, response) ->
                    $scope.tutor.photo_extension     = response.result.extension
                    $scope.tutor.photo_original_size = response.result.size
                    $scope.tutor.photo_cropped_size  = 0
                    $scope.tutor.has_photo_original  = true
                    $scope.tutor.has_photo_cropped   = false
                    $scope.picture_version++
                    $scope.$apply()
                    bindCropper()
        		,

        # show photo editor
        $scope.showPhotoEditor = ->
            $scope.dialog('change-photo')
            $scope.cropper_built = false
            bindCropper()

        # get tutor
        $timeout ->
            if $scope.id > 0
                $scope.tutor = Tutor.get {id: $scope.id}, ->
                    $timeout ->
                        # bindCropper()
                        bindFileUpload()
                    , 1000
                    $scope.original_tutor = angular.copy $scope.tutor
                    $rootScope.frontendStop()
            else
                #set default values of tutor for create page
                $scope.tutor = TutorService.default_tutor

                # закомментировал нижнюю строчку, потому что при добавлении анкеты функционала
                # с disable кнопки сохранить быть не должно. там одна кнопка "добавить", при нажатии
                # на которую выполняется редирект
                # @todo: проверить работоспособность создания репетитора без original_tutor
                # $scope.tutor = $scope.original_tutor = TutorService.defaultTutor
                $rootScope.frontendStop()

        # @todo: ЗАМЕНИТЬ НА ДИРЕКТИВУ <ng-select> (уже сделано, но глючная. надо доделать)
        # refresh selectpicker on update
        $scope.$watch 'tutor.subjects', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'tutor-subjects', 'предмет', '+' if oldVal is undefined
            spRefresh 'tutor-subjects' if oldVal isnt undefined
        $scope.$watch 'tutor.subjects_ec', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'tutor-subjects-ec', 'предмет', '+' if oldVal is undefined
            spRefresh 'tutor-subjects-ec' if oldVal isnt undefined

        # refresh selectpicker on update
        $scope.$watch 'tutor.grades', (newVal, oldVal) ->
            return if newVal is undefined
            if oldVal is undefined
                sp 'tutor-grades', 'классы'
                $timeout ->
                    $scope.shortenGrades()
                , 50
            else
                $timeout ->
                    $scope.shortenGrades()
            # spRefresh 'tutor-grades' if oldVal isnt undefined

        $scope.$watch 'tutor.branches', (newVal, oldVal) ->
            return if newVal is undefined
            sp 'tutor-branches', 'филиалы', ' ' if oldVal is undefined
            spRefresh 'tutor-branches' if oldVal isnt undefined

        # только после загрузки маркеров биндим отслеживание кнопки сохранить
        $scope.$watchCollection 'tutor', (newVal, oldVal) ->
            $scope.form_changed = true if $scope.fully_loaded

        $scope.svgSave = ->
            $scope.form_changed = true
            $('#svg-modal').modal 'hide'
            return

        $scope.yearDifference = (year) ->
            moment().format("YYYY") - year

        $scope.getAge = (birthday) ->
            moment().format("YYYY") - birthday.split(".")[2] if birthday

        $scope.add = ->
            $scope.saving = true
            Tutor.save $scope.tutor, (tutor) ->
                window.location = "tutors/#{tutor.id}/edit"

        $scope.edit = ->
            ajaxStart()
            $scope.saving = true
            filterMarkers()

            $scope.tutor.$update()
                .then (response) ->
                    $scope.tutor = response;
                    $scope.loadMarkers()
                    $scope.saving = false
                    $scope.form_changed = false
                    ajaxEnd()




        #
        # ПОСЛЕ ЗАГРУЗКИ КАРТЫ
        #
        $scope.marker_id = 1

        filterMarkers = ->
            new_markers = []
            $.each $scope.tutor.markers, (index, marker) ->
                new_markers.push _.pick(marker, 'lat', 'lng', 'type', 'metros', 'server_id', 'comment')
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
                $.each $scope.tutor.markers, (index, m) ->
                    console.log 'id', t.id, m.id
                    if m isnt undefined and t.id == m.id
                        # удаляем маркер с сервера, если нужно
                        if m.server_id isnt undefined
                            Marker.delete {id: m.server_id}
                            , ->
                                $scope.tutor.markers.splice index, 1
                        else
                            $scope.tutor.markers.splice index, 1

        $scope.bindMarkerChangeType = (marker) ->
            google.maps.event.addListener marker, 'click', (event) ->
                if @type == 'green'
                    @type = 'red'
                    icon_to_set = ICON_RED
                else
                    @type = 'green'
                    icon_to_set = ICON_GREEN
                gmap = this
                if marker.server_id isnt undefined
                    Marker.update {id: marker.server_id, type: @type}
                    , ->
                        gmap.setIcon icon_to_set
                else
                    gmap.setIcon icon_to_set

        # Поиск по карте
        $scope.searchMap = (address) ->
            $scope.geocoder.geocode {
                address: address + ', московская область'
                bounds: $scope.RECOM_BOUNDS
                componentRestrictions:
                    country: 'RU'
                    administrativeArea: 'Moscow'
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
                    new_marker = newMarker($scope.marker_id++, new google.maps.LatLng(marker.lat, marker.lng), $scope.map, marker.type, marker.id)
                    new_marker.metros = marker.metros
                    new_marker.comment = marker.comment

                    # Добавляем маркер на карту
                    new_marker.setMap($scope.map)

                    # Добавляем ивент удаления маркера
                    $scope.bindMarkerDelete(new_marker)
                    $scope.bindMarkerChangeType(new_marker)
                    markers.push new_marker
                $scope.tutor.markers = _.sortBy markers, (marker) ->
                    marker.server_id
                $timeout ->
                    $scope.fully_loaded = true

        # Сохранить метки
        $scope.saveMarkers = ->
            $scope.form_changed = true
            $('#gmap-modal').modal 'hide'
            filterMarkers()
            return
