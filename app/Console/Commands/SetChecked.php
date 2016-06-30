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
    protected $signature = 'once:checked {--truncate} {--one} {--two} {--three} {--four} {--ten} {--twelve}';

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
        if ($this->option('four')) {
            $this->_four();
        }
        if ($this->option('ten')) {
            $this->_ten();
        }
        if ($this->option('twelve')) {
            $this->_twelve();
        }
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
        $attachments = DB::table('attachments')->join(DB::raw('(
              SELECT MAX(date) as last_lesson_date, tutor_id, client_id
              FROM account_datas
              GROUP BY tutor_id, client_id
          ) ad'), function($join) {
            $join->on('attachments.tutor_id', '=', 'ad.tutor_id')
                 ->on('attachments.client_id', '=', 'ad.client_id');
            })
            ->join('archives', 'attachments.id', '=', 'archives.attachment_id')
            ->join('clients', 'attachments.client_id', '=', 'clients.id')
            ->where('ad.last_lesson_date', '<=', '2015-07-15')
            ->where('clients.grade', 12)
            ->whereNullOrZero('archives.total_lessons_missing')
            ->get([
                'attachments.id',
                DB::raw('archives.id as archive_id'),
                'ad.last_lesson_date',
            ]);


        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('id', $attachment->archive_id)->update(['date' => $attachment->last_lesson_date]);

            $bar->advance();
        }
        $bar->finish();
    }

    private function _three()
    {
        $attachments = DB::table('attachments')->join(DB::raw('(
              SELECT MAX(date) as last_lesson_date, tutor_id, client_id
              FROM account_datas
              GROUP BY tutor_id, client_id
          ) ad'), function($join) {
            $join->on('attachments.tutor_id', '=', 'ad.tutor_id')
                 ->on('attachments.client_id', '=', 'ad.client_id');
            })
            ->join('archives', 'attachments.id', '=', 'archives.attachment_id')
            ->where('ad.last_lesson_date', '<=', '2015-03-01')
            ->whereNullOrZero('archives.total_lessons_missing')
            ->get([
                'attachments.id',
                DB::raw('archives.id as archive_id'),
                'ad.last_lesson_date',
            ]);

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('id', $attachment->archive_id)->update(['date' => $attachment->last_lesson_date]);

            $bar->advance();
        }
        $bar->finish();
    }

    public function _four()
    {
        $attachments = DB::table('attachments')->whereIn('id', [3633,4218,4800,6215,6547,6844,7349,7350,7358,7359,7362,7364,7371,7374,7379,7381,7383,7389,7400,7402,7405,7411,7413,7429,7430,7434,7449,7452,7454,7457,7464,7468,7471,7481,7495,7503,7508,7512,7514,7518,7522,7527,7535,7540,7543,7548,7550,7561,7570,7578,7581,7586,7615,7620,7622,7635,7636,7655,7666,7680,7685,7687,7705,7726,7735,7738,7744,7751,7757,7769,7774,7775,7778,7786,7787,7791,7793,7805,7806,7808,7813,7816,7822,7826,7831,7832,7842,7846,7851,7853,7856,7863,7865,7870,7878,7879,7888,7893,7897,7902,7903,7905,7906,7908,7911,7912,7922,7928,7929,7930,7935,7938,7943,7945,7949,7958,7960,7964,7967,7974,7979,8004,8005,8008,8011,8017,8020,8026,8027,8041,8044,8054,8056,8062,8063,8066,8071,8074,8082,8083,8090,8092,8096,8099,8104,8111,8114,8122,8123,8124,8130,8145,8148,8152,8156,8157,8159,8162,8170,8178,8183,8202,8203,8221,8225,8227,8235,8237,8238,8239,8246,8249,8260,8264,8273,8281,8283,8284,8287,8294,8302,8305,8307,8316,8318,8323,8326,8329,8334,8339,8344,8347,8357,8364,8370,8371,8377,8378,8379,8380,8385,8392,8395,8396,8397,8399,8403,8405,8406,8407,8408,8424,8425,8429,8430,8435,8440,8442,8443,8448,8454,8456,8464,8466,8470,8472,8482,8503,8504,8505,8522,8524,8538,8545,8555,8560,8567,8569,8577,8579,8583,8601,8626,8637,8643,8644,8654,8656,8657,8666,8704,8708,8716,8727,8730,8741,8746,8748,8751,8759,8771,8773,8777,8780,8781,8785,8795,8800,8817,8830,8845,8849,8850,8853,8862,8871,8875,8878,8889,8890,8900,8920,8928,8936,8957,8959,8962,8969,8972,8976,8985,8986,9000,9013,9019,9022,9025,9028,9034,9036,9042,9052,9060,9062,9064,9065,9070,9074,9075,9080,9086,9089,9092,9102,9105,9115,9116,9119,9124,9127,9132,9141,9143,9145,9147,9157,9158,9165,9167,9169,9171,9173,9180,9189,9190,9196,9212,9213,9214,9218,9220,9227,9229,9236,9239,9242,9244,9252,9260,9264,9271,9273,9289,9294,9297,9304,9308,9309,9310,9316,9317,9321,9324,9326,9335,9336,9344,9345,9349,9350,9351,9352,9361,9362,9371,9375,9391,9396,9398,9400,9412,9419,9420,9422,9425,9432,9442,9445,9454,9456,9458,9459,9466,9474,9482,9483,9487,9489,9490,9492,9494,9497,9504,9521,9522,9524,9527,9528,9531,9532,9546,9550,9553,9555,9557,9565,9568,9570,9573,9579,9582,9593,9596,9602,9605,9612,9615,9617,9619,9621,9627,9631,9634,9639,9658,9659,9660,9669,9671,9678,9697,9700,9720,9748,9779,9783,9789,9797,9799,9809,9813,9816,9832,9856,9862,9863,9869,9884,9892,9893,9895,9899,9908,9922,9924,9928,9934,9935,9939,9946,9948,9971,9973,9978,9986,9987,9988,10034,10035,10037,7753,7369,8923,9217,9563,8296,7742,9410,8369,8105,7802,8879,8300,9103,5295,8581,8897,9018,7741,8426,7918,8630,9050,8903,8173,9114,7789,8500,8736,8544,8607,9015,9032,9006,8609,8491,8195,8402,7695,9224,8718,8758,8662,9118,9146,7690,8515,8769,8576,7783,8346,9794,8226,7660,8536,8534,9915,8501,8451,8590,8518,8455,8680,7692,8043,7585,8279,8137,9706,7360,8089,7356,9238,8218,8197,9269,8950,7697,8412,8931,7838,8018,7799,8255,7366,8350,8230,8804,9767,9657,9314,7743,9735,9736,9852,9849,8361,9079,7823,9610,7376,8733,7899,8084,8803,8164,7407,9682,9275,7886,8925,7571,7752])->get();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            $date = strtotime("+7 day", strtotime($attachment->date));

            DB::table('attachments')->where('id', $attachment->id)->update(['checked' => 1]);
            DB::table('archives')->where('attachment_id', $attachment->id)->update(['date' => date('Y-m-d', $date)]);

            $bar->advance();
        }
        $bar->finish();
    }

    public function _ten()
    {
        $attachments = Attachment::doesntHave('review')->whereHas('archive', function($query) {
            $query->whereNullOrZero('total_lessons_missing');
        })->where('date', '<=', '2015-07-01')->whereRaw('(SELECT COUNT(*) FROM account_datas ad WHERE ad.tutor_id = attachments.tutor_id AND ad.client_id = attachments.client_id) = 0')->get();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('reviews')->insert([
                'attachment_id' => $attachment->id,
                'user_id'       => 0,
                'created_at'    => now(),
                'score'         => 11,
                'state'         => 'unpublished',
            ]);
        }
        $bar->finish();
    }

    public function _twelve()
    {
        $attachments = Attachment::doesntHave('review')->whereHas('archive', function($query) {
            $query->where('date', '<=', '2015-08-01');
        })->get();

        $bar = $this->output->createProgressBar(count($attachments));

        foreach ($attachments as $attachment) {
            DB::table('reviews')->insert([
                'attachment_id' => $attachment->id,
                'user_id'       => 0,
                'created_at'    => now(),
                'score'         => 11,
                'state'         => 'unpublished',
            ]);
        }
        $bar->finish();
    }
}
