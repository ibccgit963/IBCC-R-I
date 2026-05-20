<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'transferable_type',
        'transferable_id',
        'transferred_by_user_id',
        'received_at',
        'is_for_dispatch',
        'notes',
        'is_reverted',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function transferable()
    {
        return $this->morphTo();
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by_user_id');
    }
}
