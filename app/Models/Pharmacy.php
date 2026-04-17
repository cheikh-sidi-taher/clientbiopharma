<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pharmacy extends Model
{
    protected $fillable = [
        'zone_id', 'name', 'owner_name', 'phone', 'address', 'type',
        'best_selling_products', 'stock_problem', 'delivery_problem',
        'training_need', 'distribution_need', 'interest_status',
        'partnership_type', 'notes', 'latitude', 'longitude', 'created_by',
    ];

    protected $casts = [
        'stock_problem'     => 'boolean',
        'delivery_problem'  => 'boolean',
        'training_need'     => 'boolean',
        'distribution_need' => 'boolean',
        'latitude'          => 'float',
        'longitude'         => 'float',
    ];

    // Interest status labels
    public static array $interestLabels = [
        'non_visité'    => 'Non visité',
        'visité'        => 'Visité',
        'intéressé'     => 'Intéressé',
        'non_intéressé' => 'Non intéressé',
        'client'        => 'Client',
    ];

    public static array $interestColors = [
        'non_visité'    => '#94a3b8',
        'visité'        => '#3b82f6',
        'intéressé'     => '#f59e0b',
        'non_intéressé' => '#ef4444',
        'client'        => '#10b981',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function getInterestLabelAttribute(): string
    {
        return self::$interestLabels[$this->interest_status] ?? $this->interest_status;
    }

    public function getInterestColorAttribute(): string
    {
        return self::$interestColors[$this->interest_status] ?? '#94a3b8';
    }
}
