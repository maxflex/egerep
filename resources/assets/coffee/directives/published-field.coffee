angular.module 'Egerep'
.directive 'publishedField', ->
    restrict: 'E'
#    replace: true
    templateUrl: 'directives/published-field'
    scope:
        inEgeCentr: '@'
