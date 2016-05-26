angular
    .module 'Egerep'

    #
    #   LIST CONTROLLER
    #
    .controller "DebtIndex", ($rootScope, $scope, $timeout, $http, Tutor) ->

        $rootScope.frontend_loading = true

        $timeout ->
            loadPage $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            loadPage $scope.current_page
            paginate('debt', $scope.current_page)

        loadPage = (page) ->
            params = '?page=' + page

            $http.get "api/debt#{ params }"
            .then (response) ->
                $rootScope.frontendStop()
                $scope.data = response.data
                $scope.tutors = $scope.data.data


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
