<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'provider',
        'settings',
        'enabled',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
