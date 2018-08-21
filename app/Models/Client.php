<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Service\Person
{
    const ENTITY_TYPE = 'client';

    public $timestamps = false;

    protected $with = ['requests', 'markers'];

    protected $appends = ['requests_count', 'grade_clean'];

    protected $fillable = [
        'name',
        'grade',
        'year',
        'address',
        'requests',
        'markers',
    ];

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function getRequestsCountAttribute()
    {
        return $this->requests()->count();
    }

    public function setRequestsAttribute($value)
    {
        foreach ($value as $request) {
            Request::find($request['id'])->update($request);
        }
    }

    public function getGradeAttribute($grade)
    {
        if ($this->attributes['year'] && $grade < 12) {
            $years_passed = academicYear() - $this->attributes['year'];
            $new_grade = $grade + $years_passed;
            return $new_grade > 12 ? 12 : $new_grade;
        }
        return $grade;
    }

    public function getGradeCleanAttribute($grade)
    {
        return $this->attributes['grade'];
    }

    /**
     * Удалить клиентов без заявок
     */
    public static function removeWithoutRequests()
    {
        // нужно, чтобы запускалось событие удаления клиента
        Client::doesntHave('requests')->get()->each(function($model) {
            $model->delete();
        });
    }

    protected static function boot()
    {
        // чтобы не забыли если что-то добавим в будущем.
        parent::boot();

        static::creating(function($model) {
            if (! $model->year) {
                $model->year = academicYear();
            }
        });
    }
}
