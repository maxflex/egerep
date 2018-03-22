<?php

namespace App\Console\Commands\Once;

use Illuminate\Console\Command;
use App\Models\User;
use DB;

class CreateRepresentatives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once:create_reps';

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
        $students = User::where('type', 'STUDENT')->get();
        $bar = $this->output->createProgressBar(count($students));

        $this->info('Representatives...');

        foreach($students as $user_student) {
            $student = dbEgecrm('students')->whereId($user_student->id_entity)->first();
            if ($student) {
                $user_student->email = $student->email;

                $r = dbEgecrm('representatives')->whereId($student->id_representative)->first();

                if ($r) {
                    dbEgecrm('users')->insert([
                        'email' => $r->email,
                        'login' => $user_student->login,
                        "first_name" => $r->first_name,
                        "last_name" => $r->last_name,
                        "middle_name" => $r->middle_name,
                        "type" => 'REPRESENTATIVE',
                        'phone' => $r->phone,
                        "id_entity" => $r->id,
                        'updated_at' => now(),
                    ]);
                }

                $user_student->email = $student->email;
                $user_student->phone = $student->phone;
                $user_student->save();
            }
            $bar->advance();
        }
        $bar->finish();

        $this->info("\nTutors...");
        $tutors = User::where('type', 'TEACHER')->get();
        $bar = $this->output->createProgressBar(count($tutors));
        foreach($tutors as $tutor) {
            $t = DB::table('tutors')->whereId($tutor->id_entity)->first();
            if ($t) {
                $tutor->email = $t->email;
                $tutor->phone = $t->phone;
                $tutor->save();
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
