<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public const STATUS_LEAD = 'Lead';
    public const STATUS_CONTACTED = 'İlk Temas';
    public const STATUS_QUALIFIED = 'Nitelikli Lead';
    public const STATUS_NEED_ANALYSIS = 'İhtiyaç Analizi';
    public const STATUS_PROPOSAL = 'Teklif / Demo';
    public const STATUS_NEGOTIATION = 'Pazarlık';
    public const STATUS_WAITING = 'Karar Bekleniyor';
    public const STATUS_WON = 'Kazanıldı';
    public const STATUS_LOST = 'Kaybedildi';

    public static function getStatuses()
    {
        return [
            self::STATUS_LEAD,
            self::STATUS_CONTACTED,
            self::STATUS_QUALIFIED,
            self::STATUS_NEED_ANALYSIS,
            self::STATUS_PROPOSAL,
            self::STATUS_NEGOTIATION,
            self::STATUS_WAITING,
            self::STATUS_WON,
            self::STATUS_LOST,
        ];
    }

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'assigned_user_id',
        'status',
        'deal_value',
        'source',
        'first_contact_date',
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
