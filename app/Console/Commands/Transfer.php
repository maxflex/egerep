<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;

class TransferAttachmentCreatedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:attachment_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer attachment created_at bugfix';

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
        $this->line('Starting transfer...');

        $attachments = Attachment::all();

		$bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            $created_at = \DB::connection('egerep')->table('repetitor_clients')
                                                        ->where('repetitor_id', $attachment->tutor->id_a_pers)
                                                        ->where('client_id', $attachment->client->id_a_pers)
                                                        ->where('task_id', $attachment->requestList->request->id_a_pers);
            if ($created_at->exists()) {
                $created_at = $created_at->pluck('created')[0];
                Attachment::where('id', $attachment->id)->update([
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
