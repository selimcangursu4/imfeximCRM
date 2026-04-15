<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'company_id',
        'user_id',
        'message',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
