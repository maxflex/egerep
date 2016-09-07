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
            $scope.highlight = $attrs.hasOwnProperty('highlight')
            $timeout ->
                $($element).selectpicker
                    noneSelectedText: $scope.noneText

            if $scope.highlight then $scope.$watch 'model', (newVal, oldVal) ->
                if newVal
                    $timeout ->
                        $($element).parent().find('button').removeClass 'is-selected'
                        $($element).parent().find('select > option[value!=""]:selected').parent('select').siblings('button').addClass 'is-selected'
