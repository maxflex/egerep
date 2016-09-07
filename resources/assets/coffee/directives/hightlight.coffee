angular.module('Egerep')
    .directive 'ngHighlight', ->
        restrict: 'A'
        scope:
            ngModel: '='
        controller: ($scope, $element, $attrs, $timeout) ->
            # бомже-вариант
            if $($element).prop('tagName') is 'INPUT'
                $($element).on 'keyup', ->
                    refreshInput @
                $timeout ->
                    refreshInput $element
                , 500

            if $($element).prop('tagName') is 'SELECT'
                $($element).on 'change', ->
                    refreshSelect @
                $timeout ->
                    refreshSelect $element
                , 500

            refreshInput = (el) ->
                if $(el).val()
                    $(el).parent().find('input, span').addClass('is-selected')
                else
                    $(el).parent().find('input, span').removeClass('is-selected')

            refreshSelect = (el) ->
                $(el).parent().find('button').removeClass 'is-selected'
                $(el).parent().find('select > option[value!=""]:selected').parent('select').siblings('button').addClass 'is-selected'


            # @todo: узнать, почему не запускается $watch на пустое значение
            # $scope.$watch 'ngModel', (newVal, oldVal) ->
            #     if newVal
            #         $($element).css('background', '#dceee5')
            # , true
