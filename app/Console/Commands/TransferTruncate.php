<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;
use App\Models\Marker;
use DB;

class TransferTruncate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all tables';

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
        $this->line('Starting truncate...');
        static::truncateTables();
        $this->info('');
    }

    public static function truncateTables()
	{
		DB::statement("DELETE FROM `attachments`");
		DB::statement("ALTER TABLE `attachments` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `reviews`");
		DB::statement("ALTER TABLE `reviews` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `archives`");
		DB::statement("ALTER TABLE `archives` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `request_lists`");
		DB::statement("ALTER TABLE `request_lists` AUTO_INCREMENT=1");
		Comment::where('entity_type', 'request')->delete();
		DB::statement("DELETE FROM `requests`");
		DB::statement("ALTER TABLE `requests` AUTO_INCREMENT=1");
		Marker::where('markerable_type', 'App\Models\Client')->delete();
		DB::statement("DELETE FROM `accounts`");
		DB::statement("ALTER TABLE `accounts` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `account_datas`");
		DB::statement("ALTER TABLE `account_datas` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `clients`");
		DB::statement("ALTER TABLE `clients` AUTO_INCREMENT=1");
	}
}
