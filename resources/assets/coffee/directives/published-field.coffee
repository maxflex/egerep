angular.module 'Egerep'
.directive 'publishedField', ->
    restrict: 'E'
    replace: true
    templateUrl: 'directives/published-field'
    controller: ($scope, $attrs) ->
        $scope.inEgeCentr = $attrs.hasOwnProperty 'inEgeCentr'
