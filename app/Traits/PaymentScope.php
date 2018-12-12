<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Скрыть некоторые значения платежей для обычных пользователей
 * Отображать только суперпользователям
 */
trait PaymentScope
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('hidden', function(Builder $builder) {
            if (! allowed(\Shared\Rights::IS_SUPERUSER)) {
                $builder->whereNotIn('id', self::HIDDEN_IDS);
            }
        });
   }
}
