angular.module 'Egerep'
    .service 'PusherService', ($http) ->
        this.bind = (channel, callback) ->
            init() if this.pusher is undefined
            this.channel.bind "App\\Events\\#{ channel }", callback

        init = =>
            this.pusher = new Pusher '2d212b249c84f8c7ba5c',
                encrypted: true
                cluster: 'eu'
            this.channel = this.pusher.subscribe 'egerep'

        this
