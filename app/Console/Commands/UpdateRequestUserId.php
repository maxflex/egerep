<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request;
use App\Models\Comment;

class UpdateRequestUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:request_user_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::table('requests')->where('id', 14119)->update(['user_id' => 1361]);
        \DB::table('requests')->where('id', 14025)->update(['user_id' => 1358]);
        \DB::table('requests')->where('id', 13514)->update(['user_id' => 92]);
        \DB::table('requests')->where('id', 13287)->update(['user_id' => 1353]);
        \DB::table('requests')->where('id', 13043)->update(['user_id' => 1353]);
        \DB::table('requests')->where('id', 12487)->update(['user_id' => 92]);
        \DB::table('requests')->where('id', 12466)->update(['user_id' => 1338]);
        \DB::table('requests')->where('id', 12349)->update(['user_id' => 1338]);
        \DB::table('requests')->where('id', 12292)->update(['user_id' => 61]);
        \DB::table('requests')->where('id', 12202)->update(['user_id' => 61]);
        \DB::table('requests')->where('id', 11984)->update(['user_id' => 1338]);
        \DB::table('requests')->where('id', 11863)->update(['user_id' => 92]);
        \DB::table('requests')->where('id', 11851)->update(['user_id' => 1338]);
        \DB::table('requests')->where('id', 11835)->update(['user_id' => 92]);
        \DB::table('requests')->where('id', 11827)->update(['user_id' => 61]);
        \DB::table('requests')->where('id', 11771)->update(['user_id' => 1338]);
        \DB::table('requests')->where('id', 11751)->update(['user_id' => 83]);
        \DB::table('requests')->where('id', 11691)->update(['user_id' => 101]);
        \DB::table('requests')->where('id', 11521)->update(['user_id' => 77]);
        \DB::table('requests')->where('id', 11445)->update(['user_id' => 111]);
        \DB::table('requests')->where('id', 11207)->update(['user_id' => 83]);
        \DB::table('requests')->where('id', 11106)->update(['user_id' => 92]);
        \DB::table('requests')->where('id', 11002)->update(['user_id' => 1352]);
        \DB::table('requests')->where('id', 10627)->update(['user_id' => 83]);
        \DB::table('requests')->where('id', 10498)->update(['user_id' => 56]);
        \DB::table('requests')->where('id', 10331)->update(['user_id' => 79]);
        \DB::table('requests')->where('id', 10178)->update(['user_id' => 1331]);
        \DB::table('requests')->where('id', 10110)->update(['user_id' => 77]);
        \DB::table('requests')->where('id', 10067)->update(['user_id' => 65]);
        \DB::table('requests')->where('id', 10014)->update(['user_id' => 77]);
        \DB::table('requests')->where('id', 10006)->update(['user_id' => 77]);
        \DB::table('requests')->where('id', 9997)->update(['user_id' => 1321]);
        \DB::table('requests')->where('id', 9996)->update(['user_id' => 65]);
        \DB::table('requests')->where('id', 9967)->update(['user_id' => 1326]);
        \DB::table('requests')->where('id', 9929)->update(['user_id' => 65]);
        \DB::table('requests')->where('id', 9830)->update(['user_id' => 61]);
        \DB::table('requests')->where('id', 9736)->update(['user_id' => 65]);
        \DB::table('requests')->where('id', 9728)->update(['user_id' => 65]);
        \DB::table('requests')->where('id', 9496)->update(['user_id' => 71]);
        \DB::table('requests')->where('id', 9201)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 9115)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 9088)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 8616)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 8281)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 7562)->update(['user_id' => 1320]);
        \DB::table('requests')->where('id', 7560)->update(['user_id' => 1320]);
        \DB::table('requests')->where('id', 7545)->update(['user_id' => 1320]);
        \DB::table('requests')->where('id', 7526)->update(['user_id' => 1311]);
        \DB::table('requests')->where('id', 7506)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 6219)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 5405)->update(['user_id' => 1311]);
        \DB::table('requests')->where('id', 5270)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 4050)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 3076)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 2329)->update(['user_id' => 1310]);
        \DB::table('requests')->where('id', 1976)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 1768)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 1695)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 1679)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1574)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 1509)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1477)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1475)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1474)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1473)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1451)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1430)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1416)->update(['user_id' => 1308]);
        \DB::table('requests')->where('id', 1385)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 1027)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 895)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 195)->update(['user_id' => 1]);
        \DB::table('requests')->where('id', 14627)->update(['user_id' => 1384]);
        \DB::table('requests')->where('id', 6217)->update(['user_id' => 1310]);
    }
}
