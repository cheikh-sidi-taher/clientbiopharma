<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Client extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'payment_terms',
        'credit_limit',
        'commercial_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Filtres alignés sur la liste clients (recherche, zone, statut, commercial).
     */
    public function scopeFiltered(Builder $query, Request $request): Builder
    {
        $query->with(['pharmacy.zone', 'commercial']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_terms', 'like', "%{$search}%")
                    ->orWhereHas('pharmacy', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('owner_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhereHas('zone', function ($zq) use ($search) {
                                $zq->where('name', 'like', "%{$search}%");
                            });
                    })->orWhereHas('commercial', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->get('zone_id')) {
            $query->whereHas('pharmacy.zone', fn ($q) => $q->where('id', $request->get('zone_id')));
        }

        if ($request->filled('status') && in_array($request->get('status'), ['actif', 'inactif'], true)) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('commercial_id')) {
            $query->where('commercial_id', $request->integer('commercial_id'));
        }

        return $query->orderByDesc('created_at');
    }
}
