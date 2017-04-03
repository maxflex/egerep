<select ng-highlight class="form-control selectpicker" ng-model='search.user_id' ng-change="filter()" id='change-user'>
    <option value=''>пользователь</option>
	<option disabled>──────────────</option>
	<option
		ng-repeat="user in UserService.getActiveInAnySystem()"
		value="@{{ user.id }}"
		data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span>"
	></option>
	<option disabled>──────────────</option>
	<option
        ng-repeat="user in UserService.getBannedInBothSystems()"
		value="@{{ user.id }}"
		data-content="<span style='color: black'>@{{ user.login }}</span>"
	></option>
</select>
