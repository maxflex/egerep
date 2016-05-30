<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Marker;
use App\Models\Metro;
use App\Models\Request;
use App\Models\Account;
use App\Traits\Markerable;
use App\Traits\Person;
use App\Events\ResponsibleUserChanged;

class Tutor extends Model
{
    use Markerable;
    use Person;

    public $timestamps = false;
    
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
        'debt',
        'debt_comment'
    ];

    protected $appends = [
        'has_photo_original',
        'has_photo_cropped',
        'photo_cropped_size',
        'photo_original_size',
        'photo_url',
        'age'
    ];

    // protected $with = ['markers'];

    protected static $commaSeparated = ['svg_map', 'subjects', 'grades', 'branches'];
    protected static $virtual = ['banned'];

    const UPLOAD_DIR = '/img/tutors/';
    const NO_PHOTO   = 'no-profile-img.gif';
    const USER_TYPE  = 'TEACHER';

    // ------------------------------------------------------------------------

    public function accounts()
    {
        // последние 60 дней с момента начальной стыковки
        return $this->hasMany('App\Models\Account');
        // ->whereRaw("date_end > DATE_SUB((SELECT date_end FROM accounts WHERE tutor_id=" . $this->id . " ORDER BY date_end DESC LIMIT 1), INTERVAL 60 DAY)");
    }

    public function attachments($hide = null)
    {
        $query = $this->hasMany('App\Models\Attachment');
        if ($hide !== null) {
            $query->where('hide', $hide);
        }
        return $query;
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

    /**
     * Последняя задолженность
     */
     public function getLastAccountInfoAttribute()
     {
         return DB::table('accounts')->select('debt', 'debt_type', 'date_end')->where('tutor_id', $this->id)->orderBy('date_end', 'desc')->first();
        //  return $this->accounts()->take(1)->orderBy('date_end', 'desc')->first();
        //  return Account::where('tutor_id', $this->id)->orderBy('date_end', 'desc')->select('debt', 'debt_type')->first();
     }

    /**
     * Данные по встречам в определенным периоде с момента последней встречи.
     */
    public function getLastAccountsAttribute()
    {
        $query = $this->accounts();

        // если отчетности нет, возвращаем пустой массив, дальше ничего делать не нужно
        if (! $query->exists()) {
            return [];
        }

        if ($this->current_period) {
            // находим отчетность перед date_limit
            $date_limit = DB::table('accounts')->where('tutor_id', $this->id)
                                               ->where('date_end', '<', $this->getDateLimit())
                                               ->orderBy('date_end', 'desc')
                                               ->take(1)
                                               ->skip($this->current_period - 1)
                                               ->value('date_end');
            // если дата не нашлась, то всё уже отобразили
            if (! $date_limit) {
                return null;
            }
        } else {
            $date_limit = $this->getDateLimit();
        }
        return $query
            ->where('date_end', '>=', $date_limit)
            ->orderBy('date_end', 'asc')
            ->get();
    }

    public function getBannedAttribute()
    {
        // @check: does this run 2 separate queries?
        return $this->user ? $this->user->banned : false;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->has_photo_cropped) {
            $photo = $this->id . '.' . $this->photo_extension;
        } else {
            $photo = static::NO_PHOTO;
        }
        return substr(static::UPLOAD_DIR, 1) . $photo;
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
        if ($this->has_photo_original) {
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
         return Request::join('request_lists', 'requests.id', '=', 'request_lists.request_id')
                    ->join('attachments', 'attachments.request_list_id', '=', 'request_lists.id')
                    ->where('attachments.tutor_id', $this->id)
                    ->groupBy('requests.client_id')
                    ->pluck('requests.client_id');
     }

    /**
     * Получить ID всех клиентов преподавателя для создания списка отчетности
     * $hide – получать только не скрытых клиентов по умолчанию
     */
    public function getAttachmenClients($hide = 0, $with_lessons_count = false)
    {
        $clients = [];

        foreach ($this->attachments($hide)->get() as $attachment) {
            if ($attachment->requestList && $attachment->requestList->request) {
                $client = [
                    'id'                    => $attachment->requestList->request->client_id,
                    # @todo: заменить на link_url
                    'link'                  => "requests/{$attachment->requestList->request->id}/edit#{$attachment->requestList->id}#{$attachment->id}",
                    'attachment_date'       => $attachment->getOriginal('date'),
                    'archive_date'          => $attachment->archive ? $attachment->archive->getOriginal('date') : null,
                    'attachment_created_at' => $attachment->getOriginal('created_at'),
                    'attachment_id'         => $attachment->id,
                    'name'                  => $attachment->requestList->request->client->name,
                    'grade'                 => $attachment->requestList->request->client->grade,
                ];
                if ($with_lessons_count) {
                    $client['lessons_count'] = AccountData::where('client_id', $attachment->requestList->request->client_id)
                                                            ->where('tutor_id', $this->id)
                                                            ->count();
                }
                $clients[] = $client;
            }
        }

        // сортируем по дате и времени реквизитов клиента
        usort($clients, function($a, $b) {
            return $a['attachment_created_at'] > $b['attachment_created_at'];
        });

        return $clients;
    }

    /**
     * Получить количество встреч
     */
    public function getMeetingCount()
    {
        return $this->accounts()->count();
    }

    /**
     * Получить кол-во  клиентов
     */
    public function clientsCount($hide = null)
    {
        return $this->attachments($hide)->count();
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

    /**
     * Данные по встречам 60 дней с момента последней встречи
     *
     */
     public function withLastAccounts($current_period = 0)
     {
        $this->current_period = $current_period;
        $this->append('last_accounts');
        return $this;
     }

     /**
      * Лимит даты для отображения начальной отчетности
      * (дата последней встречи - 60 дней)
      */
     public function getDateLimit()
     {
         return Account::select(DB::raw("DATE_SUB(date_end, INTERVAL 60 DAY) as date_limit"))
                            ->where('tutor_id', $this->id)->orderBy('date_end', 'desc')->pluck('date_limit')->first();
     }

    /**
     * @param string $type      initial|month|year|all.
     * @return int              Days for period.
     */
     private function _defineAccountDays($type = 'initial') {
         switch ($type) {
             case 'month':
                 return 90;
             case 'year':
                 return 365;
             case 'all':
                 return 0;
             default:
                 return 60;
         }
     }

    /**
 	 * Соответствия межу ID преподавателей
     * удалить после обновления a-perspektiva.ru
 	 */
 	public static function newTutorId($tutor_id)
 	{
 		$new_tutor_id = static::where('id_a_pers', $tutor_id)->pluck('id')->first();
 		return $new_tutor_id ? $new_tutor_id : null;
 	}
}
