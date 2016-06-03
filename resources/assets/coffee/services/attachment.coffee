angular.module 'Egerep'
    .service 'AttachmentService', (AttachmentStates) ->
        this.AttachmentStates = AttachmentStates
        
        this.getStatus = (attachment) ->
            if attachment.archive
                this.AttachmentStates['ended'].label
            else
                if attachment.forecast
                    this.AttachmentStates['inprogress'].label
                else
                    this.AttachmentStates['new'].label
        this
