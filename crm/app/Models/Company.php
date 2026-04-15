<?php

namespace App\Models;

use App\Models\CompanyApiSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function channels()
    {
        return $this->hasMany(MessageChannel::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function apiSettings()
    {
        return $this->hasMany(CompanyApiSetting::class);
    }
}
