<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class ExpenditureGroup extends Model
{
    protected $table = 'payment_expenditure_groups';

    protected $fillable = ['name', 'position'];

    public function data()
    {
        return $this->hasMany(Expenditure::class, 'group_id')->orderBy('position');
    }

    public static function getAll()
    {
        $data = ExpenditureGroup::with('data')->orderBy('position')->get();
        $empty_group = new ExpenditureGroup(['name' => 'Остальное']);
        $empty_group->data = Expenditure::whereNull('group_id')->orderBy('position')->get();
        $data[] = $empty_group;
        return $data;
    }
}
