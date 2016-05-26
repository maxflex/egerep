angular
    .module 'Egerep'
    .factory 'Attachment', ($resource) ->
        $resource 'api/attachments/:id', {},
            update:
                method: 'PUT'
    .controller 'AttachmentsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates) ->
        _.extend AttachmentStates, { all : 'все' }
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        $scope.changeList = (state_id) ->
            $scope.chosen_state_id = state_id
            $scope.current_page = 1

            ajaxStart()
            loadAttachments 1
            ajaxEnd()

            window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());


        $timeout ->
            loadAttachments $scope.page
            $scope.current_page = $scope.page
#            if not $scope.state_counts
#                $http.post "api/attachments/counts",
#                    state: $scope.attachment_state
#                .then (response) ->
#                    $scope.request_state_counts = response.data.attachment_state_counts

        $scope.pageChanged = ->
            loadAttachments $scope.current_page
            paginate('attachments', $scope.current_page)

        loadAttachments = (page) ->
            params = '?page=' + page
            if $scope.chosen_state_id
                params += '&state=' + $scope.chosen_state_id
            else
                params += '&state=' + 'new'

            $http.get "api/attachments#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.attachments = $scope.data.data
