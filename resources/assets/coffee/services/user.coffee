angular.module 'Egerep'
    .service 'UserService', ->
        this.getUser = (user_id, users) ->
            _.findWhere users,
                id: parseInt(user_id)

        this