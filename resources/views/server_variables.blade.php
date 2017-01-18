<script>
    angular.module('Egerep')
        .value('PhoneFields', {!! json_encode(\App\Traits\Person::$phone_fields) !!})
        .value('LogColumns', {!! json_encode(\App\Models\Service\Log::getColumns()) !!})
        .value('RequestStates', {!! json_encode(array_flip(\App\Models\Request::$states)) !!})
        .value('TeacherPaymentTypes', {!! json_encode(dbFactory('teacher_payment_types')->get()) !!})
        .value('Places', {!! json_encode(dbFactory('places')->get()) !!})
        .value('Sort', {!! json_encode(dbFactory('sort')->get()) !!})
</script>
