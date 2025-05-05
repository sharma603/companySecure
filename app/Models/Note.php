<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    protected $attributes = [
        'data' => '{}' // Default empty JSON object
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'user_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }
} 