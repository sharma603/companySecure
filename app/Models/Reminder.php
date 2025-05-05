<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'reminder_date',
        'is_completed',
        'company_id',
        'user_id',
        'reminder_email',
        'email_sent'
    ];

    protected $casts = [
        'reminder_date' => 'datetime',
        'is_completed' => 'boolean',
        'email_sent' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 