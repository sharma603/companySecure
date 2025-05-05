<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
    protected $table = 'company_details';

    protected $fillable = [
        'company_name',
        'contact_number',
        'address',
        'url'
    ];

    // Add any relationships here
}