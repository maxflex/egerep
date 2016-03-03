angular.module 'Egerep'
    .directive 'ngMulti', ->
        restrict: 'E'
        replace: true
        scope:
            object: '='
            model: '='
            noneText: '@'
        templateUrl: 'directives/ngmulti'
        controller: ($scope, $element, $attrs, $timeout) ->
            $timeout ->
                $($element).selectpicker
                    noneSelectedText: $scope.noneText
