<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tutor;

class TransferDebt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:debt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer tutor debt';

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
        $this->info('Starting debt transfer');

        $teachers = \DB::connection('egerep')->table('repetitors')->get();
        $bar = $this->output->createProgressBar(count($teachers));

        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', $teacher->id);
            // если преподавателя нет в базе
            if ($tutor->exists()) {
                $tutor->update([
                    'debt' => $teacher->debet
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
    }
}
