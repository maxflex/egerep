<div class="row mb">
    <div class="col-sm-3">
        <div class="form-group">
            <textarea name="name" rows="4" cols="40" class="form-control" placeholder="адрес" ng-model="client.address"></textarea>
        </div>
        <div class="form-group">
            <div class="form-gorup">
                <input type="text" class="form-control" placeholder="имя ученика" ng-model="client.name">
            </div>
        </div>
        <div class="form-group">
            <select class="form-control" ng-click="grade_year_dropdown = true"></select>
            <span class="custom-dropdown__label">
                <span ng-show="!client.grade" class="placeholder-gray">класс и год</span>
                <span ng-show="client.grade">
                    @{{ getRealGrade() ? Grades[getRealGrade()] : 'класс не указан' }}
                </span>
            </span>
            <div class="custom-dropdown" ng-show="grade_year_dropdown">
                <div ng-repeat="(grade_id, label) in Grades" class="custom-dropdown__item" ng-click="selectGrade(grade_id)">
                    @{{ label }}
                    <span class="glyphicon glyphicon-ok check-mark" ng-show="grade_id == client.grade"></span>
                </div>
                <div class="custom-dropdown__separator"></div>
                <div ng-repeat="(year, label) in Years" class="custom-dropdown__item" ng-click="selectYear(year)">
                    @{{ label }}
                    <span class="glyphicon glyphicon-ok check-mark" ng-show="year == client.year"></span>
                </div>
            </div>
            {{-- <select class="form-control" ng-model='client.grade'
                ng-options='+(grade_id) as label for (grade_id, label) in Grades'>
                <option value="">выберите класс</option>
            </select> --}}
            {{-- <select class="form-control" ng-model='client.grade'>
                <option value="">выберите класс</option>
                <option ng-repeat='(grade_id, label) in Grades' ng-value='grade_id' ng-selected='client.grade == grade_id'>@{{ label }}</option>
            </select> --}}
        </div>
    </div>
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group">
                    <div ng-if='client !== undefined'>
                        <phones entity="client"></phones>
                        <email entity='client'></email>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <span class="link-like" ng-click="showMap()">метки</span> (@{{ client.markers.length }})
                </div>
            </div>
        </div>
    </div>
</div>
