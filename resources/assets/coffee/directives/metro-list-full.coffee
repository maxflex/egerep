angular.module('Egerep').directive 'metroListFull', ->
    restrict: 'E'
    templateUrl: 'directives/metro-list-full'
    scope:
        markers: '='
    controller: ($scope, $element, $attrs) ->
        $scope.minutes = (minutes) ->
            Math.round minutes
