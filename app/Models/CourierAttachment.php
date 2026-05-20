<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierAttachment extends Model
{
    protected $fillable = [
        'attachable_type', 'attachable_id',
        'original_name', 'stored_name', 'mime_type', 'size', 'uploaded_by',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
