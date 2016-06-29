<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Attachment;

class SetChecked extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:checked {--truncate} {--one} {--two} {--three} {--four}';

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
        if ($this->option('truncate')) {
            $this->info('Truncating...');
            DB::table('attachments')->update([
                'checked' => 0
            ]);
        }
        if ($this->option('one')) {
            $this->_one();
        }
        if ($this->option('two')) {
            $this->_two();
        }
        if ($this->option('three')) {
            $this->_three();
        }
        // if ($this->option('four')) {
        //     $this->_four();
        // }
    }

    private function _one()
    {
        $attachments = Attachment::whereHas('archive', function($query) {
            $query->whereNullOrZero('total_lessons_missing');
        })->where('date', '<=', '2015-03-01')->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) = 0')->get();

        $bar = $this->output->createProgressBar($attachments->count());

        foreach ($attachments as $attachment) {
            $date = strtotime("+7 day", $attachment->date);

            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('attachment_id', $attachment->id)->update(['date' => date('Y-m-d', $date)]);

            $bar->advance();
        }
        $bar->finish();
    }

    private function _two()
    {
        $attachments = DB::table('attachments')->join('account_datas', function($join) {
            $join->on('attachments.tutor_id', '=', 'account_datas.id')
                 ->on('account_datas.date', '=', DB::raw('
                    (SELECT MAX(date)
                    FROM account_datas ad
                    WHERE attachments.tutor_id = ad.tutor_id)
                '));
            })
            ->join('archives', 'attachments.id', '=', 'archives.attachment_id')
            ->join('clients', 'attachments.client_id', '=', 'clients.id')
            ->where('account_datas.date', '<=', '2015-07-15')
            ->where('clients.grade', 12)
            ->whereNullOrZero('archives.total_lessons_missing')
            ->get([
                'attachments.id',
                DB::raw('archives.id as archive_id'),
                'account_datas.date',
            ]);


        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('id', $attachment->archive_id)->update(['date' => $attachment->date]);

            $bar->advance();
        }
        $bar->finish();
    }

    private function _three()
    {
        $attachments = DB::table('attachments')->join('account_datas', function($join) {
            $join->on('attachments.tutor_id', '=', 'account_datas.id')
                 ->on('account_datas.date', '=', DB::raw('
                    (SELECT MAX(date)
                    FROM account_datas ad
                    WHERE attachments.tutor_id = ad.tutor_id)
                '));
            })
            ->join('archives', 'attachments.id', '=', 'archives.attachment_id')
            ->where('account_datas.date', '<=', '2015-03-01')
            ->whereNullOrZero('archives.total_lessons_missing')
            ->get([
                'attachments.id',
                DB::raw('archives.id as archive_id'),
                'account_datas.date',
            ]);


        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('id', $attachment->archive_id)->update(['date' => $attachment->date]);

            $bar->advance();
        }
        $bar->finish();
    }
}
