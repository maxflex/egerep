<?php
    $string = "1234567-7654321, sdfdsf";

    preg_match_all("/(\b[\d]{7}\b)/", $string, $output_array);

    echo '<pre>';
    print_r($output_array);
    echo '</pre>';
