angular.module('Egerep').directive 'notifications', ->
    restrict: 'E'
    templateUrl: 'directives/notifications'
    scope:
        user: '='
        entityId: '='
        trackLoading: '='
        entityType: '@'
    controller: ($rootScope, $scope, $timeout, Notification) ->
        $scope.show_max = 4                         # сколько комментов показывать в свернутом режиме
        $scope.show_all_notifications = false       # показать все напоминания?
        $scope.is_dragging = false                  # комментарий перетаскивается

        bindDraggable = (notification_id) ->
            $("#notification-#{notification_id}").draggable
                revert: 'invalid'
                activeClass: 'drag-active'
                start: (e, ui) ->
                    $scope.is_dragging = true
                    $scope.$apply()
                stop: (e, ui) ->
                    $scope.is_dragging = false
                    $scope.$apply()

        $timeout ->
            $scope.notifications.forEach (notification) ->
                bindDraggable(notification.id)
            $("#notification-delete-#{$scope.entityType}-#{$scope.entityId}").droppable
                tolerance: 'pointer'
                hoverClass: 'hovered'
                drop: (e, ui) ->
                    $scope.remove($(ui.draggable).data('notification-id'))
        , 2000

        $scope.showAllNotifications = ->
            $scope.show_all_notifications = true
            $timeout ->
                $scope.notifications.forEach (notification) ->
                    bindDraggable(notification.id)

        $scope.getNotifications = ->
            if ($scope.show_all_notifications or $scope.notifications.length <= $scope.show_max) then $scope.notifications else _.last($scope.notifications, $scope.show_max - 1)

        # перезагружаем комменты, если меняется entity_id
        $scope.$watch 'entityId', (newVal, oldVal) ->
            $scope.notifications = Notification.query
                entity_type: $scope.entityType
                entity_id: newVal
            , ->
                $rootScope.loaded_notifications++ if $scope.trackLoading

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.startNotificationing = (event) ->
            $scope.start_notificationing = true
            $timeout ->
                $(event.target).parent().find('div').focus()

        $scope.endNotificationing = ->
            $scope.comment = ''
            $scope.start_notificationing = false

        $scope.remove = (notification_id) ->
            _.find($scope.notifications, {id: notification_id}).$remove()
            $scope.notifications = _.without($scope.notifications, _.findWhere($scope.notifications, {id: notification_id}))

        $scope.edit = (notification, event) ->
            old_text    = notification.notification
            element     = $(event.target)

            element.unbind('keydown').unbind('blur')

            element.attr('contenteditable', 'true').focus()
                .on 'keydown', (e) ->
                    console.log old_text
                    if e.keyCode is 13
                        $(@).removeAttr('contenteditable').blur()
                        notification.notification = $(@).text()
                        notification.$update()
                    if e.keyCode is 27
                        $(@).blur()

                .on 'blur', (e) ->
                    if element.attr 'contenteditable'
                        console.log old_text
                        element.removeAttr('contenteditable').html old_text
            return
            # setEndOfContenteditable(event.target)

        $scope.resizeInput = (event) ->
            $(event.target).attr('size', $(event.target).val().length)

        $scope.submitNotification = (event) ->
            if event.keyCode is 13
                date = $scope.comment.match /[\d]{2}\.[\d]{2}\.[\d]{4}/
                return if date is null
                new_notification = new Notification
                    comment: $scope.comment
                    user_id: $scope.user.id
                    entity_id: $scope.entityId
                    date: date[0]
                    entity_type: $scope.entityType
                new_notification.$save()
                    .then (response)->
                        console.log response
                        new_notification.user = $scope.user
                        new_notification.id = response.id
                        $scope.notifications.push new_notification
                        $timeout ->
                            bindDraggable(new_notification.id)
                $scope.endNotificationing()

            if event.keyCode is 27
                $(event.target).blur()
