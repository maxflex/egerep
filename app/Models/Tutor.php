<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Metro;
use App\Models\Request;
use App\Models\Account;
use App\Events\ResponsibleUserChanged;

class Tutor extends Service\Person
{
    const ENTITY_TYPE = 'tutor';

    public $timestamps = false;

    protected $fillable =  [
        'first_name',
        'last_name',
        'middle_name',
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
        'description',
        'login',
        'password',
        'branches',
        'banned',
        'in_egecentr',
        'video_link',
        'debt_comment',
        'comment_extended',
        'debtor',
        'errors',
        'security_notification',
        'photo_extension',
        'egecentr_source'
    ];

    protected $appends = [
        'has_photo_original',
        'has_photo_cropped',
        'photo_cropped_size',
        'photo_original_size',
        'photo_url',
        'age',
        'clients_count'
    ];

    // protected $with = ['markers'];

    protected static $commaSeparated = ['subjects', 'grades', 'branches', 'errors'];
    protected static $virtual = ['banned'];

    const UPLOAD_DIR = '/img/tutors/';
    const NO_PHOTO   = 'no-profile-img.gif';
    const USER_TYPE  = 'TEACHER';

    const STATES = ['не установлено', 'на проверку', 'к закрытию', 'закрыто', 'к одобрению', 'одобрено'];

    // ------------------------------------------------------------------------

    public function accounts()
    {
        // последние 60 дней с момента начальной стыковки
        return $this->hasMany('App\Models\Account');
        // ->whereRaw("date_end > DATE_SUB((SELECT date_end FROM accounts WHERE tutor_id=" . $this->id . " ORDER BY date_end DESC LIMIT 1), INTERVAL 60 DAY)");
    }

    public function attachments($hide = null, $get_possible_archives = false)
    {
        $query = $this->hasMany('App\Models\Attachment');
        if(!$get_possible_archives) {
            if ($hide !== null) {
                $query->where('hide', $hide);
            }
        } else {
            $query->whereRaw("(attachments.hide = 0 or exists (select 1 from archives where archives.attachment_id = attachments.id and  archives.state = 'possible'))");
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

    public function plannedAccount()
    {
        return $this->hasOne('App\Models\PlannedAccount');
    }

    // ->whereRaw("date_end > DATE_SUB((SELECT date_end FROM accounts WHERE tutor_id=" . $this->id . " ORDER BY date_end DESC LIMIT 1), INTERVAL 60 DAY)");

    // ------------------------------------------------------------------------

    public function getDebtCalcAttribute()
    {
        return round(Debt::sum([
            'tutor_id' => $this->id,
            'after_last_meeting' => 1
        ]));
    }

    public function getSvgMapAttribute()
    {
        return DB::table('tutor_departures')->where('tutor_id', $this->id)->pluck('station_id');
    }

    public function setSvgMapAttribute($station_ids)
    {
        DB::table('tutor_departures')->where('tutor_id', $this->id)->delete();
        $departure_stations  = [];

        foreach (array_unique($station_ids) as $station_id) {
            $departure_stations[] = [
                'tutor_id'   => $this->id,
                'station_id' => $station_id
            ];
        }

        DB::table('tutor_departures')->insert($departure_stations);
    }

    /**
     * Последняя задолженность
     */
     public function getLastAccountInfoAttribute()
     {
         return DB::table('accounts')->select('debt', 'debt_type', 'debt_calc', 'date_end')->where('tutor_id', $this->id)->orderBy('date_end', 'desc')->first();
     }

     /**
      * Оповещения в системе безопасности
      */
    public function getSecurityNotificationAttribute($value)
    {
        return empty($value) ? array_fill(0, 3, false) : json_decode($value);
    }
    public function setSecurityNotificationAttribute($value)
    {
        $this->attributes['security_notification'] = json_encode($value);
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

        return $query
            ->where('date_end', '>=', $this->getDateLimit())
            ->orderBy('date_end', 'asc')
            ->get()->append('mutual_debts');
    }

    public function getBannedAttribute()
    {
        // @check: does this run 2 separate queries?
        return $this->user ? $this->user->banned : false;
    }

    public function getStatisticsAttribute()
    {
        $stats = Api\Api::exec('teacherStatistics', ['tutor_id' => $this->id]);
//        $stats->er_review_count = Attachment::where('tutor_id', $this->id)->has('review')->count();
        $stats->er_review_count = DB::table('reviews')->join('attachments', 'attachments.id', '=', 'attachment_id')->where('tutor_id', $this->id)->whereBetween('score', [1, 10])->count();
        $review_score_sum = DB::table('reviews')->join('attachments', 'attachments.id', '=', 'attachment_id')->where('tutor_id', $this->id)->whereBetween('score', [1, 10])->select('reviews.score')->sum('reviews.score');

        switch($this->js) {
            case 6:
            case 10: {
                $js = 8;
                break;
            }
            case 8: {
                $js = 10;
                break;
            }
            case 7: {
                $js = 9;
                break;
            }
            default: {
                $js = $this->js;
            }
        }

        $stats->er_review_avg = (4* (($this->lk + $this->tb + $js) / 3) + $review_score_sum)/(4 + $stats->er_review_count);
        return $stats;
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
        return $this->clientsCount();
    }

    public function getActiveClientsCountAttribute()
    {
        return $this->attachments()->newOrActive()->count();
    }

    /**
     * Количество встреч
     */
    public function getMeetingCountAttribute()
    {
        return $this->accounts()->count();
    }

    // ------------------------------------------------------------------------

    /**
      * Получить ID всех клиентов преподавателя
      */
     public function getClientIds($hide = null)
     {
         return $this->attachments($hide)->pluck('client_id');
     }

    /**
     * Получить ID всех клиентов преподавателя для создания списка отчетности
     * $hide – получать только не скрытых клиентов по умолчанию
     */
    public function getAttachmenClients($hide = 0, $with_lessons_count = false, $get_possible_archives = false)
    {
        $clients = [];

        foreach ($this->attachments($hide, $get_possible_archives)->get() as $attachment) {
            if ($attachment->requestList && $attachment->requestList->request) {
                $client = [
                    'id'                    => $attachment->requestList->request->client_id,
                    'address'               => $attachment->requestList->request->client->address,
                    'phones'                => $attachment->requestList->request->client->phones,
                    # @todo: заменить на link_url
                    'link'                  => "requests/{$attachment->requestList->request->id}/edit#{$attachment->requestList->id}#{$attachment->id}",
                    'attachment_date'       => $attachment->getOriginal('date'),
                    'archive_date'          => $attachment->archive ? $attachment->archive->getOriginal('date') : null,
                    'attachment_created_at' => $attachment->getOriginal('created_at'),
                    'attachment_id'         => $attachment->id,
                    'name'                  => $attachment->requestList->request->client->name,
                    'grade'                 => $attachment->requestList->request->client->grade,
                    'total_lessons_missing' => $attachment->archive ? $attachment->archive->total_lessons_missing : null,
                    'archive_state'         => $attachment->archive ? $attachment->archive->state : null,
                    'total_lessons'         => DB::table('account_datas')->where('tutor_id', $attachment->tutor_id)->where('client_id', $attachment->client_id)->count(),
                    'forecast'              => $attachment->forecast,
                    'state'                 => $attachment->getState(),
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
     * Получить кол-во  клиентов
     */
    public function clientsCount($hide = null, $count_possible_archives = false)
    {
        return $this->attachments($hide, $count_possible_archives)->count();
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
        parent::boot();

        static::saving(function($tutor) {
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

        static::saved(function($model) {
            DB::table('tutors')->where('id', $model->id)->update(['errors' => \App\Models\Helpers\Tutor::errors($model)]);
        });
    }

    public function scopeSearchByLastNameAndPhone($query, $searchText)
    {
        if ($searchText) {
            $query->whereRaw("lower(last_name) like lower('%{$searchText}%')");

            foreach (Tutor::$phone_fields as $field) {
                $query->orWhere($field, "like", "%{$searchText}%");
            }

            return $query;
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
     * Search by debtor
     */
    public function scopeSearchByDebtor($query, $debtor)
    {
        if (isset($debtor)) {
            return $query->where('debtor', $debtor);
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

    private static function addPublishedCondition($query, $published_state)
    {

        if ($published_state !== '' && $published_state !== null && ($published_state == 0 || $published_state == 1)) {
            if ($published_state) {
                $query->where('public_desc', '!=', '');
            } else {
                $query->whereRaw("(public_desc IS NULL OR public_desc = '')");
            }
        }
        return $query;
    }

    public function scopeSearchByPublishedState($query, $published_state)
    {
        return self::addPublishedCondition($query, $published_state);
    }

    public function isPublished()
    {
        return $this->public_desc != '';
    }


    private static function addErrorsCondition($query, $errors_state)
    {
        if ($errors_state !== '' && $errors_state !== null && in_array($errors_state, range(1, 4))) {
            $query->whereRaw("find_in_set({$errors_state}, errors)");
        }
        return $query;
    }

    private static function addSourceCondition($query, $egecentr_source)
    {
        if ($egecentr_source !== '' && $egecentr_source !== null) {
            $query->where('egecentr_source', $egecentr_source);
        }
        return $query;
    }

    public function scopeSearchByErrorsState($query, $errors_state)
    {
        return self::addErrorsCondition($query, $errors_state);
    }

    public function scopeSearchBySource($query, $source)
    {
        return self::addSourceCondition($query, $source);
    }

    /**
     * State counts
     * @return array [state_id] => state_count
     */
    public static function stateCounts($user_id, $published_state, $errors_code, $egecentr_source)
    {
        $return = [];
        foreach (range(0, 5) as $i) {
            $query = static::where('state', $i);
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }
            static::addPublishedCondition($query, $published_state);
            static::addErrorsCondition($query, $errors_code);
            static::addSourceCondition($query, $egecentr_source);
            $return[$i] = $query->count();
        }
        return $return;
    }

    /**
     * State counts
     * @return array [user_id] => state_count
     */
    public static function userCounts($state, $published_state, $errors_code, $egecentr_source)
    {
        $user_ids = static::where('responsible_user_id', '>', 0)->groupBy('responsible_user_id')->pluck('responsible_user_id');
        $return = [];
        foreach ($user_ids as $user_id) {
            $query = static::where('responsible_user_id', $user_id);
            if (! empty($state) || strlen($state) > 0) {
                $query->where('state', $state);
            }
            self::addPublishedCondition($query, $published_state);
            self::addErrorsCondition($query, $errors_code);
            static::addSourceCondition($query, $egecentr_source);
            $return[$user_id] = $query->count();
        }
        return $return;
    }

    /**
     * Published state counts
     * @return array [state_id] => state_count
     */
    public static function publishedCounts($state, $user_id, $errors_code, $egecentr_source)
    {
        $return = [];
        foreach (range(0, 1) as $i) {
            $query = self::addPublishedCondition(self::query(), $i);
            if (! empty($state) || strlen($state) > 0) {
                $query->where('state', $state);
            }
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }
            self::addErrorsCondition($query, $errors_code);
            static::addSourceCondition($query, $egecentr_source);
            $return[$i] = $query->count();
        }
        return $return;
    }

    /* подсчет преподов с ошибками в анкете */
    public static function errorsCounts($state, $user_id, $published_state, $egecentr_source)
    {
        $return = [];
        foreach (range(1, 4) as $error_code) {
            $query = self::addErrorsCondition(self::query(), $error_code);
            static::addSourceCondition($query, $egecentr_source);
            self::addPublishedCondition($query, $published_state);
            if (! empty($state) || strlen($state) > 0) {
                $query->where('state', $state);
            }
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }

            $return[$error_code] = $query->count();
        }
        return $return;
    }

    /* подсчет преподов с ошибками в анкете */
    public static function sourceCounts($state, $user_id, $published_state, $errors_code)
    {
        $return = [];
        foreach ([0, 1] as $source) {
            $query = self::addSourceCondition(self::query(), $source);
            static::addErrorsCondition($query, $errors_code);
            self::addPublishedCondition($query, $published_state);
            if (! empty($state) || strlen($state) > 0) {
                $query->where('state', $state);
            }
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }
            $return[$source] = $query->count();
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
      * Лимит даты для отображения начальной отчетности
      * (дата последней встречи - 60 дней)
      */
     public function getDateLimit()
     {
         return Account::select(DB::raw("DATE_SUB(date_end, INTERVAL 60 DAY) as date_limit"))
                            ->where('tutor_id', $this->id)->orderBy('date_end', 'desc')->pluck('date_limit')->first();
     }

     /**
      * Общий дебет на сегодня
      */
     public static function totalDebt()
     {
         return DB::table('tutors')->where('debtor', 0)->sum('debt_calc');
     }


    /**
     * Возвращаем
     */
    public static function plusPeriod($id, $date_limit)
    {
        $query = Account::where('date_end', '<', $date_limit)
                        ->where('tutor_id', $id);

        $account = $query->orderBy('date_end', 'desc')->first();

        $accounts_in_week = [];
        if ($query->exists()) {
            // периоды, которые входят в зону недельной видимости последнего отчета
            $accounts_in_week = Account::where('tutor_id', $id)
                                ->where('date_end', '<', $account->date_end)
                                ->where('date_end', '>', date('Y-m-d', strtotime('-7 day', strtotime($account->date_end))))->get();
        }
        // if ($account) {
        //     $account->append('mutual_debts');
        // }

        return [
            'left'             => $query->count(),
            'account'          => $account,
            'accounts_in_week' => $accounts_in_week,
        ];
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
