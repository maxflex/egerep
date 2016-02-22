angular.module('Egerep')
    .factory 'Review', ($resource) ->
        $resource apiPath('reviews'), {id: '@id'}, updateMethod()

    .factory 'Archive', ($resource) ->
        $resource apiPath('archives'), {id: '@id'}, updateMethod()

    .factory 'Attachment', ($resource) ->
        $resource apiPath('attachments'), {id: '@id'}, updateMethod()

    .factory 'RequestList', ($resource) ->
        $resource apiPath('lists'), {id: '@id'}, updateMethod()

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
