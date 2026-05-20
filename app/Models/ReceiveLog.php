<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveLog extends Model
{
    protected $fillable = [
        'sr_no', 'date', 'name', 'designation', 'organization_name', 'subject', 'signature', 'center_id', 'created_by'
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
