<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_name',
        'father_name',
        'applicant_contact',
        'case_number',
        'dispatch_courier_company',
        'dispatched_from',
        'tracking_id',
        'type',
        'status',
        'center_id',
        'dispatched_by_user_id',
        'requested_by_user_id',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by_user_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function attachments()
    {
        return $this->morphMany(\App\Models\CourierAttachment::class, 'attachable');
    }
}
