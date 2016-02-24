angular.module('Egerep').directive 'metroList', ->
    restrict: 'E'
    templateUrl: 'directives/metro-list'
    scope:
        markers: '='
    controller: ($scope) ->
        $scope.short = (title) ->
            title.slice(0,3).toUpperCase()

        $scope.minutes = (minutes) ->
            Math.round minutes
