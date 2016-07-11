<script>
    angular.module('Egerep')
        .value('PhoneFields', {!! json_encode(\App\Traits\Person::$phone_fields) !!})
        .value('LogColumns',  {!! json_encode(\App\Models\Service\Log::COLUMNS) !!})
</script>
