angular.module('Egerep').directive 'comments', ->
    restrict: 'E'
    templateUrl: 'directives/comments'
    scope:
        user: '='
        entityId: '='
        entityType: '@'
    controller: ($scope, $timeout, Comment) ->
        $timeout ->
            $scope.comments = Comment.query
                entity_type: $scope.entityType
                entity_id: $scope.entityId

        $scope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $scope.startCommenting = (event) ->
            $scope.start_commenting = true
            $timeout ->
                $(event.target).parent().find('input').focus()

        $scope.endCommenting = ->
            $scope.comment = ''
            $scope.start_commenting = false

        $scope.submitComment = (event) ->
            if event.keyCode is 13
                new_comment = new Comment
                    comment: $scope.comment
                    user_id: $scope.user.id
                    entity_id: $scope.entityId
                    entity_type: $scope.entityType
                new_comment.$save()
                
                new_comment.user = $scope.user
                $scope.comments.push new_comment
                $scope.endCommenting()
