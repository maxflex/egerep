angular.module('Egerep')
    .factory 'Marker', ($resource) ->
        $resource apiPath('markers'), {id: '@id'}, updateMethod()

    .factory 'Notification', ($resource) ->
        $resource apiPath('notifications'), {id: '@id'}, updateMethod()

    .factory 'Account', ($resource) ->
        $resource apiPath('accounts'), {id: '@id'}, updateMethod()

    .factory 'AccountPayment', ($resource) ->
        $resource apiPath('account/payments'), {id: '@id'}, updateMethod()

    .factory 'PlannedAccount', ($resource) ->
        $resource apiPath('periods/planned'), {id: '@id'}, updateMethod()

    .factory 'Review', ($resource) ->
        $resource apiPath('reviews'), {id: '@id'}, updateMethod()

    .factory 'Archive', ($resource) ->
        $resource apiPath('archives'), {id: '@id'}, updateMethod()

    .factory 'Attachment', ($resource) ->
        $resource apiPath('attachments'), {id: '@id'}, updateMethod()

    .factory 'RequestList', ($resource) ->
        $resource apiPath('lists'), {id: '@id'}, updateMethod()

    .factory 'Request', ($resource) ->
        $resource apiPath('requests'), {id: '@id'},
            update:
                method: 'PUT'
            transfer:
                method: 'POST'
                url: apiPath('requests', 'transfer')
            list:
                method: 'GET'

    .factory 'Sms', ($resource) ->
        $resource apiPath('sms'), {id: '@id'}, updateMethod()

    .factory 'Comment', ($resource) ->
        $resource apiPath('comments'), {id: '@id'}, updateMethod()

    .factory 'Client', ($resource) ->
        $resource apiPath('clients'), {id: '@id'}, updateMethod()

    .factory 'User', ($resource) ->
        $resource apiPath('users'), {id: '@id'}, updateMethod()

    .factory 'Tutor', ($resource) ->
        $resource apiPath('tutors'), {id: '@id'},
            update:
                method: 'PUT'
            deletePhoto:
                url: apiPath('tutors', 'photo')
                method: 'DELETE'
            list:
                method: 'GET'

apiPath = (entity, additional = '') ->
    "api/#{entity}/" + (if additional then additional + '/' else '') + ":id"

updateMethod = ->
    update:
        method: 'PUT'
