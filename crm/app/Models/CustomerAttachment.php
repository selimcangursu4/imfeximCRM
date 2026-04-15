<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'conversation_id',
        'filename',
        'file_path',
        'mime_type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
