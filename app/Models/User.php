<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'egecrm';

    # ID of the last real user
    const LAST_REAL_ID = 112;


    /**
     * Get real users
     */
    public static function getReal($only_working = false)
    {
        $query = User::where('id', '<=', self::LAST_REAL_ID);

        if ($only_working) {
            $query = $query->where('worktime', 1);
        }

        return $query->get();
    }
}
