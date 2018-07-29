<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use DB;

class TransferAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:transfer_admins';

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
        dbEgecrm('admins')->truncate();

        $admins = dbEgecrm('users')->where('type', 'USER')->get();

        $bar = $this->output->createProgressBar(count($admins));
        foreach($admins as $admin) {
            extract((array)$admin);
            dbEgecrm('admins')->insert(compact('id', 'login', 'color', 'first_name', 'last_name', 'middle_name', 'phone', 'photo_extension', 'has_photo_cropped', 'salary', 'rights'));
            dbEgecrm('users')->whereId($admin->id)->update(['id_entity' => $admin->id]);
            $bar->advance();
        }
        $bar->finish();
    }
}
