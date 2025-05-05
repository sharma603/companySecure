<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact',
        'address',
        'url'
    ];

    protected $dates = ['deleted_at'];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
} 