<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Metro;
use App\Models\Request;
use App\Models\Account;
use App\Events\ResponsibleUserChanged;
use App\Events\RecalcTutorDebt;
use App\Events\RecalcTutorData;

class Tutor extends Service\Person
{
    const ENTITY_TYPE = 'tutor';

    public $timestamps = false;

    protected $casts = [
        // 'source' => 'string',
        'auto_publish_disabled' => 'boolean'
    ];

    protected $fillable =  [
        'file',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'birthday',
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
        'subjects_ec',
        'grades',
        'departure_price',
        'list_comment',
        'responsible_user_id',
        'lesson_duration',
        'ready_to_work',
        'comment',
        'description',
        'branches',
        'in_egecentr',
        'video_link',
        'debt_comment',
        'debtor',
        'errors',
        'photo_extension',
        'source',
        'photo_desc',
        'auto_publish_disabled',
        'passport_series',
        'passport_number',
        'passport_code',
        'passport_address',
        'passport_issue_place',
        'so',
    ];

    protected $appends = [
        'has_photo_original',
        'has_photo_cropped',
        'photo_cropped_size',
        'photo_original_size',
        'photo_url',
        'age',
        'clients_count',
        'review_avg'
    ];

    // protected $with = ['markers'];

    protected static $commaSeparated = ['subjects', 'subjects_ec', 'grades', 'branches', 'errors'];
    protected static $dotDates = ['birthday'];

    const FILE_UPLOAD_DIR = '/tutor-files/';
    const UPLOAD_DIR = '/img/tutors/';
    const NO_PHOTO   = 'no-profile-img.gif';
    const USER_TYPE  = 'TEACHER';

    const STATES = ['не установлено', 'на проверку', 'к закрытию', 'закрыто', 'к одобрению', 'одобрено', 'собеседование'];

    // ------------------------------------------------------------------------

    public function accounts()
    {
        // последние 60 дней с момента начальной стыковки
        return $this->hasMany('App\Models\Account');
    }

    public function attachments($hide = null, $get_possible_archives = false)
    {
        $query = $this->hasMany('App\Models\Attachment');
        if(!$get_possible_archives) {
            if ($hide !== null) {
                $query->where('hide', $hide);
            }
        } else {
            $query->whereRaw("(attachments.hide = 0)");
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

    public function getMarginAttribute($margin)
    {
        return 'M' . ($margin ?: 'E');
    }

    public function getReviewAvgAttribute()
    {
        return DB::table('tutor_data')->where('tutor_id', $this->id)->value('review_avg');
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
         return DB::table('accounts')->select('debt', 'debt_type', 'date_end')->where('tutor_id', $this->id)->orderBy('date_end', 'desc')->first();
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
            ->get();
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
        return date('Y') - date('Y', strtotime($this->birthday));
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
    public function getAttachmenClients($hide = 0, $get_possible_archives = false)
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
                    // attachment-refactored
                    'total_lessons'         => DB::table('account_datas')->where('attachment_id', $attachment->id)->count(),
                    'forecast'              => $attachment->forecast,
                    'state'                 => $attachment->getState(),
                ];
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

        $mutual_metro = null;

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
            if ($tutor->changed(['email'])) {
                // отстреливаем в синюю базу
                // TODO: снести поле email
                $query = dbEgecrm2('emails')
                    ->where('entity_type', "App\\Models\\Teacher")
                    ->where('entity_id', $tutor->id);

                if (cloneQuery($query)->exists()) {
                    $query->update(['email' => $tutor->email]);
                } else {
                    $query->insert([
                        'entity_type' => "App\\Models\\Teacher",
                        'entity_id' => $tutor->id,
                        'password' => '',
                        'email' => $tutor->email,
                    ]);
                }
            }
            if ($tutor->changed(['email', 'phone', 'first_name', 'last_name', 'middle_name'])) {
                $tutor->updateUser();
            }
        });

        static::updated(function($tutor) {
            # if responsible user changed
            if ($tutor->changed('responsible_user_id')) {
                event(new ResponsibleUserChanged($tutor));
            }
            if ($tutor->changed(['debtor'])) {
                event(new RecalcTutorDebt($tutor->id));
            }
            event(new RecalcTutorData($tutor->id));
        });

        static::created(function($tutor) {
            event(new RecalcTutorData($tutor->id));
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
     * Search by markers
     */
    public function scopeSearchByMarkers($query, $state)
    {
        if (isset($state)) {
            switch(intval($state)) {
                case 1:
                    return $query->has('markers')->whereRaw("(SELECT COUNT(*) FROM markers WHERE markerable_id=tutors.id AND markerable_type='App\\\Models\\\Tutor') = (SELECT COUNT(*) FROM markers WHERE markerable_id=tutors.id AND markerable_type='App\\\Models\\\Tutor' AND comment<>'')");
                case 2:
                    return $query->has('markers')->whereRaw("(SELECT COUNT(*) FROM markers WHERE markerable_id=tutors.id AND markerable_type='App\\\Models\\\Tutor') <> (SELECT COUNT(*) FROM markers WHERE markerable_id=tutors.id AND markerable_type='App\\\Models\\\Tutor' AND comment<>'')");
                case 3:
                    return $query->doesntHave('markers');
            }
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
     * Search by duplicates
     */
    public function scopeSearchByDuplicates($query, $value)
    {
        if (isset($value) && $value) {
            switch($value) {
                case 'phone':
                    return $query
                        ->select(DB::raw('tutors.*, GROUP_CONCAT(p2.tutor_id) as duplicate_tutor_ids'))
                        ->leftJoin('phones as p1', 'p1.tutor_id', '=', 'tutors.id')
                        ->leftJoin('phones as p2', 'p2.tutor_id', '<>', 'tutors.id')
                        ->whereRaw("p1.phone = p2.phone")
                        ->groupBy('tutors.id');
                        case 'phone':
                case 'last_name':
                    return $query
                        ->select(DB::raw('tutors.*, GROUP_CONCAT(t2.id) as duplicate_tutor_ids'))
                        ->join('tutors as t2', 't2.last_name', '=', 'tutors.last_name')
                        ->whereRaw("t2.id <> tutors.id and tutors.last_name <> ''")
                        ->groupBy('tutors.id');
                case 'last_first_name':
                    return $query
                        ->select(DB::raw('tutors.*, GROUP_CONCAT(t2.id) as duplicate_tutor_ids'))
                        ->join('tutors as t2', function ($join) {
                            return $join
                                ->on('t2.last_name', '=', 'tutors.last_name')
                                ->on('t2.first_name', '=', 'tutors.first_name');
                        })
                        ->whereRaw("t2.id <> tutors.id and tutors.last_name <> ''")
                        ->groupBy('tutors.id');
                case 'fio':
                    return $query
                        ->select(DB::raw('tutors.*, GROUP_CONCAT(t2.id) as duplicate_tutor_ids'))
                        ->join('tutors as t2', function ($join) {
                            return $join
                                ->on('t2.last_name', '=', 'tutors.last_name')
                                ->on('t2.first_name', '=', 'tutors.first_name')
                                ->on('t2.middle_name', '=', 'tutors.middle_name');
                        })
                        ->whereRaw("t2.id <> tutors.id and tutors.last_name <> '' and tutors.middle_name <> ''")
                        ->groupBy('tutors.id');
                default: return $query;
            }
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

    private static function addSourceCondition($query, $source)
    {
        if ($source !== '' && $source !== null) {
            $query->where('source', $source);
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

    public function scopeSearchByInEgecentr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('in_egecentr', $value);
        }
        return $query;
    }

    public function scopeSearchBySubjectsEr($query, $value)
    {
        if ($value !== '' && $value !== null && $value !== []) {
            if (! is_array($value)) {
                $value = explode(',', $value);
            }
            $query->whereIn('subjects', $value);
        }
        return $query;
    }

    public function scopeSearchBySubjectsEc($query, $value)
    {
        if ($value !== '' && $value !== null && $value !== []) {
            if (! is_array($value)) {
                $value = explode(',', $value);
            }
            $query->whereIn('subjects_ec', $value);
        }
        return $query;
    }

    /**
     * State counts
     * @return array [state_id] => state_count
     */
    public static function stateCounts($user_id, $published_state, $errors_code, $source)
    {
        $return = [];
        foreach (range(0, 6) as $i) {
            $query = static::where('state', $i);
            if (! empty($user_id)) {
                $query->where('responsible_user_id', $user_id);
            }
            static::addPublishedCondition($query, $published_state);
            static::addErrorsCondition($query, $errors_code);
            static::addSourceCondition($query, $source);
            $return[$i] = $query->count();
        }
        return $return;
    }

    /**
     * State counts
     * @return array [user_id] => state_count
     */
    public static function userCounts($state, $published_state, $errors_code, $source)
    {
        $user_ids = static::where('responsible_user_id', '>', 0)->groupBy('responsible_user_id')->pluck('responsible_user_id');
        // count system
        $user_ids[] = 0;
        $return = [];
        foreach ($user_ids as $user_id) {
            $query = static::where('responsible_user_id', $user_id);
            if (! empty($state) || strlen($state) > 0) {
                $query->where('state', $state);
            }
            self::addPublishedCondition($query, $published_state);
            self::addErrorsCondition($query, $errors_code);
            static::addSourceCondition($query, $source);
            $return[$user_id] = $query->count();
        }
        return $return;
    }

    /**
     * Published state counts
     * @return array [state_id] => state_count
     */
    public static function publishedCounts($state, $user_id, $errors_code, $source)
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
            static::addSourceCondition($query, $source);
            $return[$i] = $query->count();
        }
        return $return;
    }

    /* подсчет преподов с ошибками в анкете */
    public static function errorsCounts($state, $user_id, $published_state, $source)
    {
        $return = [];
        foreach (range(1, 4) as $error_code) {
            $query = self::addErrorsCondition(self::query(), $error_code);
            static::addSourceCondition($query, $source);
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
        foreach (range(0, 2) as $source) {
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

      public static function inEgecentrCounts($source, $state, $user_id, $published_state, $errors_code, $subjects_er, $subjects_ec)
      {
          $return = [];
          foreach (range(0, 6) as $in_egecentr) {
            $query = self::query()
                ->searchByInEgecentr($in_egecentr)
                ->searchBySubjectsEc($subjects_ec)
                ->searchBySubjectsEr($subjects_er);

            self::addSourceCondition($query, $source);
            static::addErrorsCondition($query, $errors_code);
            self::addPublishedCondition($query, $published_state);
              if (! empty($state) || strlen($state) > 0) {
                  $query->where('state', $state);
              }
              if (! empty($user_id)) {
                  $query->where('responsible_user_id', $user_id);
              }
              $return[$in_egecentr] = $query->count();
          }
          return $return;
      }

      public static function subjectsErCounts($source, $state, $user_id, $published_state, $errors_code, $in_egecentr, $subjects_ec)
      {
          $return = [];
          foreach (range(1, 11) as $subject_id) {
            $query = self::query()
                ->searchByInEgecentr($in_egecentr)
                ->searchBySubjectsEc($subjects_ec)
                ->searchBySubjectsEr("{$subject_id}");

            self::addSourceCondition($query, $source);
            static::addErrorsCondition($query, $errors_code);
            self::addPublishedCondition($query, $published_state);
              if (! empty($state) || strlen($state) > 0) {
                  $query->where('state', $state);
              }
              if (! empty($user_id)) {
                  $query->where('responsible_user_id', $user_id);
              }
              $return[$subject_id] = $query->count();
          }
          return $return;
      }

      public static function subjectsEcCounts($source, $state, $user_id, $published_state, $errors_code, $in_egecentr, $subjects_er)
      {
          $return = [];
          foreach (range(1, 11) as $subject_id) {
            $query = self::query()
                ->searchByInEgecentr($in_egecentr)
                ->searchBySubjectsEr($subjects_er)
                ->searchBySubjectsEc("{$subject_id}");

            self::addSourceCondition($query, $source);
            static::addErrorsCondition($query, $errors_code);
            self::addPublishedCondition($query, $published_state);
              if (! empty($state) || strlen($state) > 0) {
                  $query->where('state', $state);
              }
              if (! empty($user_id)) {
                  $query->where('responsible_user_id', $user_id);
              }
              $return[$subject_id] = $query->count();
          }
          return $return;
      }

    /**
     * Updates corresponding user from users table
     */
    public function updateUser()
    {
        if ($this->in_egecentr) {
            $condition = [
                'id_entity' => $this->id,
                'type' => 'TEACHER'
            ];
            if (dbEgecrm('users')->where($condition)->exists()) {
                dbEgecrm('users')->where($condition)->update(['email' => $this->email]);
            } else {
                dbEgecrm('users')->insert(array_merge($condition, ['email' => $this->email]));
            }
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

        return [
            'left'             => $query->count(),
            'account'          => $account,
            'accounts_in_week' => $accounts_in_week,
        ];
    }
}
