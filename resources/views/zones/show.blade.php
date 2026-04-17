@extends('layouts.app')

@section('title', $zone->name)
@section('page_title', 'Zones')

@section('content')

<div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
    <a href="{{ route('zones.index') }}" style="color:var(--primary);text-decoration:none;">Zones</a>
    <i class="bi bi-chevron-right" style="font-size:11px;"></i>
    <span>{{ $zone->name }}</span>
</div>

<div class="grid grid-3" style="margin-bottom:24px;">
    {{-- Zone Info --}}
    <div class="card" style="grid-column:span 2;">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                <div>
                    <h2 style="font-size:22px;font-weight:800;color:var(--text-primary);">{{ $zone->name }}</h2>
                    @if($zone->description)
                        <p style="font-size:14px;color:var(--text-secondary);margin-top:4px;">{{ $zone->description }}</p>
                    @endif
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('zones.edit', $zone) }}" class="btn btn-primary" style="padding:8px 14px;">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>
            <div class="grid grid-4">
                <div style="text-align:center;padding:16px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:28px;font-weight:800;color:var(--primary);">{{ $zone->pharmacies->count() }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">Pharmacies</div>
                </div>
                <div style="text-align:center;padding:16px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:28px;font-weight:800;color:var(--accent);">{{ $zone->coverageRate() }}%</div>
                    <div style="font-size:12px;color:var(--text-muted);">Couverture</div>
                </div>
                <div style="text-align:center;padding:16px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:28px;font-weight:800;color:#f59e0b;">{{ $zone->pharmacies->where('interest_status','intéressé')->count() }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">Intéressées</div>
                </div>
                <div style="text-align:center;padding:16px;background:var(--bg-main);border-radius:10px;">
                    <div style="font-size:28px;font-weight:800;color:#10b981;">{{ $zone->pharmacies->where('interest_status','client')->count() }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">Clients</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Agent --}}
    <div class="card">
        <div class="card-body">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;color:var(--text-secondary);">AGENT ASSIGNÉ</h3>
            @if($zone->agent)
                <div style="text-align:center;padding:16px;">
                    <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#fff;margin:0 auto 12px;">
                        {{ strtoupper(substr($zone->agent->name, 0, 1)) }}
                    </div>
                    <div style="font-size:15px;font-weight:700;color:var(--text-primary);">{{ $zone->agent->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">{{ $zone->agent->email }}</div>
                </div>
            @else
                <div style="text-align:center;padding:20px;color:var(--text-muted);">
                    <i class="bi bi-person-x" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    <p style="font-size:13px;">Aucun agent assigné</p>
                    <a href="{{ route('zones.edit', $zone) }}" style="font-size:12px;color:var(--primary);">Assigner un agent →</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Pharmacies de la zone --}}
<div class="card">
    <div class="card-header" style="padding:18px 24px 0;display:flex;align-items:center;justify-content:space-between;">
        <span class="card-title">Pharmacies dans cette zone</span>
        <a href="{{ route('pharmacies.create') }}" class="btn btn-primary" style="padding:7px 14px;font-size:13px;">
            <i class="bi bi-plus"></i> Ajouter une pharmacie
        </a>
    </div>
    <div class="card-body">
        @forelse($zone->pharmacies as $pharmacy)
        <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--border);">
            <div style="width:40px;height:40px;background:var(--primary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:18px;flex-shrink:0;">
                <i class="bi bi-hospital-fill"></i>
            </div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:var(--text-primary);">{{ $pharmacy->name }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $pharmacy->owner_name }} — {{ $pharmacy->phone }}</div>
            </div>
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $pharmacy->interest_color }}22;color:{{ $pharmacy->interest_color }};">
                {{ $pharmacy->interest_label }}
            </span>
            <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color:var(--primary);font-size:18px;">
                <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
        @empty
        <div style="text-align:center;padding:30px;color:var(--text-muted);">
            <p>Aucune pharmacie dans cette zone.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
