<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Archive;
use App\Models\Attachment;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Account;
use App\Models\RequestList;
use App\Events\RecalcTutorDebt;

class RecalcTutorDebtTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $_SESSION['user'] = User::find(69);
    }


    /**
     * АРХИВАЦИЯ
     */



    /**
     * @test
     * @group archive
     */
    public function archiveDateChanged()
    {
        $this->expectsEvents(RecalcTutorDebt::class);

        // изменение даты
        $archive = Archive::first();
        $archive->date = '01.01.2005';
        $archive->save();
    }

    /**
     * @test
     * @group archive
     */
    public function archiveDateNotChanged()
    {
        $this->doesntExpectEvents(RecalcTutorDebt::class);

        // изменение даты
        $archive = Archive::first();
        $archive->date = $archive->date;
        $archive->save();
    }

    /**
     * @test
     * @group archive
     */
    public function archiveCreated()
    {
        $this->expectsEvents(RecalcTutorDebt::class);
        Attachment::doesntHave('archive')->first()->archive()->create([]);
    }

    /**
     * @test
     * @group archive
     */
    public function archiveDeleted()
    {
        $this->expectsEvents(RecalcTutorDebt::class);
        Archive::first()->delete();
    }


    /**
     * СТЫКОВКИ
     */


     /**
      * @test
      * @group attachment
      */
     public function attachmentDateChanged()
     {
         $this->expectsEvents(RecalcTutorDebt::class);

         // изменение даты
         $attachment = Attachment::first();
         $attachment->date = '01.01.2005';
         $attachment->save();
     }

     /**
      * @test
      * @group attachment
      */
     public function attachmentDateNotChanged()
     {
         $this->doesntExpectEvents(RecalcTutorDebt::class);

         // изменение даты
         $attachment = Attachment::first();
         $attachment->date = $attachment->date;
         $attachment->save();
     }

     /**
      * @test
      * @group attachment
      */
     public function attachmentForecastChanged()
     {
         $this->expectsEvents(RecalcTutorDebt::class);

         // изменение даты
         $attachment = Attachment::first();
         $attachment->forecast = 9752;
         $attachment->save();
     }

     /**
      * @test
      * @group attachment
      */
     public function attachmentForecastNotChanged()
     {
         $this->doesntExpectEvents(RecalcTutorDebt::class);

         // изменение даты
         $attachment = Attachment::first();
         $attachment->forecast = $attachment->forecast;
         $attachment->save();
     }

     /**
      * @test
      * @group attachment
      */
     public function attachmentCreated()
     {
         $this->expectsEvents(RecalcTutorDebt::class);
         Attachment::create([
             'tutor_id' => Tutor::value('id'),
             'request_list_id' => RequestList::value('id'),
        ]);
     }

     /**
      * @test
      * @group attachment
      */
     public function attachmentDeleted()
     {
         $this->expectsEvents(RecalcTutorDebt::class);
         Attachment::first()->delete();
     }


     /**
      * ПРЕПОДАВАТЕЛЬ
      */


      /**
       * @test
       * @group tutor
       */
      public function tutorDebtorChanged()
      {
          $this->expectsEvents(RecalcTutorDebt::class);

          $tutor = Tutor::first();
          $tutor->debtor = !$tutor->debtor;
          $tutor->save();
      }

      /**
       * @test
       * @group tutor
       */
      public function tutorDebtorNotChanged()
      {
          $this->doesntExpectEvents(RecalcTutorDebt::class);

          $tutor = Tutor::first();
          $tutor->debtor = $tutor->debtor;
          $tutor->first_name = 'test';
          $tutor->save();
      }


      /**
       * ВСТРЕЧИ
       */


       /**
        * Изменение даты ПОСЛЕДНЕЙ встречи
        * @test
        * @group account
        */
       public function lastAccountDateChanged()
       {
           $this->expectsEvents(RecalcTutorDebt::class);

           // изменение даты
           $account = Account::where('tutor_id', 5)->orderBy('date_end', 'desc')->first();
           $account->date_end = '2017-12-31';
           $account->save();
       }

       /**
        * Редактирование ПОСЛЕДНЕЙ встречи без изменения даты
        * @test
        * @group account
        */
       public function lastAccountDateNotChanged()
       {
           $this->doesntExpectEvents(RecalcTutorDebt::class);

           // изменение даты
           $account = Account::where('tutor_id', 5)->orderBy('date_end', 'desc')->first();
           $account->date_end = $account->date_end;
           $account->received = 963;
           $account->save();
       }

       /**
        * Изменение даты НЕ ПОСЛЕДНЕЙ встречи
        * @test
        * @group account
        */
       public function notLastAccountDateChanged()
       {
           $this->doesntExpectEvents(RecalcTutorDebt::class);

           // изменение даты
           $account = Account::where('tutor_id', 5)->orderBy('date_end', 'asc')->first();
           $account->date_end = (new \DateTime($account->date_end))->modify('+7 days')->format('Y-m-d');
           $account->save();
       }

       /**
        * Дата НЕ ПОСЛЕДНЕЙ встречи не изменилась
        * @test
        * @group account
        */
       public function notLastAccountDateNotChanged()
       {
           $this->doesntExpectEvents(RecalcTutorDebt::class);

           // изменение даты
           $account = Account::where('tutor_id', 5)->orderBy('date_end', 'asc')->first();
           $account->received = 346;
           $account->save();
       }

       /**
        * @test
        * @group account
        */
       public function accountCreated()
       {
           $this->expectsEvents(RecalcTutorDebt::class);
           Account::create([
               'tutor_id' => Tutor::value('id'),
               'date_end' => '2017-12-31',
           ]);
       }

       /**
        * @test
        * @group account
        */
       public function accountDeleted()
       {
           $this->expectsEvents(RecalcTutorDebt::class);
           Account::first()->delete();
       }
}
