@extends('layouts.app')

@section('title', $pharmacy->name)
@section('page_title', 'Pharmacies')

@section('content')

<div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
    <a href="{{ route('pharmacies.index') }}" style="color:var(--primary);text-decoration:none;">Pharmacies</a>
    <i class="bi bi-chevron-right" style="font-size:11px;"></i>
    <span>{{ $pharmacy->name }}</span>
</div>

<div class="grid grid-3" style="margin-bottom:20px;">

    {{-- Main info --}}
    <div class="card" style="grid-column:span 2;">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:56px;height:56px;background:var(--primary-light);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:28px;color:var(--primary);">
                        <i class="bi bi-hospital-fill"></i>
                    </div>
                    <div>
                        <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);">{{ $pharmacy->name }}</h2>
                        <p style="font-size:13px;color:var(--text-secondary);">{{ $pharmacy->owner_name }} — {{ $pharmacy->address }}</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('pharmacies.edit', $pharmacy) }}" class="btn btn-primary" style="padding:8px 14px;">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>

                    @if($pharmacy->client)
                        <a href="{{ route('clients.show', $pharmacy->client) }}" class="btn btn-outline" style="padding:8px 14px;">
                            <i class="bi bi-briefcase-fill"></i> Client
                        </a>
                    @else
                        <a href="{{ route('pharmacies.convert_to_client', $pharmacy) }}" class="btn btn-outline" style="padding:8px 14px;">
                            <i class="bi bi-arrow-right-circle-fill"></i> Convertir en client
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-4" style="margin-bottom:20px;">
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <span style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $pharmacy->interest_color }}22;color:{{ $pharmacy->interest_color }}">
                        {{ $pharmacy->interest_label }}
                    </span>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">Statut</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:16px;font-weight:700;color:var(--text-primary);text-transform:capitalize;">{{ $pharmacy->type }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Type</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:16px;font-weight:700;color:var(--text-primary);">{{ $pharmacy->visits->count() }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Visites</div>
                </div>
                <div style="text-align:center;padding:14px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:16px;font-weight:700;color:var(--primary);">{{ $pharmacy->zone->name ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Zone</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px;">TÉLÉPHONE</div>
                    <div style="font-size:14px;color:var(--text-primary);">{{ $pharmacy->phone ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px;">PARTENARIAT</div>
                    <div style="font-size:14px;color:var(--text-primary);text-transform:capitalize;">{{ str_replace('_',' ',$pharmacy->partnership_type) }}</div>
                </div>
                @if($pharmacy->best_selling_products)
                <div style="grid-column:1/-1;">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px;">PRODUITS LES PLUS VENDUS</div>
                    <div style="font-size:14px;color:var(--text-primary);">{{ $pharmacy->best_selling_products }}</div>
                </div>
                @endif
                @if($pharmacy->notes)
                <div style="grid-column:1/-1;">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);margin-bottom:4px;">NOTES</div>
                    <div style="font-size:14px;color:var(--text-secondary);font-style:italic;">{{ $pharmacy->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Besoins --}}
    <div class="card">
        <div class="card-body">
            <h3 style="font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:14px;">ANALYSE BESOINS</h3>
            @php
                $needs = [
                    'stock_problem'     => ['Problème stock',    '#dc2626', 'bi-box-seam'],
                    'delivery_problem'  => ['Problème livraison','#f59e0b', 'bi-truck'],
                    'training_need'     => ['Besoin formation',  '#3b82f6', 'bi-mortarboard'],
                    'distribution_need' => ['Besoin distribution','#8b5cf6','bi-grid'],
                ];
            @endphp
            @foreach($needs as $field => [$label, $color, $icon])
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="width:32px;height:32px;background:{{ $pharmacy->$field ? $color.'22' : '#f1f5f9' }};border-radius:8px;display:flex;align-items:center;justify-content:center;color:{{ $pharmacy->$field ? $color : '#94a3b8' }};font-size:15px;">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <span style="flex:1;font-size:13px;color:var(--text-primary);">{{ $label }}</span>
                @if($pharmacy->$field)
                    <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                @else
                    <i class="bi bi-dash-circle" style="color:#cbd5e1;"></i>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
