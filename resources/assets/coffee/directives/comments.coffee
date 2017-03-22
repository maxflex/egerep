angular.module('Egerep').directive 'comments', ->
    restrict: 'E'
    templateUrl: 'directives/comments'
    scope:
        user: '='
        entityId: '='
        trackLoading: '='
        entityType: '@'
    controller: ($rootScope, $scope, $timeout, Comment, UserService) ->
        $scope.UserService = UserService
        $scope.show_max = 4                 # сколько комментов показывать в свернутом режиме
        $scope.show_all_comments = false    # показать все комментарии?

        $scope.showAllComments = ->
            $scope.show_all_comments = true
            focusModal()

        $scope.getComments = ->
            if ($scope.show_all_comments or $scope.comments.length <= $scope.show_max) then $scope.comments else _.last($scope.comments, $scope.show_max - 1)

        # перезагружаем комменты, если меняется entity_id
        $scope.$watch 'entityId', (newVal, oldVal) ->
            $scope.comments = Comment.query
                entity_type: $scope.entityType
                entity_id: newVal
            , ->
                $rootScope.loaded_comments++ if $scope.trackLoading

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.startCommenting = (event) ->
            $scope.start_commenting = true
            $timeout ->
                $(event.target).parent().find('input').focus()

        $scope.endCommenting = ->
            $scope.comment = ''
            $scope.start_commenting = false

        $scope.remove = (comment_id) ->
            _.find($scope.comments, {id: comment_id}).$remove()
            $scope.comments = _.without($scope.comments, _.findWhere($scope.comments, {id: comment_id}))

        $scope.edit = (comment, event) ->
            old_text    = comment.comment
            element     = $(event.target)
            # комментарий редактируется...
            comment.is_being_edited = true

            element.unbind('keydown').unbind('blur')

            element.attr('contenteditable', 'true').focus()
                .on 'keydown', (e) ->
                    console.log old_text
                    if e.keyCode is 13
                        $(@).removeAttr('contenteditable').blur()
                        comment.comment = $(@).text()
                        comment.$update()
                    if e.keyCode is 27
                        $(@).blur()

                .on 'blur', (e) ->
                    _.find($scope.comments, {id: comment.id})?.is_being_edited = false
                    if element.attr 'contenteditable'
                        console.log old_text
                        element.removeAttr('contenteditable').html old_text
            return
            # setEndOfContenteditable(event.target)

        $scope.submitComment = (event) ->
            if event.keyCode is 13
                new_comment = new Comment
                    comment: $scope.comment
                    user_id: $scope.user.id
                    entity_id: $scope.entityId
                    entity_type: $scope.entityType
                new_comment.$save()
                    .then (response)->
                        console.log response
                        new_comment.user = $scope.user
                        new_comment.id = response.id
                        $scope.comments.push new_comment
                $scope.endCommenting()
                focusModal()

            if event.keyCode is 27
                $(event.target).blur()

        focusModal = ->
            # @custom
            # после сохранения коммента фокус устновим на .modul:visible если оно есть,
            # иначе фокус теряется и с модуля и с инпута, и ESC не обрабатывается
            $('.modal:visible').focus() if $('.modal:visible').length
            return
            # /@custom
