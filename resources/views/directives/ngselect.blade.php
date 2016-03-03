<select class='form-control'>
    <option ng-if='noneText' selected value="">@{{ noneText }}</option>
    <option ng-if='noneText' disabled>──────────────</option>
    <option
        ng-repeat='(object_id, label) in object'
        ng-model='model'
    >@{{ label }}</option>
</select>
