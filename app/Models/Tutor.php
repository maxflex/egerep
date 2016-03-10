<?php

namespace App\Models;

use App\Http\Requests\Request;
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
		'phone4',
		'phone_comment',
		'phone2_comment',
		'phone3_comment',
		'phone4_comment',
		'email_comment',
		'gender',
		'birth_year',
		'start_career_year',
		'tb',
		'lk',
		'js',
		'state',
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
		'departure_price',
        'list_comment',
        'responsible_user_id'
    ];
    protected $appends = ['has_photo_original', 'has_photo_cropped'];
    protected $with = ['markers'];
    protected static $commaSeparated = ['svg_map', 'subjects', 'grades'];

    const UPLOAD_DIR = '/img/tutors/';

    // ------------------------------------------------------------------------

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }

	public function responsibleUser()
	{
		return $this->belongsTo('App\Models\User');
	}

    // ------------------------------------------------------------------------

    public function getHasPhotoOriginalAttribute()
    {
        return file_exists($this->photoPath('_original'));
    }

    public function getHasPhotoCroppedAttribute()
    {
        return file_exists($this->photoPath('@2x'));
    }

    /**
     * Получить ID всех клиентов преподавателя
     */
    public function getClientIds()
    {
        $client_ids = [];

        foreach ($this->attachments as $attachment) {
            $client_ids[] = $attachment->requestList->request->client_id;
        }

        return $client_ids;
    }

    /**
     * Получить дату первой стыковки
     */
    public function getFirstAttachmentDate()
    {
        return $this->attachments()->orderBy('date')->pluck('date')->first();
    }

    // ------------------------------------------------------------------------

    public function photoPath($addon = '')
    {
        return public_path() . static::UPLOAD_DIR . $this->id . $addon . '.' . $this->photo_extension;
    }

    protected static function boot()
    {
        static::saving(function($model) {
            cleanNumbers($model);
        });
    }

    /**
     * @param QueryBuilder $query        Query Builder.
     * @param string $searchText         Last name or phone of tutor.
     * @return QueryBuilder              QueryBuilder instance.
     *
     * @todo Add phone4 field after Task #738 migrations!
     */
    public function scopeSearchByLastNameAndPhone($query, $searchText)
    {
        if ($searchText) {
            return $query->whereRaw("lower(last_name) like lower('%{$searchText}%')")
                         ->orWhere("phone", "like", "%{$searchText}%")
                         ->orWhere("phone2", "like", "%{$searchText}%")
                         ->orWhere("phone3", "like", "%{$searchText}%");

        }
    }
}
