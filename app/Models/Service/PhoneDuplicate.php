<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class PhoneDuplicate extends Model
{
    protected $fillable = ['phone', 'entity_type'];
    public $timestamps = false;
    public $loggable = false;

    public static function exists($phone, $entity_type)
    {
        return static::duplicates($phone, $entity_type)->exists();
    }

    public static function remove($phone, $entity_type)
    {
        static::duplicates($phone, $entity_type)->delete();
    }

    public static function add($phone, $entity_type)
    {
        if (! static::exists($phone, $entity_type)) {
            static::create(compact('phone', 'entity_type'));
        }
    }

    public function scopeDuplicates($query, $phone, $entity_type)
    {
        return $query->where('phone', $phone)->where('entity_type', $entity_type);
    }

    /**
     * Кол-во номеров телефона в БД
     */
    public static function countByPhone($query, $phone)
    {
        $count = 0;
        foreach(\App\Traits\Person::$phone_fields as $phone_field) {
            $count += $query->where($phone_field, $phone)->count();
        }
        return $count;
    }
}
