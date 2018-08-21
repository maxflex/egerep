@include('modules.gmap')

<div class="custom-dropdown__close" ng-click="grade_year_dropdown = false" ng-show="grade_year_dropdown"></div>
@include('clients.form._modals')
@include('clients.form._client')
@include('clients.form._request')

@if(@$id)
    @include('clients.form._lists')
    @include('clients.form._attachments')
@endif

<sms number='sms_number'></sms>
