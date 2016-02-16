angular.module 'Egerep'
    .directive 'ngSelect', ->
        restrict: 'E'
        replace: true
        scope:
            object: '='
            model: '='
            title: '@'
        templateUrl: 'directives/ngselect'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.title = $attrs.title
            $scope.multiple = $attrs.hasOwnProperty('multiple')
            # $timeout ->
            #     $($element).selectpicker
            #         noneSelectedText: $scope.title

            # refresh selectpicker on update
            $scope.$watch 'model', (newVal, oldVal) ->
                console.log newVal, oldVal
                return if newVal is undefined
                spe $element, 'предмет' if oldVal is undefined
                spRefresh $element if oldVal isnt undefined

        # link: (scope, element, attrs) ->
        #     angular.element(document).ready ->
        #         console.log scope.multiple, typeof scope.multiple
        #         if scope.multiple
        #             console.log 'in TRUE'
        #             # $(element).attr('multiple', true).selectpicker({noneSelectedText: scope.hint})
        #         else
        #             console.log 'in FALSE'
        #             $(element).removeAttr('multiple').selectpicker()
