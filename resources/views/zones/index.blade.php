@extends('layouts.app')

@section('title', 'Zones')
@section('page_title', 'Zones')

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:20px; font-weight:700; color:var(--text-primary);">Gestion des Zones</h2>
        <p style="font-size:13px; color:var(--text-secondary); margin-top:2px;">
            {{ $zones->count() }} zone(s) configurée(s) à Nouakchott
        </p>
    </div>
    <a href="{{ route('zones.create') }}" class="btn btn-primary" id="btn-add-zone">
        <i class="bi bi-plus-circle-fill"></i> Nouvelle Zone
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    </div>
@endif

{{-- Zones Grid --}}
<div class="grid grid-3">
    @forelse($zones as $zone)
    <div class="card" style="transition:transform .2s, box-shadow .2s;"
         onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.1)'"
         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='var(--shadow)'">

        <div style="height:5px; background:{{ $zone->status === 'active' ? 'linear-gradient(90deg,var(--primary),var(--accent))' : '#e2e8f0' }};"></div>

        <div class="card-body">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
                <div>
                    <h3 style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $zone->name }}</h3>
                    @if($zone->description)
                        <p style="font-size:12px; color:var(--text-muted); margin-top:3px;">{{ $zone->description }}</p>
                    @endif
                </div>
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;
                    background:{{ $zone->status === 'active' ? '#e6f9f4' : '#f1f5f9' }};
                    color:{{ $zone->status === 'active' ? '#00895e' : '#94a3b8' }};">
                    {{ $zone->status === 'active' ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Stats --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                <div style="background:var(--bg-main); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:22px; font-weight:800; color:var(--primary);">{{ $zone->pharmacies_count }}</div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Pharmacies</div>
                </div>
                <div style="background:var(--bg-main); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:22px; font-weight:800; color:var(--accent);">{{ $zone->coverageRate() }}%</div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Couverture</div>
                </div>
            </div>

            {{-- Coverage bar --}}
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:4px;">
                    <span>Taux de couverture</span>
                    <span>{{ $zone->coverageRate() }}%</span>
                </div>
                <div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $zone->coverageRate() }}%;height:100%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:3px;transition:width .5s;"></div>
                </div>
            </div>

            {{-- Agent --}}
            @if($zone->agent)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding:8px 10px;background:var(--primary-light);border-radius:8px;">
                <div style="width:28px;height:28px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;">
                    {{ strtoupper(substr($zone->agent->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--primary);">{{ $zone->agent->name }}</div>
                    <div style="font-size:10px;color:var(--text-muted);">Agent assigné</div>
                </div>
            </div>
            @else
            <div style="margin-bottom:16px;padding:8px 10px;background:#fff8e6;border-radius:8px;font-size:12px;color:#d97706;">
                <i class="bi bi-exclamation-triangle"></i> Aucun agent assigné
            </div>
            @endif

            {{-- Actions --}}
            <div style="display:flex;gap:8px;">
                <a href="{{ route('zones.show', $zone) }}" class="btn btn-outline" style="flex:1;justify-content:center;padding:8px;">
                    <i class="bi bi-eye"></i> Voir
                </a>
                <a href="{{ route('zones.edit', $zone) }}" class="btn btn-primary" style="flex:1;justify-content:center;padding:8px;">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <form method="POST" action="{{ route('zones.destroy', $zone) }}" onsubmit="return confirm('Supprimer cette zone ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:8px 12px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:60px 20px;">
        <i class="bi bi-map" style="font-size:48px; color:var(--text-muted); display:block; margin-bottom:16px;"></i>
        <p style="font-size:16px; color:var(--text-secondary); font-weight:600;">Aucune zone configurée</p>
        <p style="font-size:13px; color:var(--text-muted); margin:8px 0 20px;">Commencez par ajouter les zones de Nouakchott.</p>
        <a href="{{ route('zones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill"></i> Ajouter la première zone
        </a>
    </div>
    @endforelse
</div>

@endsection
