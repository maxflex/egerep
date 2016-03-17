angular.module 'Egerep'
    .service 'PusherService', ($http) ->
        this.init = (channel, callback) ->
            this.pusher = new Pusher '2d212b249c84f8c7ba5c',
                encrypted: true
                cluster: 'eu'
            this.channel = this.pusher.subscribe 'egerep'
            this.channel.bind "App\\Events\\#{ channel }", callback
        this
