angular.module 'Egerep'
    .directive 'ngSelect', ->
        restrict: 'E'
        replace: true
        scope:
            object: '='
            model: '='
            noneText: '@'
        templateUrl: 'directives/ngselect'
        controller: ($scope, $element, $attrs) ->
            # выбираем первое значение по умолчанию, если нет noneText
            if not $scope.noneText
                $scope.model = _.first Object.keys($scope.object)
