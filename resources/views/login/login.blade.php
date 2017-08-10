@extends('login')
@section('content')
<center autocomplete="off">
        <div style='display: none'>
            <input type="password"/>
        </div>
        <div class="input-wrapper">
            <input ng-disabled="sms_verification" type="text" id="inputLogin" placeholder="login" autofocus ng-model="login" autocomplete="off" ng-keyup="enter($event)">
        </div>
        <div class="input-wrapper">
            <input ng-class="{'password-start-typing': password}" ng-disabled="sms_verification" type="password" id="inputPassword"  placeholder="password" ng-model="password" autocomplete="off" ng-keyup="enter($event)">
            <img ng-show="!sms_verification && password" src="img/svg/@{{ in_process ? 'spinner' : 'next' }}.svg" ng-class="{'in-process': in_process}" ng-click="checkFields()" />
        </div>
        <div class="input-wrapper" ng-show="sms_verification">
            <input type="text" id="sms-code" placeholder="sms code" ng-model="code" autocomplete="off" ng-keyup="enter($event)">
            <img ng-show="code" src="img/svg/@{{ in_process ? 'spinner' : 'next' }}.svg" ng-class="{'in-process': in_process}" ng-click="checkFields()" />
        </div>
    <div class="g-recaptcha" data-sitekey="{{ config('captcha.site') }}" data-size="invisible" data-callback="captchaChecked"></div>
</center>
@stop
