<script>
    angular.module('Egerep')
        .value('PhoneFields', {!! json_encode(\App\Traits\Person::$phone_fields) !!})
        .value('RequestStates', {!! json_encode(array_flip(\App\Models\Request::$states)) !!})
        .value('TeacherPaymentTypes', {!! json_encode(dbFactory('teacher_payment_types')->get()) !!})
        .value('TutorStates', {!! json_encode(\App\Models\Tutor::STATES) !!})
        .value('Branches', {!! collect(dbFactory('branches')->get())->keyBy('id') !!})
</script>
