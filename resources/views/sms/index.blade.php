@extends('app')
@section('title', 'История СМС')
@section('controller', 'SmsIndex')

@section('content')
<style>
    .glyphicon-pencil {
        display: none;
    }
</style>
<div class="row" style='margin-bottom: 20px; position: relative; z-index: 999'>
	<div class="col-sm-6">
		<input class="form-control" placeholder="поиск..." name="search" ng-keyup="filter()" ng-model="search">
	</div>
	<div class="col-sm-3">
		<div class="form-group">
			<phones entity='{}' sms-number='sms_number' entity-types='репетитор'></phones>
		</div>
		<!-- СМС -->
		<sms number='sms_number'></sms>
	</div>
    @if (allowed(\Shared\Rights::SECRET_SMS))
	<div class="col-sm-3">
        <select ng-model='is_secret' class='selectpicker' ng-change='filter()'>
            <option value="">все смс</option>
            <option disabled>──────────────</option>
            <option value='1'>только секретные</option>
            <option value='0'>только обычные</option>
        </select>
	</div>
    @endif
</div>

<div class="row">
   <div class="col-sm-12">
		<table class="table table-hover">
			<tbody>
				<tr ng-repeat="s in data.data" ng-class="{'secret-sms': s.is_secret}">
					<td class="col-sm-2">
						@{{ s.number_formatted }}
					</td>
					<td class="col-sm-6">
						@{{ s.message }}
					</td>
					<td class="col-sm-1">
						@{{ s.user_login }}
					</td>
					<td class="col-sm-2">
						@{{ s.created_at | formatDateTime }}
					</td>
					<td class="col-sm-1">
						@{{ s.status }}
					</td>
				</tr>
			</tbody>
		</table>
   </div>
</div>
@include('modules.pagination')
@stop
