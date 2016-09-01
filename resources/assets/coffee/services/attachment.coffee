angular.module 'Egerep'
    .service 'AttachmentService', (AttachmentStates) ->
        this.AttachmentStates = AttachmentStates

        this.getState = (attachment) ->
            if attachment.archive
                'ended'
            else
                if attachment.forecast
                    'inprogress'
                else
                    'new'

        this.getStatus = (attachment) ->
            this.AttachmentStates[this.getState(attachment)]
            
        this
