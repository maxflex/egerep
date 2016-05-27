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
                console.log $scope.tutors

        $scope.blurComment = (tutor) ->
            tutor.is_being_commented = false
            tutor.debt_comment = tutor.old_debt_comment

        $scope.focusComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment

        $scope.startComment = (tutor) ->
            tutor.is_being_commented = true
            tutor.old_debt_comment = tutor.debt_comment
            $timeout ->
                $("#list-comment-#{tutor.id}").focus()

        $scope.saveComment =  (event, tutor) ->
            if event.keyCode is 13
                Tutor.update
                    id: tutor.id
                    debt_comment: tutor.debt_comment
                , (response) ->
                    tutor.old_debt_comment = tutor.debt_comment
                    $(event.target).blur()
