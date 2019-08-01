<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\User;
use DB;

class TransferUserIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:transfer-user-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer userIds';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // tutors – responsible_user_id
        // requests – user_id_created
        // emails – id_user
        // mango???
        $users = User::pluck('id', 'old_id')->all();
        $tables = [
            'accounts', 'account_payments', 'archives', 'attachments', 'attendance',
            'backgrounds', 'comments', 'efficency_data', 'logs', 'notifications',
            'payments', 'planned_accounts', 'requests', 'request_lists', 'reviews',
            'sms',
        ];

        DB::beginTransaction();

        foreach($tables as $table) {
            foreach($users as $oldId => $newId) {
                DB::statement("update {$table} set user_id={$newId} where user_id={$oldId}");
            }
        }

        foreach($users as $oldId => $newId) {
            DB::statement("update tutors set responsible_user_id={$newId} where responsible_user_id={$oldId}");
            DB::statement("update requests set user_id_created={$newId} where user_id_created={$oldId}");
            DB::statement("update emails set id_user={$newId} where id_user={$oldId}");
        }

        DB::commit();
    }
}
