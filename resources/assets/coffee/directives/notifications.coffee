angular.module('Egerep').directive 'notifications', ->
    restrict: 'E'
    templateUrl: 'directives/notifications'
    scope:
        user: '='
        entityId: '='
        trackLoading: '='
        entityType: '@'
    controller: ($rootScope, $scope, $timeout, Notification, Notify) ->
        $scope.show_max = 4                         # сколько комментов показывать в свернутом режиме
        $scope.show_all_notifications = false       # показать все напоминания?
        $scope.Notification = Notification
        $scope.Notify = Notify

        bindDateMask = (notification_id) ->
            $("#notification-#{notification_id}")
                .find('.notification-date-add')
                .mask 'd9.y9.y9', {clearIfNotMatch: true}

        $timeout ->
            $scope.notifications.forEach (notification) ->
                bindDateMask notification.id
        , 2000

        $scope.showAllNotifications = ->
            $scope.show_all_notifications = true
            $timeout ->
                $scope.notifications.forEach (notification) ->
                    bindDateMask notification.id

        $scope.getNotifications = ->
            if ($scope.show_all_notifications or $scope.notifications.length <= $scope.show_max) then $scope.notifications else _.last($scope.notifications, $scope.show_max - 1)

        # передобавляет contenteditable, хотя он есть всегда
        $scope.hack = (notification, event) ->
            $scope.setEditing notification
            $(event.target).attr('contenteditable', true).focus()
            return

        $scope.setEditing = (notification) ->
            $timeout ->
                notification.is_being_edited = true
            , 100
        $scope.unsetEditing = (notification) ->
            _.find($scope.notifications, {id: notification.id})?.is_being_edited = false
            console.log 'blue'

        $scope.toggle = (notification) ->
            $rootScope.toggleEnumServer(notification, 'approved', Notify, Notification)

        # перезагружаем комменты, если меняется entity_id
        $scope.$watch 'entityId', (newVal, oldVal) ->
            $scope.notifications = Notification.query
                entity_type: $scope.entityType
                entity_id: newVal
            , ->
                $timeout -> $scope.$apply()

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.startNotificationing = (event) ->
            $scope.start_notificationing = true
            $timeout ->
                $(event.target).parents('div').first().find('div').focus()
                $(event.target).parents('div').first().find('input').mask 'd9.y9.y9', {clearIfNotMatch: true}

        $scope.endNotificationing = (comment_element, date_element)->
            comment_element.html('')
            date_element.val('')
            $scope.start_notificationing = false

        $scope.remove = (notification_id) ->
            _.find($scope.notifications, {id: notification_id}).$remove()
            $scope.notifications = _.without($scope.notifications, _.findWhere($scope.notifications, {id: notification_id}))

        saveEdit = (notification, event) ->
            event.preventDefault()
            parent          = $(event.target).parents('div').first()
            comment_element = parent.find('div').last()
            date_element    = parent.find('input')
            comment         = comment_element.text()
            date            = date_element.val()

            if date is '' or date.match /_/
                console.log 'no date', date, date_element
                date_element.blur().focus()
                return
            if comment is ''
                console.log 'no comment', comment, comment_element
                comment_element.focus()
                return

            Notification.update {id: notification.id},
                comment: comment
                date: date

        $scope.editNotification = (notification, event) ->
            handleDateKeycodes event

            if event.keyCode is 13
                event.preventDefault()
                $(event.target).blur()
                window.getSelection().removeAllRanges()
                saveEdit(notification, event)
            if event.keyCode is 27
                window.getSelection().removeAllRanges()
                $(event.target).blur()
                return

        notificate = (event) ->
            parent          = $(event.target).parents('div').first()
            comment_element = parent.find('div').last()
            date_element    = parent.find('input')
            comment         = comment_element.text()
            date            = date_element.val()

            if date is '' or date.match /_/
                date_element.blur().focus()
                return
            if comment is ''
                comment_element.focus()
                return
            new_notification = new Notification
                comment: comment
                user_id: $scope.user.id
                entity_id: $scope.entityId
                date: date
                entity_type: $scope.entityType
            new_notification.$save()
                .then (response)->
                    console.log response
                    new_notification.user = $scope.user
                    new_notification.id = response.id
                    new_notification.approved = 0
                    $scope.notifications.push new_notification
                    $timeout ->
                        bindDateMask new_notification.id
            $scope.endNotificationing(comment_element, date_element)


        handleDateKeycodes = (event) ->
            return if $(event.target).prop('tagName') is 'DIV'
            if event.keyCode in [38, 40]
                event.preventDefault()
                date_node = $(event.target).parents('div').first().find('input')
                date = date_node.val()
                if date.match /_/
                    date_node.val $rootScope.formatDate moment()
                else
                    add_days = if event.keyCode == 38 then 1 else -1
                    new_date = $rootScope.formatDate moment('20' + convertDate date).add {day : add_days} # '20' чтобы  16 => 2016
                    date_node.val new_date

        $scope.submitNotification = (notification, event) ->
            handleDateKeycodes event

            if event.keyCode is 13
                event.preventDefault()
                notificate(event)
            if event.keyCode is 27
                window.getSelection().removeAllRanges()
                $(event.target).blur()

        $scope.defaultNotification = ->
            new_notification = new Notification
                comment: 'стандартное напоминание'
                user_id: $scope.user.id
                entity_id: $scope.entityId
                entity_type: $scope.entityType
                approved: 1
                date: moment(convertDate($scope.$parent.selected_attachment.date)).add(2, 'days').format('DD.MM.YY')
            new_notification.$save()
                .then (response)->
                    new_notification.user = $scope.user
                    new_notification.id = response.id
                    new_notification.approved = 1
                    $scope.notifications.push new_notification
                    $timeout ->
                        bindDateMask new_notification.id
