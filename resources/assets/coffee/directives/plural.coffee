angular.module 'Egerep'
    .directive 'plural', ->
        restrict: 'E'
        scope:
            count: '='
            type: '@'
            noneText: '@'
        templateUrl: 'directives/plural'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.textOnly = $attrs.hasOwnProperty('textOnly')

            $scope.when =
                'age': ['год', 'года', 'лет']
                'student': ['ученик', 'ученика', 'учеников']
                'minute': ['минуту', 'минуты', 'минут']
