angular.module('Egerep')
    .factory 'Client', ($resource) ->
        $resource apiPath('clients'), {id: '@id'}, updateMethod()



    .factory 'User', ($resource) ->
        $resource apiPath('users'), {id: '@id'}, updateMethod()



    .factory 'Tutor', ($resource) ->
        $resource apiPath('tutors'), {id: '@id'}, updateMethod()



apiPath = (entity) ->
    "api/#{entity}/:id"

updateMethod = ->
    update:
        method: 'PUT'
