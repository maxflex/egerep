{{-- <select class="form-control" id='sp-tutor-grades'
    ng-model="model"
    ng-options="object_id as label for (object_id, label) in object">
</select> --}}
<select class="form-control" id='sp-tutor-grades' ng-model="model">
    <option ng-repeat='(object_id, label) in object' ng-selected='model.indexOf(object_id) > -1'>@{{ label }}</option>
</select>
