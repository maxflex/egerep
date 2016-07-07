<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $loggable   = false;
    public $timestamps = false;

    public static function search()
    {
        $search = isset($_COOKIE['reviews']) ? json_decode($_COOKIE['reviews']) : (object)[];
        $search = filterParams($search);
        $query = Attachment::with(['tutor'])->has('archive');

        if (isset($search->mode)) {
            if ($search->mode) {
                $query->doesntHave('review');
            } else {
                $query->has('review');
            }
        }

        if (isset($search->state) || isset($search->signature) || isset($search->comment) || isset($search->score)) {
            $query->whereHas('review', function($query) use ($search) {
                if (isset($search->state)) {
                    $query->where('state', $search->state);
                }
                if (isset($search->signature)) {
                    $query->where('signature', $search->signature ? '=' : '<>', '');
                }
                if (isset($search->comment)) {
                    $query->where('comment', $search->comment ? '=' : '<>', '');
                }
                if (isset($search->score)) {
                    $query->where('score', $search->score);
                }
            });
        }

        return $query->orderBy('date', 'desc');
    }
}
