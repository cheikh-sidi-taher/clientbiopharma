<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name', 'status', 'agent_id', 'target_pharmacies', 'description',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function pharmacies(): HasMany
    {
        return $this->hasMany(Pharmacy::class);
    }

    public function coverageRate(): float
    {
        $total = $this->pharmacies()->count();
        if ($total === 0) return 0;
        $visited = $this->pharmacies()
            ->whereNotIn('interest_status', ['non_visité'])
            ->count();
        return round(($visited / $total) * 100, 1);
    }
}
