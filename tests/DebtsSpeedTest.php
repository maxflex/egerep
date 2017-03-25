<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Debt;

class DebtsSpeedTest extends TestCase
{
    private $dates = [
        '2017-02-23',
        '2017-02-24',
        '2017-02-25',
        '2017-02-26',
        '2017-02-27',
        '2017-02-28',
        '2017-03-01',
        '2017-03-02',
        '2017-03-03',
        '2017-03-04',
        '2017-03-05',
        '2017-03-06',
        '2017-03-07',
        '2017-03-08',
        '2017-03-09',
        '2017-03-10',
        '2017-03-11',
        '2017-03-12',
        '2017-03-13',
        '2017-03-14',
        '2017-03-15',
        '2017-03-16',
        '2017-03-17',
        '2017-03-18',
        '2017-03-19',
        '2017-03-20',
        '2017-03-21',
        '2017-03-22',
        '2017-03-23',
        '2017-03-24',
    ];
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $date = '2017-03-20';
        dump(Debt::total([
            'date_start' => $date,
            'date_end' => $date
        ]));
        // foreach($this->dates as $index => $date) {
        //     if ($index == 1) {
        //         break;
        //     }
        //     Debt::total($date, $date);
        // }
        $this->assertTrue(true);
    }
}
