<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_id',
        'courier_company',
        'sender_name',
        'sender_cnic',
        'sender_contact',
        'city',
        'address',
        'internal_branch',
        'branch',
        'ministry_department',
        'category',
        'type',
        'center_id',
        'received_by_user_id',
        'status',
        'department_id',
        'assigned_user_id',
        'reverted_by_user_id',
        'comments',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function transfers()
    {
        return $this->hasMany(CourierTransfer::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function revertedBy()
    {
        return $this->belongsTo(User::class, 'reverted_by_user_id');
    }

    public function attachments()
    {
        return $this->morphMany(CourierAttachment::class, 'attachable');
    }
}
