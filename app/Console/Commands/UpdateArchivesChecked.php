<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateArchivesChecked extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:archive_state';

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
        $archives = \App\Models\Archive::where('state', 'possible')->get();

        $bar = $this->output->createProgressBar(count($archives));

        $update_data = [
            'state'   => 'impossible',
            'checked' => 1,
        ];

        foreach($archives as $archive) {
            $x = $archive->total_lessons_missing + $archive->attachment->account_data_count;
            if (! $x && $archive->getOriginal('date') <= '2016-01-01') {
                \DB::table('archives')->whereId($archive->id)->update($update_data);
            }
            if ($x && $archive->getOriginal('date') <= '2015-01-01') {
                \DB::table('archives')->whereId($archive->id)->update($update_data);
            }
            $bar->advance();
        }
    }
}
