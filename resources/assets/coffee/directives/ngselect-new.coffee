angular.module 'Egerep'
    .directive 'ngSelectNew', ->
        restrict: 'E'
        replace: true
        scope:
            object: '='
            model: '='
            noneText: '@'
            label: '@'
            field: '@'
        templateUrl: 'directives/select-new'
        controller: ($scope, $element, $attrs, $timeout) ->
            # выбираем первое значение по умолчанию, если нет noneText
            if not $scope.noneText
                value = _.first Object.keys($scope.object)
                value = $scope.object[value][$scope.field] if $scope.field

                if not $scope.model #race condition fix
                    $scope.model = value

            $timeout ->
                $element.selectpicker
                    noneSelectedText: $scope.noneText
            , 100

            $scope.$watchGroup ['model', 'object'], (newVal) ->
                if newVal
                    $timeout ->
                        $element.selectpicker 'refresh'
