angular
    .module 'Egerep'
    .factory 'Request', ($resource) ->
        $resource 'api/requests/:id', {},
            update:
                method: 'PUT'
    .controller 'RequestsIndex', ($scope, Request) ->
        $scope.requests = Request.query()

    .controller 'RequestsForm', ($scope) ->
        console.log 'here'
