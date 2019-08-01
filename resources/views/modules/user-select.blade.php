<select ng-highlight class="form-control selectpicker" ng-model='search.user_id' ng-change="filter()" id='change-user'>
    <option value=''>пользователь</option>
	<option disabled>──────────────</option>
	<option
		ng-repeat="user in UserService.getWithSystem()"
		ng-show='counts.user[user.id]'
		value="@{{ user.id }}"
		data-content="@{{ user.nickname }}<small class='text-muted'>@{{ counts.user[user.id] || '' }}</small>"
	></option>
	<option disabled ng-show="UserService.getBannedHaving(counts.user).length">──────────────</option>
	<option
		ng-show='counts.user[user.id]'
        ng-repeat="user in UserService.getBannedUsers()"
		value="@{{ user.id }}"
		data-content="<span style='color: gray'>@{{ user.nickname }}</span><small class='text-muted'>@{{ counts.user[user.id] || '' }}</small>"
	></option>
</select>
