<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class TutorDepartureTransferTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStationsEquals()
    {
        $this->db->statement('set group_concat_max_len = 10000000');
        $result = $this->db->select(
            "select 
             length((select group_concat(_svg_map) from tutors where _svg_map <> '' group by 'all'))
             - 
            length(replace((select group_concat(_svg_map) from tutors where _svg_map <> '' group by 'all'),',','')) 
            as count"
        );

        $existing_stations = ($result[0])->count + $this->db->table('tutors')->where('_svg_map', '<>', '')->whereRaw("find_in_set(_svg_map, '\'')")->count();
        $generated_stations = $this->db->table('tutor_departures')->count();

        $this->assertTrue(
            $existing_stations == $generated_stations,
            "$existing_stations != $generated_stations"
        );
    }
}
