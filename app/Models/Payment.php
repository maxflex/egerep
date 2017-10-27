<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sum',
        'type',
        'purpose',
        'date',
        'addressee_id',
        'source_id',
        'expenditure_id'
    ];

    protected $attributes = [
        'type'           => 0,
        'source_id'      => '',
        'addressee_id'   => '',
        'expenditure_id' => '',
    ];

    protected static $dotDates = ['date'];

    protected static function boot()
    {
        static::saving(function($model) {
            if (! $model->source_id) {
                $model->source_id = null;
            }
            if (! $model->addressee_id) {
                $model->addressee_id = null;
            }
            if (! $model->expenditure_id) {
                $model->expenditure_id = null;
            }
        });
        static::creating(function ($model) {
            $model->user_id = User::fromSession()->id;
        });
    }

    public static function search($model = true)
    {
        $search = isset($_COOKIE['payments']) ? json_decode($_COOKIE['payments']) : (object)[];
        $search = filterParams($search);

        $query = $model ? self::query() : \DB::table('payments');
        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        if (isset($search->source_ids) && count($search->source_ids)) {
            $query->whereIn('source_id', $search->source_ids);
        }

        if (isset($search->addressee_ids) && count($search->addressee_ids)) {
            $query->whereIn('addressee_id', $search->addressee_ids);
        }

        if (isset($search->expenditure_ids) && count($search->expenditure_ids)) {
            $query->whereIn('expenditure_id', $search->expenditure_ids);
        }

        if (isset($search->purpose) && ! isBlank($search->purpose)) {
            $query->whereRaw("purpose LIKE '%" . $search->purpose . "%'");
        }

        // if (isset($search->date_start) && $search->date_start) {
        //     $query->whereRaw("date(`date`) >= '" . fromDotDate($search->date_start) . "'");
        // }
        //
        // if (isset($search->date_end) && $search->date_end) {
        //     $query->whereRaw("date(`date`) <= '" . fromDotDate($search->date_end) . "'");
        // }

        if (isset($search->type)) {
            $query->where('type', $search->type);
        }

        return $query;
    }
}
