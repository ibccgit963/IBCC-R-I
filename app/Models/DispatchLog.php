<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchLog extends Model
{
    protected $fillable = [
        'serial_number', 'name', 'address', 'subject', 'file_no', 'remarks', 'center_id', 'created_by'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
