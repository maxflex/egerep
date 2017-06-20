<div ng-show="selected_list.attachments.length">
    <div class="row controls-line">
        <div class="col-sm-12">
            <span
                ng-repeat="attachment in selected_list.attachments"
                ng-class="{'link-like': attachment !== selected_attachment}"
                ng-click="selectAttachment(attachment)"
            >@{{ tutors[attachment.tutor_id] }}</span>
            @if($user->allowed(\Shared\Rights::ER_DELETE_ATTACHMENTS))
                <div class='controls-right'>
                    <span ng-click="removeAttachment()" ng-show='selected_attachment'>удалить</span>
                </div>
            @endif
        </div>
    </div>

        @include('clients.form.attachment._attachment')

        <div class="row mb">
            <div class="col-sm-12">
                <notifications entity-type='attachment' entity-id='selected_attachment.id' user='{{ $user }}'></notifications>
            </div>
        </div>
        <div class="row mb">
            <div class="col-sm-12">
                <comments entity-type='attachment' entity-id='selected_attachment.id' user='{{ $user }}'></comments>
            </div>
        </div>

        @include('clients.form.attachment._archive')
        @include('clients.form.attachment._review')
    </div>
</div>
