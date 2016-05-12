<?php

namespace App\Models;

use App\Http\Requests\Request;
use Log;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marker;
use App\Models\Metro;
use App\Traits\Markerable;
use App\Traits\Person;
use App\Events\ResponsibleUserChanged;

class Tutor extends Model
{
    use Markerable;
    use Person;

    const USER_TYPE = 'TEACHER';

    protected $fillable =  [
        'first_name',
        'last_name',
        'middle_name',
        'email',
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
        'responsible_user_id',
        'lesson_duration',
        'ready_to_work',
        'comment',
        'expert_mark',
        'rubbles',
        'description',
        'public_seniority',
        'public_ege_start',
        'login',
        'password',
        'branches',
        'banned',
        'in_egecentr',
        'video_link',
    ];

    protected $appends = ['has_photo_original', 'has_photo_cropped', 'photo_cropped_size', 'photo_original_size', 'age'];
    protected $with = ['markers'];

    protected static $commaSeparated = ['svg_map', 'subjects', 'grades', 'branches'];
    protected static $virtual = ['banned'];

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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'id_entity')
                    ->where('type', static::USER_TYPE);
    }

    // ------------------------------------------------------------------------

    public function getBannedAttribute()
    {
        // @todo: does this run 2 separate queries?
        return $this->user ? $this->user->banned : false;
    }

    public function getHasPhotoOriginalAttribute()
    {
        return file_exists($this->photoPath('_original'));
    }

    public function getHasPhotoCroppedAttribute()
    {
        return file_exists($this->photoPath());
    }

    public function getPhotoCroppedSizeAttribute()
    {
        if ($this->has_photo_cropped) {
            return filesize($this->photoPath());
        } else {
            return 0;
        }
    }

    public function getPhotoOriginalSizeAttribute()
    {
        if ($this->has_photo_cropped) {
            return filesize($this->photoPath('_original'));
        } else {
            return 0;
        }
    }

    public function getAgeAttribute()
    {
        return static::getAge($this->birth_year);
    }

    public function getClientsCountAttribute()
    {
        return count($this->getClientIds());
    }

    // ------------------------------------------------------------------------

    /**
     * Получить ID всех клиентов преподавателя
     */
    public function getClientIds()
    {
        $client_ids = [];

        foreach ($this->attachments as $attachment) {
            if ($attachment->requestList && $attachment->requestList->request) {
                $client_ids[] = $attachment->requestList->request->client_id;
            }
        }

        return $client_ids;
    }

    /**
     * Получить количество встреч
     */
    public function getMeetingCount()
    {
        return $this->accounts()->count();
    }

    /**
     * Получить дату первой стыковки
     */
    public function getFirstAttachmentDate()
    {
        return $this->attachments()->orderBy('date')->pluck('date')->first();
    }

    public function photoPath($addon = '')
    {
        return public_path() . static::UPLOAD_DIR . $this->id . $addon . '.' . $this->photo_extension;
    }

    /**
     * Получить минимальное время между всеми метками репетитора и меткой клиента
     */
    public function getMinutes($client_marker)
    {
        if (! $this->markers->count()) {
            return -1;
        }

        $min_minutes = PHP_INT_MAX;
        $client_marker = (object)$client_marker;

        foreach($this->markers as $marker) {
            # сначала проверяем, есть ли общие ближайшие станции метро
            foreach ($client_marker->metros as $metro) {
                $mutual_metro = $marker->metros->where('station_id', $metro['station_id'])->first();

                # если нашлось общее метро
                if ($mutual_metro !== null) {
                    break 2;
                }
            }
        }

        # если есть общие станции метро
        if ($mutual_metro) {
            # применяем стандартный метод расчета времени между 2-мя метками
            foreach($this->markers as $marker) {
                $new_min_minutes = Metro::minutesBetweenMarkers($marker, $client_marker);
                if ($new_min_minutes < $min_minutes) {
                    $min_minutes = $new_min_minutes;
                }
            }
        } else {
            # общего метро нет
            foreach($this->markers as $marker) {
                foreach($marker->metros as $tutor_metro) {
                    foreach($client_marker->metros as $client_metro) {
                        # время от метки репетитора до ближайшей станции репетитора
                        $new_min_minutes = $tutor_metro->minutes;

                        # время от ближайшей станции репетитора до ближайшей станции ученика
                        $new_min_minutes += Metro::minutesBetweenMetros($tutor_metro->station_id, $client_metro['station_id']);

                        # время от ближайшей станции ученика до метки ученика
                        $new_min_minutes += $client_metro['minutes'];

                        if ($new_min_minutes < $min_minutes) {
                            $min_minutes = $new_min_minutes;
                        }
                    }
                }
            }
        }

        return round($min_minutes);
    }

    protected static function boot()
    {
        static::saving(function($tutor) {
            cleanNumbers($tutor);

            if ($tutor->changed(['login', 'password', 'banned'])) {
                $tutor->updateUser();
            }
        });

        static::updated(function($tutor) {
            # if responsible user changed
            if ($tutor->changed('responsible_user_id')) {
                event(new ResponsibleUserChanged($tutor));
            }
        });
    }

    public function scopeSearchByLastNameAndPhone($query, $searchText)
    {
        if ($searchText) {
            // @todo: цикл по номерам телефона
            return $query->whereRaw("lower(last_name) like lower('%{$searchText}%')")
                         ->orWhere("phone", "like", "%{$searchText}%")
                         ->orWhere("phone2", "like", "%{$searchText}%")
                         ->orWhere("phone3", "like", "%{$searchText}%")
                         ->orWhere("phone4", "like", "%{$searchText}%");

        }
    }

    /**
     * Search by status
     */
    public function scopeSearchByState($query, $state)
    {
        if (isset($state)) {
            return $query->where('state', $state);
        }
    }

    /**
     * Search by user id
     */
    public function scopeSearchByUser($query, $user_id)
    {
        if (isset($user_id)) {
            return $query->where('responsible_user_id', $user_id);
        }
    }

    /**
     * State counts
     * @return array [state_id] => state_count
     */
    public static function stateCounts($user_id)
    {
        $return = [];
        foreach (range(0, 5) as $i) {
            $query = static::where('state', $i);
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }
            $return[$i] = $query->count();
        }
        return $return;
    }

    /**
     * State counts
     * @return array [user_id] => state_count
     */
    public static function userCounts($state)
    {
        $user_ids = static::where('responsible_user_id', '>', 0)->groupBy('responsible_user_id')->pluck('responsible_user_id');
        $return = [];
        foreach ($user_ids as $user_id) {
            $query = static::where('responsible_user_id', $user_id);
            if (! empty($state)) {
                $query->where('state', $state);
            }
            $return[$user_id] = $query->count();
        }
        return $return;
    }

    /**
     * Updates corresponding user from users table
     */
    public function updateUser()
    {
        if ($this->in_egecentr) {
            User::updateOrCreate([
                'id_entity' => $this->id,
                'type'      => static::USER_TYPE,
            ], [
                'banned'    => $this->getClean('banned'),
                'login'     => $this->login,
                'password'  => $this->password,
            ]);
        }
    }
}
