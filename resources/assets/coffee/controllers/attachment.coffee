angular
    .module 'Egerep'
    .factory 'Attachment', ($resource) ->
        $resource 'api/attachments/:id', {},
            update:
                method: 'PUT'
    .controller 'AttachmentsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates, UserService, PhoneService, Subjects, Grades) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.sort_field = 'created_at'
        $scope.sort_type = 'desc'

        $scope.sort = (field) ->
            if $scope.sort_field == field
                $scope.sort_type = if $scope.sort_type == 'desc' then 'asc' else 'desc'
            else
                $scope.sort_field = field
                $scope.sort_type  = 'desc'

            loadAttachments $scope.current_page

        $scope.changeState = (state_id) ->
            $rootScope.frontend_loading = true
            $scope.attachments = []
            $scope.chosen_state_id = state_id
            $scope.chosen_state_page_size = AttachmentStates[state_id].page_size
            $scope.sort_field = AttachmentStates[state_id].sort.field
            $scope.sort_type = AttachmentStates[state_id].sort.type
            $scope.current_page = 1

            loadAttachments 1
            window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());


        $timeout ->
            loadAttachments $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadAttachments $scope.current_page
            paginate('attachments', $scope.current_page)

        loadAttachments = (page) ->
            $scope.chosen_state_id = 'new' if not $scope.chosen_state_id

            params = '?page=' + page
            params += '&sort_field=' + $scope.sort_field + '&sort_type=' + $scope.sort_type
            params += '&state=' + $scope.chosen_state_id + '&page_size=' + AttachmentStates[$scope.chosen_state_id].page_size

            $http.get "api/attachments#{ params }"
            .then (response) ->
                $rootScope.frontend_loading = false
                $scope.data = response.data
                $scope.attachments = $scope.data.data
