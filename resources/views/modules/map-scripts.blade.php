<script src="//maps.google.ru/maps/api/js?key=AIzaSyAXXZZwXMG5yNxFHN7yR4GYJgSe9cKKl7o&libraries=places"></script>
<script src="{{ asset('/js/maps.js', isProduction()) }}"></script>
@if (isset($clusterer) && $clusterer)
    <script src="{{ asset('/js/markerclusterer.js', isProduction()) }}"></script>
@endif
