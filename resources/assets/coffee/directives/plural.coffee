angular.module 'Egerep'
    .directive 'plural', ->
        restrict: 'E'
        scope:
            count: '='
            type: '@'
            noneText: '@'
        templateUrl: 'directives/plural'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.when =
                'age': ['год', 'года', 'лет']
                'student': ['ученик', 'ученика', 'учеников']
