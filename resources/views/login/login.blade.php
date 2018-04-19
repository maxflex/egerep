@extends('login')
@section('content')
<center autocomplete="off" class="animated fadeIn" style='animation-duration: 1.5s'>
        <div style='display: none'>
            <input type="password"/>
        </div>
        <div class="input-wrapper">
            <input ng-disabled="sms_verification" type="text" id="inputLogin" placeholder="login" autofocus ng-model="login" autocomplete="off" ng-keyup="enter($event)">
        </div>
        <div class="input-wrapper">
            <input ng-class="{'password-start-typing': password}" ng-disabled="sms_verification" type="password" id="inputPassword"  placeholder="password" ng-model="password" autocomplete="off" ng-keyup="enter($event)">
            <span class="login-icons" ng-show="!sms_verification && password">
                <img ng-show="in_process"  class="in-process"  src="img/svg/spinner.svg">
                <img ng-show="!in_process" class="login-arrow" src="img/svg/next.svg" ng-click="checkFields()">
            </span>
        </div>
        <div class="input-wrapper" ng-show="sms_verification" style='position: absolute'>
            <input type="text" id="sms-code" placeholder="sms code" ng-model="code" autocomplete="off" ng-keyup="enter($event)">
            <span class="login-icons" ng-show="code">
                <img ng-show="in_process"  class="in-process"  src="img/svg/spinner.svg">
                <img ng-show="!in_process" class="login-arrow" src="img/svg/next.svg" ng-click="checkFields()">
            </span>
        </div>
    <div class="g-recaptcha" data-sitekey="{{ config('captcha.site') }}" data-size="invisible" data-callback="captchaChecked"></div>
</center>
@stop
