angular
    .module 'Egerep'
    .controller 'RequestsIndex', ($rootScope, $scope, $timeout, $http, Request, RequestStates, Comment, PhoneService, UserService, Grades, Subjects, PusherService) ->
        bindArguments($scope, arguments)
        _.extend RequestStates, { all : 'все' }
        $rootScope.frontend_loading = true

        $scope.user_id = localStorage.getItem('requests_index_user_id')

        PusherService.init 'RequestUserChanged', (data) ->
            if request = findById($scope.requests, data.request_id)
                request.user_id = data.new_user_id
                $scope.$apply()

        # track comment loading.
        $rootScope.loaded_comments = 0
        $scope.$watch () ->
            console.log $rootScope.loaded_comments
            $rootScope.loaded_comments
        , (val) ->
            console.log val
            $rootScope.frontend_loading = false if $scope.requests and $scope.requests.length == val
        # /track comment loading.

        $scope.howLongAgo = (created_at) ->
            now = moment(Date.now())
            created_at = moment(new Date(created_at).getTime())
            days = now.diff(created_at, 'days')
            hours = now.diff(created_at, 'hours') - (days * 24)
            {days: days, hours: hours}

        $scope.changeList = (state_id) ->
            $scope.chosen_state_id = state_id
            $scope.current_page = 1
            $rootScope.loaded_comments = 0
            $rootScope.frontend_loading = true

            ajaxStart()
            loadRequests 1
            ajaxEnd()

            window.history.pushState('requests/' + state_id.toLowerCase(), '', 'requests/' + state_id.toLowerCase());

        extendRequestStates = ->
            $scope.RequestStatesForTabLabel = angular.copy $scope.RequestStates
            _.extend $scope.RequestStatesForTabLabel, { all : 'все' }

        $timeout ->
            extendRequestStates()
            loadRequests $scope.page
            $scope.current_page = $scope.page

        $scope.pageChanged = ->
            $rootScope.frontend_loading = true
            $rootScope.loaded_comments = 0
            loadRequests $scope.current_page
            paginate('requests/' + $scope.chosen_state_id, $scope.current_page)

        loadRequests = (page) ->
            $scope.chosen_state_id = 'all' if not $scope.chosen_state_id

            params = '?page=' + page
            params += '&state=' + $scope.chosen_state_id
            params += "&user_id=#{ $scope.user_id }" if $scope.user_id isnt ''


            $http.get "api/requests#{ params }"
            .then (response) ->
                $scope.data = response.data
                $scope.requests = $scope.data.data

                # сортировка станций маркеров по близоcти
                $scope.requests.forEach (request) ->
                    request.client.markers.forEach (marker) ->
                        marker.metros = _.sortBy marker.metros, (s) ->
                            s.minutes

                $rootScope.frontend_loading = false if not $scope.requests.length

            $http.post "api/requests/counts",
                state: $scope.chosen_state_id
                user_id: $scope.user_id
            .then (response) ->
                $scope.request_state_counts = response.data.request_state_counts
                $scope.user_counts          = response.data.user_counts
                console.log 'counts updated'
                $timeout ->
                    $('#change-state option, #change-user option').each (index, el) ->
                        $(el).data 'subtext', $(el).attr 'data-subtext'
                        $(el).data 'content', $(el).attr 'data-content'
                    $('#change-state, #change-user').selectpicker 'refresh'

        $scope.hasBannedUsers = ->
            _.filter(UserService.getBannedUsers(), (u) ->
                scope.user_counts[u.id] > 0).length


        $scope.changeState = ->
            localStorage.setItem('requests_index_state', $scope.state)
            $scope.changeList($scope.state)

        $scope.changeUser = ->
            localStorage.setItem('requests_index_user_id', $scope.user_id)
            $scope.changeList($scope.chosen_state_id)
            # $scope.changeList($scope.state)

        # $scope.bannedUsersHaveRequests = ->
        #     banned_users_have_requests = false
        #     UserService.getBannedUsers().forEach (user) ->
        #         if $scope.user_counts[user.id]
        #             banned_users_have_requests = true
        #             return
        #     banned_users_have_requests

        # @todo использовать $rootScope.toggleEnumServer
        $scope.toggleState = (request) ->
            request_cpy = angular.copy request
            $rootScope.toggleEnum request_cpy, 'state', RequestStates

            $scope.Request.update
                id: request_cpy.id
                state: request_cpy.state
            , (response) ->
                $rootScope.toggleEnum request, 'state', RequestStates
    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
