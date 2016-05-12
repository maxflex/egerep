<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tutor;

class TutorRetina extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutor:retina';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Оставить только Retina изображения, остальные удалить';

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
        $tutors = Tutor::where('photo_extension', '<>', '')->take(100)->get();

        $this->line('Starting: ' . $tutors->count() . ' tutors');

        foreach ($tutors as $tutor) {
            @unlink($tutor->photoPath());
            rename($tutor->photoPath('@2x'), $tutor->photoPath());
        }

        $this->info('Success!');
    }
}
