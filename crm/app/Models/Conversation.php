<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'message_channel_id',
        'external_thread_id',
        'subject',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function channel()
    {
        return $this->belongsTo(MessageChannel::class, 'message_channel_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function unreadMessages()
    {
        return $this->messages()->where('direction', 'incoming')->where('status', '!=', 'read');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function internalNotes()
    {
        return $this->hasMany(InternalNote::class)->orderBy('created_at', 'asc');
    }
}
