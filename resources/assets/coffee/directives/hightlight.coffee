angular.module('Egerep')
    .directive 'ngHighlight', ->
        restrict: 'A'
        scope:
            ngModel: '='
        controller: ($scope, $element, $attrs, $timeout) ->
            # бомже-вариант

            if $($element).prop('tagName') is 'INPUT' then $($element).on 'keyup', ->
                if $(@).val()
                    $(@).parent().find('input, span').addClass('is-selected')
                else
                    $(@).parent().find('input, span').removeClass('is-selected')
            if $($element).prop('tagName') is 'SELECT' then $($element).on 'change', ->
                $($element).parent().find('button').removeClass 'is-selected'
                $($element).parent().find('select > option[value!=""]:selected').parent('select').siblings('button').addClass 'is-selected'
            # @todo: узнать, почему не работает $watch
            # $scope.$watch 'ngModel', (newVal, oldVal) ->
            #     if newVal
            #         $($element).css('background', '#dceee5')
            # , true
