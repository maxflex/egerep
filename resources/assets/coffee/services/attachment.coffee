angular.module 'Egerep'
    .service 'AttachmentService', (AttachmentStates) ->
        this.AttachmentStates = AttachmentStates

        this.getStatus = (attachment) ->
            if attachment.archive
                this.AttachmentStates['ended']
            else
                if attachment.forecast
                    this.AttachmentStates['inprogress']
                else
                    this.AttachmentStates['new']
        this
