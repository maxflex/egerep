angular.module('Egerep')
    .factory 'Request', ($resource) ->
        $resource apiPath('requests'), {id: '@id'}, updateMethod()

    .factory 'Sms', ($resource) ->
        $resource apiPath('sms'), {id: '@id'}, updateMethod()

    .factory 'Comment', ($resource) ->
        $resource apiPath('comments'), {id: '@id'}, updateMethod()

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
