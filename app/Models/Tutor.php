<?php

namespace App\Models;

use Log;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marker;

class Tutor extends Model
{
    protected $connection = 'egecrm';
    protected $table = 'teachers';

    protected $fillable =  ["first_name", "last_name", "middle_name", "email",
        "phone", "phone2", "phone3", "gender", "birth_year", "start_career_year",
        "tb", "lk", "js", "approved", "contacts", "price", "education", "achievements",
        "preferences", "experience", "current_work", "tutoring_experience",
        "students_category", "impression", "schedule", "public_desc", "public_price",
        "markers", "svg_map", 'subjects', 'grades', 'id_a_pers', 'departure_price'
    ];

    protected $appends = ['full_name', 'has_photo'];
    protected $with = ['markers'];
    // protected $guarded = ['id', 'created_at', '$promise', '$resolved', 'full_name', 'has_photo'];

    const UPLOAD_DIR = "img/tutors/";

    public function getSubjectsAttribute($value)
    {
        return empty($value) ? null : explode(',', $value);
    }
    public function setSubjectsAttribute($value)
    {
        $this->attributes['subjects'] = implode(',', $value);
    }

    public function getGradesAttribute($value)
    {
        return explode(',', $value);
    }
    public function setGradesAttribute($value)
    {
        $this->attributes['grades'] = implode(',', $value);
    }

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->last_name, $this->first_name, $this->middle_name]);
    }

    public function getHasPhotoAttribute()
    {
        return file_exists(self::UPLOAD_DIR . $this->id . ".jpg");
    }

    public function getSvgMapAttribute($value)
    {
        return empty($value) ? null : explode(',', $value);
    }

    public function setSvgMapAttribute($value)
    {
        if ($value) {
            $this->attributes['svg_map'] = implode(',', $value);
        }
    }

    public function markers()
    {
        return $this->morphMany('App\Models\Marker', 'markerable');
    }

    public function save(array $options = [])
    {
        $this->_saveMarkers();
        parent::save($options);
    }


    private function _saveMarkers()
    {
        $this->markers()->delete();
        foreach ($this->markers as $data) {
            $this->markers()->create(json_decode($data, true));
        }
        unset($this->markers);
    }

    protected static function boot()
    {
        static::saving(function($model) {
            cleanNumbers($model);
        });
    }
}
