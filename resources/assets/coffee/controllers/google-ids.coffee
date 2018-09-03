angular
    .module 'Egerep'
    .controller 'GoogleIdsController', ($scope, $timeout, $http, $rootScope) ->
        bindArguments($scope, arguments)

        $scope.google_ids = ''
        $scope.loading = false

        $scope.show = ->
            $rootScope.frontend_loading = true
            $http.post('api/google-ids', {google_ids: $scope.google_ids}).then (response) ->
                console.log(response)
                $scope.data = response.data.result
                $scope.totals = response.data.totals
                $rootScope.frontend_loading = false
