<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Client;
use App\Models\Tutor;
use App\Models\Service\PhoneDuplicate;

class PhoneDuplicateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test номер добавляется в дубли
     *
     */
    public function it_adds_a_duplicate()
    {
        $this->_it_adds_a_duplicate(Client::class);
        $this->_it_adds_a_duplicate(Tutor::class);
    }

    private function _it_adds_a_duplicate($Class)
    {
        $phone = '79111112222';
        $Class::create(compact('phone'));
        $Class::create(compact('phone'));
        $this->assertEquals(true, PhoneDuplicate::exists($phone, $Class::ENTITY_TYPE));
    }

    /**
     * @test номер удаляется из дублей
     *
     */
    public function it_removes_a_duplicate()
    {
        $this->_it_removes_a_duplicate(Client::class);
        $this->_it_removes_a_duplicate(Tutor::class);
    }

    private function _it_removes_a_duplicate($Class)
    {
        $phone = '79111112222';

        $Class::create(compact('phone'));
        $new_client = $Class::create(compact('phone'));
        $this->assertEquals(true, PhoneDuplicate::exists($phone, $Class::ENTITY_TYPE));

        $new_client->phone = '79111112223';
        $new_client->save();
        $this->assertEquals(false, PhoneDuplicate::exists($phone, $Class::ENTITY_TYPE));
    }

    /**
     * @test меняем номер у одного и того же клиента
     */
    public function it_changes_within_single_entity()
    {
        $this->_it_changes_within_single_entity(Client::class);
        $this->_it_changes_within_single_entity(Tutor::class);
    }

    private function _it_changes_within_single_entity($Class)
    {
        $phone  = '79111112222';
        $phone2 = '79111112223';

        $client = $Class::create(compact('phone', 'phone2'));

        $client->phone  = $phone2;
        $client->phone2 = $phone;

        $client->save();

        $this->assertEquals(false, PhoneDuplicate::exists($phone, $Class::ENTITY_TYPE));
        $this->assertEquals(false, PhoneDuplicate::exists($phone2, $Class::ENTITY_TYPE));
    }
}
