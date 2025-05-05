<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    protected $fillable = [
        'name',
        'color'
    ];

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class);
    }
} 