<script>
    angular.module('Egerep')
        .value('PhoneFields', {!! json_encode(\App\Traits\Person::$phone_fields) !!})
</script>
