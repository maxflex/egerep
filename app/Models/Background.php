<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCredentials;

class Background extends Model
{
    use HasCredentials;

    const UPLOAD_DIR = '/img/wallpapper/';

    protected $fillable = [
        'is_approved',
        'user_id',
        'image',
        'date'
    ];

    protected $appends = [
        'image_url',
        'credentials'
    ];

    protected $attributes = [
        'is_approved' => 0
    ];

    public function getImageUrlAttribute()
    {
        return substr(static::UPLOAD_DIR, 1) . $this->image;
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
