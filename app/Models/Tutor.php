<?php

namespace App\Models;

use Log;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marker;
use App\Traits\Markerable;
use App\Traits\Person;

class Tutor extends Model
{
    use Markerable;
    use Person;

    protected $connection = 'egecrm';
    protected $table = 'teachers';
    protected $fillable =  [
        'first_name',
		'last_name',
		'middle_name',
		'email',
		'phone',
		'phone2',
		'phone3',
		'gender',
		'birth_year',
		'start_career_year',
		'tb',
		'lk',
		'js',
		'approved',
		'contacts',
		'price',
		'education',
		'achievements',
		'preferences',
		'experience',
		'current_work',
		'tutoring_experience',
		'students_category',
		'impression',
		'schedule',
		'public_desc',
		'public_price',
		'markers',
		'svg_map',
		'subjects',
		'grades',
		'id_a_pers',
		'departure_price'
    ];
    protected $appends = ['has_photo'];
    protected $with = ['markers'];
    protected static $commaSeparated = ['svg_map', 'subjects', 'grades'];

    const UPLOAD_DIR = 'img/tutors/';

    // ------------------------------------------------------------------------

    public function getHasPhotoAttribute()
    {
        return file_exists(self::UPLOAD_DIR . $this->id . '.jpg');
    }

    // ------------------------------------------------------------------------

    protected static function boot()
    {
        static::saving(function($model) {
            cleanNumbers($model);
        });
    }
}
