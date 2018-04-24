<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCredentials;

class Background extends Model
{
    use HasCredentials;

    const UPLOAD_DIR = '/img/wallpaper/';

    protected $fillable = [
        'status',
        'user_id',
        'image',
        'date',
        'title'
    ];

    protected $appends = [
        'image_url',
        'credentials'
    ];

    protected $attributes = [
        'status' => 0
    ];

    public function getImageUrlAttribute()
    {
        return substr(static::UPLOAD_DIR, 1) . $this->image;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }
}
