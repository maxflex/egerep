<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSubjectList extends Model
{
    protected $fillable = ['client_id', 'tutor_id', 'subject_id'];
    public $timestamps = false;
}
