angular
    .module 'Egerep'

    .factory 'Model', ($resource) ->
        $resource 'api/models/:id', {},
            update:
                method: 'PUT'



    #
    #   LIST CONTROLLER
    #
    .controller "ModelsIndex", ($scope, $timeout, Model) ->
        $scope.models = Model.query()



    #
    #   ADD/EDIT CONTROLLER
    #
    .controller "ModelsForm", ($scope, $timeout, $interval, Model) ->
        # get teacher
        $timeout ->
            $scope.model = Model.get {id: $scope.id} if $scope.id > 0
