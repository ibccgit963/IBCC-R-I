<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'subject_label', 'old_values', 'new_values', 'notes', 'center_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public static function record(string $action, Model $subject, ?string $notes = null, array $oldValues = [], array $newValues = []): void
    {
        $user = auth()->user();
        static::create([
            'user_id'       => $user?->id,
            'action'        => $action,
            'subject_type'  => class_basename($subject),
            'subject_id'    => $subject->id,
            'subject_label' => $subject->tracking_id ?? $subject->case_number ?? (string)$subject->id,
            'old_values'    => $oldValues ?: null,
            'new_values'    => $newValues ?: null,
            'notes'         => $notes,
            'center_id'     => $user?->center_id,
        ]);
    }
}
