<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Officer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'designation', 'contact', 'department_id', 'center_id', 'user_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
