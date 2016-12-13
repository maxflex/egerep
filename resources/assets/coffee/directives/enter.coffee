angular.module('Egerep')
  .directive 'enter', ->
    restrict: 'A'
    #scope:
    #  enter: '@'
    link: (scope, element, attrs) ->
      console.log 'test2', scope

      element.bind "keydown keypress"
      , (event) ->
          if event.which == 13
            scope.$apply ->
              scope.$eval attrs.enter

          event.preventDefault()
