<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'center_id'];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function officers()
    {
        return $this->hasMany(Officer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
