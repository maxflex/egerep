angular
    .module 'Egerep'
    .factory 'Attachment', ($resource) ->
        $resource 'api/attachments/:id', {},
            update:
                method: 'PUT'
    .controller 'AttachmentsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.sort_field = 'created_at'
        $scope.sort_type = 'desc'

        $scope.sort = (field) ->
            $rootScope.frontend_loading = true
            if $scope.sort_field == field
                $scope.sort_type = if $scope.sort_type == 'desc' then 'asc' else 'desc'
            else
                $scope.sort_field = field
                $scope.sort_type  = 'desc'
            loadAttachments $scope.current_page

        # track comment loading.
        $rootScope.loaded_comments = 0
        $scope.$watch () ->
            $rootScope.loaded_comments
        , (val) ->
            $rootScope.frontend_loading = false if $scope.attachments and $scope.attachments.length == val
        # /track comment loading.

        $scope.changeState = (state_id) ->
            $rootScope.frontend_loading = true
            $rootScope.loaded_comments = 0
            $scope.attachments = []
            $scope.current_page = 1
            $scope.chosen_state_id = state_id
            $scope.chosen_state_page_size = AttachmentStates[state_id].page_size
            $scope.sort_field = AttachmentStates[state_id].sort.field
            $scope.sort_type = AttachmentStates[state_id].sort.type

            loadAttachments 1
            window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());


        $timeout ->
            loadAttachments $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            $rootScope.loaded_comments = 0
            $rootScope.attachments = []
            loadAttachments $scope.current_page
            paginate('attachments/' + $scope.chosen_state_id, $scope.current_page)

        loadAttachments = (page) ->
            $scope.chosen_state_id = 'new' if not $scope.chosen_state_id

            params = '?page=' + page
            params += '&sort_field=' + $scope.sort_field + '&sort_type=' + $scope.sort_type
            params += '&state=' + $scope.chosen_state_id + '&page_size=' + AttachmentStates[$scope.chosen_state_id].page_size

            $http.get "api/attachments#{ params }"
            .then (response) ->
                $scope.data = response.data
                $scope.attachments = $scope.data.data
                $rootScope.frontend_loading = false if not AttachmentStates[$scope.chosen_state_id].track_comment_load
