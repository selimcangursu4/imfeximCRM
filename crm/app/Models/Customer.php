<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'company',
        'address',
    ];

    public function companyRelation()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function attachments()
    {
        return $this->hasMany(CustomerAttachment::class);
    }

    public function activities()
    {
        return $this->hasMany(CustomerActivity::class);
    }
}
