angular.module('Egerep').directive 'metroList', ->
    restrict: 'E'
    templateUrl: 'directives/metro-list'
    scope:
        markers: '='
    controller: ($scope, $element, $attrs) ->
        $scope.inline = $attrs.hasOwnProperty('inline')
        $scope.one_station = $attrs.hasOwnProperty('oneStation')

        $scope.short = (title) ->
            title.slice(0,3).toUpperCase()

        $scope.minutes = (minutes) ->
            Math.round minutes

        $scope.editMarkerModal = (marker) ->
            $('#marker-modal').modal('show')
            $scope.selected_marker = marker
            $scope.marker_comment = marker.comment

        $scope.editMarker = ->
            $('#marker-modal').modal('hide')
            $scope.$parent.form_changed = true
            $scope.selected_marker.comment = $scope.marker_comment
