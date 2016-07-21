angular
    .module 'Egerep'
    .factory 'Attachment', ($resource) ->
        $resource 'api/attachments/:id', {},
            update:
                method: 'PUT'
    .controller 'AttachmentsIndex', ($rootScope, $scope, $timeout, $http, AttachmentStates, AttachmentService, UserService, PhoneService, Subjects, Grades, Presence, YesNo, AttachmentVisibility) ->
        bindArguments($scope, arguments)
        $rootScope.frontend_loading = true

        refreshCounts = ->
            $timeout ->
                $('.selectpicker option').each (index, el) ->
                    $(el).data 'subtext', $(el).attr 'data-subtext'
                    $(el).data 'content', $(el).attr 'data-content'
                $('.selectpicker').selectpicker 'refresh'
            , 100

        $scope.filter = ->
            $.cookie("attachments", JSON.stringify($scope.search), { expires: 365, path: '/' });
            $scope.current_page = 1
            $scope.pageChanged()

        $scope.changeState = (state_id) ->
            $rootScope.frontend_loading = true
            $scope.attachments = []
            $scope.current_page = 1
            loadAttachments($scope.current_page)
            window.history.pushState(state_id, '', 'attachments/' + state_id.toLowerCase());

        $timeout ->
            $scope.search = if $.cookie("attachments") then JSON.parse($.cookie("attachments")) else {}
            loadAttachments $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            $rootScope.attachments = []
            loadAttachments $scope.current_page
            paginate('attachments', $scope.current_page)

        loadAttachments = (page) ->
            params = '?page=' + page

            $http.get "api/attachments#{ params }"
            .then (response) ->
                $scope.data = response.data.data
                $scope.attachments = response.data.data.data
                $scope.counts = response.data.counts
                $rootScope.frontend_loading = false
                refreshCounts()
