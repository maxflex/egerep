<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameStationsTwo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('stations')->whereId(15)->update([
            'title' => 'Белорусская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(196)->update([
            'title' => 'Белорусская (Замоскворецкая)'
        ]);
        \DB::table('stations')->whereId(197)->update([
            'title' => 'Каширская (Каховская)'
        ]);
        \DB::table('stations')->whereId(46)->update([
            'title' => 'Каширская (Замоскворецкая)'
        ]);
        \DB::table('stations')->whereId(47)->update([
            'title' => 'Киевская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(198)->update([
            'title' => 'Киевская (Филевская)'
        ]);
        \DB::table('stations')->whereId(187)->update([
            'title' => 'Киевская (Арбатско-Покровская)'
        ]);
        \DB::table('stations')->whereId(48)->update([
            'title' => 'Китай-город (Калужско-Рижская)'
        ]);
        \DB::table('stations')->whereId(195)->update([
            'title' => 'Китай-город (Таганско-Краснопресненская)'
        ]);
        \DB::table('stations')->whereId(190)->update([
            'title' => 'Комсомольская (Сокольническая)'
        ]);
        \DB::table('stations')->whereId(51)->update([
            'title' => 'Комсомольская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(186)->update([
            'title' => 'Кунцевская (Арбатско-Покровская)'
        ]);
        \DB::table('stations')->whereId(62)->update([
            'title' => 'Кунцевская (Филевская)'
        ]);
        \DB::table('stations')->whereId(188)->update([
            'title' => 'Курская (Арбатско-Покровская)'
        ]);
        \DB::table('stations')->whereId(63)->update([
            'title' => 'Курская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(86)->update([
            'title' => 'Октябрьская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(192)->update([
            'title' => 'Октябрьская (Калужско-Рижская)'
        ]);
        \DB::table('stations')->whereId(91)->update([
            'title' => 'Павелецкая (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(193)->update([
            'title' => 'Павелецкая (Замоскворецкая)'
        ]);
        \DB::table('stations')->whereId(92)->update([
            'title' => 'Парк культуры (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(189)->update([
            'title' => 'Парк культуры (Сокольническая)'
        ]);
        \DB::table('stations')->whereId(212)->update([
            'title' => 'Петровско-разумовская (Люблинско-Дмитровская)'
        ]);
        \DB::table('stations')->whereId(97)->update([
            'title' => 'Петровско-разумовская (Серпуховско-Тимирязевская)'
        ]);
        \DB::table('stations')->whereId(191)->update([
            'title' => 'Проспект мира (Калужско-Рижская)'
        ]);
        \DB::table('stations')->whereId(109)->update([
            'title' => 'Проспект мира (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(131)->update([
            'title' => 'Таганская (Кольцевая)'
        ]);
        \DB::table('stations')->whereId(199)->update([
            'title' => 'Таганская (Таганско-Краснопресненская)'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
