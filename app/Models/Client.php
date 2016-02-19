<?php

namespace App\Models;

use Log;
use App\Traits\Markerable;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use Markerable;

    protected $with = ['requests', 'markers'];
    // protected $appends = ['lists', 'attachments'];
    protected $fillable = ['name', 'phone', 'phone2', 'phone3',
                            'grade', 'address', 'requests',
                            'markers'
                        ];

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function setRequestsAttribute($value)
    {
        foreach ($value as $request) {
            Request::updateOrCreate(['id' => $request['id']], $request);
            // Request::where('id', $request['id'])->update($request);
        }
    }

    // public function getSubjectListAttribute($value)
    // {
    //     return explode(',', $value);
    // }
    //
    // public function setSubjectListAttribute($value)
    // {
    //     $this->attributes['subject_list'] = implode(',', $value);
    // }

    // public function getListsAttribute()
    // {
    //     foreach ($this->subject_list as $subject_id) {
    //          $lists[$subject_id] = ClientSubjectList::where('client_id', $this->id)
    //                                                         ->where('subject_id', $subject_id)
    //                                                         ->pluck('tutor_id');
    //     }
    //     return $lists;
    // }
    //
    // public function setListsAttribute($value)
    // {
    //     ClientSubjectList::where('client_id', $this->id)->delete();
    //     foreach ($value as $subject_id => $tutor_ids) {
    //         foreach ($tutor_ids as $tutor_id) {
    //             ClientSubjectList::create([
    //                 'client_id'     => $this->id,
    //                 'tutor_id'      => $tutor_id,
    //                 'subject_id'    => $subject_id,
    //             ]);
    //         }
    //     }
    // }

    // public function getAttachmentsAttribute()
    // {
    //     $attachments = [];
    //
    //     foreach ($this->lists as $subject_id => $tutor_ids) {
    //         $tutor_attachments = Attachment::where('client_id', $this->id)
    //                                             ->where('subject_id', $subject_id)
    //                                             ->whereIn('tutor_id', $tutor_ids)
    //                                             ->get();
    //         if (count($tutor_attachments)) {
    //             $attachments[$subject_id] = $tutor_attachments;
    //         }
    //     }
    //
    //     return $attachments;
    // }
    //
    // public function setAttachmentsAttribute($value)
    // {
    //     Attachment::where('client_id', $this->id)->delete();
    //
    //     foreach ($value as $subject_id => $attachments) {
    //         foreach ($attachments as $attachment) {
    //             Log::info($attachment);
    //             Attachment::create($attachment);
    //         }
    //     }
    // }
}
