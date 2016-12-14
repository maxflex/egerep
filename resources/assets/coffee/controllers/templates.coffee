angular
  .module 'Egerep'
    .controller 'TemplateIndex', ($rootScope, $scope, $http, Request) ->
      $scope.form_changed = false

      $scope.save = ->
        $http.post "templates",
          allTemplates: $scope.allTemplates
        .then (success)->
            $scope.form_changed = false
        , (error) ->
          console.error(error)
          $scope.form_changed = false



      #навешиваем событие изменения текста в форме
      angular.element(document).ready ->
        $(".checkChange").on 'keyup change', 'input, select, textarea', ->
          $scope.form_changed = true
          $scope.$apply()