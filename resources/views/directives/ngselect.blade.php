<select class='form-control' ng-model='model'>
    <option ng-if='noneText' selected value="">@{{ noneText }}</option>
    <option ng-if='noneText' disabled>──────────────</option>
    <option
        ng-repeat='(object_id, label) in object'
        value='@{{object_id}}'
    >@{{ label }}</option>
</select>
