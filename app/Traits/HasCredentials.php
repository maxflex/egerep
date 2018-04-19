<?php

namespace App\Traits;

use App\Models\User;

/**
 * Имеет маркеры
 *
 * @usage: добавить 'markers' в $fillable и $with
 */
trait HasCredentials
{
    public function getCredentialsAttribute()
    {
        return User::whereId($this->user_id)->value('login') . ' в ' . date('d.m.y в H:i', strtotime($this->created_at));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
