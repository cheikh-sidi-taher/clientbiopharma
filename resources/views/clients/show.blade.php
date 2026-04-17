@extends('layouts.app')

@section('title', 'Client')
@section('page_title', 'Clients')

@section('content')
<div style="max-width:900px;">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('clients.index') }}" style="color:var(--primary);text-decoration:none;">Clients</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>{{ $client->pharmacy->name ?? 'Client' }}</span>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:56px;height:56px;background:var(--primary-light);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:28px;color:var(--primary);">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <h2 style="font-size:20px;font-weight:900;color:var(--text-primary);margin-bottom:4px;">
                            {{ $client->pharmacy->name ?? '—' }}
                        </h2>
                        <p style="font-size:13px;color:var(--text-secondary);">
                            Zone : {{ $client->pharmacy->zone->name ?? '—' }}
                        </p>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ route('pharmacies.show', $client->pharmacy) }}" class="btn btn-outline">
                        <i class="bi bi-hospital-fill"></i> Voir la pharmacie
                    </a>
                </div>
            </div>

            <div class="grid grid-4" style="margin-bottom:16px;">
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:16px;font-weight:800;color:var(--text-primary);">
                        {{ $client->commercial->name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Commercial assigné</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:16px;font-weight:900;color:var(--primary);">
                        {{ number_format((float) $client->credit_limit, 2, ',', ' ') }}
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Limite crédit</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;grid-column:span 2;">
                    <div style="font-size:13px;font-weight:800;color:var(--text-primary);">
                        Conditions de paiement
                    </div>
                    <div style="font-size:13px;color:var(--text-secondary);margin-top:6px;word-break:break-word;">
                        {{ $client->payment_terms ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

