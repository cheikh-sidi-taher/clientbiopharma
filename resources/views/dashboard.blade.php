@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div style="margin-bottom:24px;">
    <h2 style="font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">
        Bonjour, {{ explode(' ', auth()->user()->name)[0] }}
    </h2>
    <p style="color:var(--text-secondary); font-size:14px;">
        {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }} —
        @if(auth()->user()->hasRole('Admin')) Vous avez un accès total au système.
        @elseif(auth()->user()->hasRole('Superviseur')) Suivez les performances de votre équipe.
        @elseif(auth()->user()->hasRole('Agent terrain')) Consultez votre planning du jour.
        @else Consultez votre pipeline commercial.
        @endif
    </p>
</div>

{{-- KPI Cards --}}
<div class="grid grid-4" style="margin-bottom:24px;">

    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f0fa; color:var(--primary);">
            <i class="bi bi-hospital-fill"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_pharmacies'] }}</div>
            <div class="stat-label">Pharmacies totales</div>
            <div class="stat-trend neutral"><i class="bi bi-dash"></i> En construction</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#e6f9f4; color:#00895e;">
            <i class="bi bi-clipboard2-pulse-fill"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['visited_pharmacies'] }}</div>
            <div class="stat-label">Pharmacies visitées</div>
            <div class="stat-trend neutral"><i class="bi bi-dash"></i> En construction</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fff8e6; color:#d97706;">
            <i class="bi bi-star-fill"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['interested'] }}</div>
            <div class="stat-label">Pharmacies intéressées</div>
            <div class="stat-trend neutral"><i class="bi bi-dash"></i> En construction</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fce8e8; color:#dc2626;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stats['new_clients'] }}</div>
            <div class="stat-label">Nouveaux clients</div>
            <div class="stat-trend neutral"><i class="bi bi-dash"></i> En construction</div>
        </div>
    </div>

</div>

{{-- Second Row --}}
<div class="grid grid-2" style="margin-bottom:24px;">

    {{-- Taux de conversion --}}
    <div class="card">
        <div class="card-header" style="padding:20px 24px 16px;">
            <span class="card-title">Taux de Conversion</span>
            <span style="font-size:12px; color:var(--text-muted);">Sprint 2+</span>
        </div>
        <div class="card-body">
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="position:relative; width:80px; height:80px;">
                    <svg viewBox="0 0 80 80" style="transform:rotate(-90deg);">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                        <circle cx="40" cy="40" r="32" fill="none" stroke="var(--accent)" stroke-width="8"
                                stroke-dasharray="{{ ($stats['conversion_rate'] / 100) * 201 }} 201"
                                stroke-linecap="round"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:var(--text-primary);">
                        {{ $stats['conversion_rate'] }}%
                    </div>
                </div>
                <div>
                    <div style="font-size:24px; font-weight:800;">{{ $stats['conversion_rate'] }}%</div>
                    <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Pharmacies → Clients</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                        Les données seront disponibles dès le Sprint 2
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Zones couvertes --}}
    <div class="card">
        <div class="card-header" style="padding:20px 24px 16px;">
            <span class="card-title">Couverture par Zones</span>
            <span style="font-size:12px; color:var(--text-muted);">Sprint 2+</span>
        </div>
        <div class="card-body">
            @foreach($zones as $zone)
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                <div style="width:8px; height:8px; border-radius:50%; background:var(--primary); flex-shrink:0;"></div>
                <span style="font-size:13px; color:var(--text-primary); flex:1;">{{ $zone->name }}</span>
                <div style="width:100px; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden;">
                    <div style="width:{{ $zone->coverageRate() }}%; height:100%; background:var(--primary); border-radius:3px;"></div>
                </div>
                <span style="font-size:12px; color:var(--text-muted); width:36px; text-align:right;">{{ $zone->pharmacies_count }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Modules rapides --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-header" style="padding:20px 24px 0;">
        <span class="card-title">Accès rapides</span>
    </div>
    <div class="card-body">
        <div class="grid grid-4">
            @canany(['view zones', 'manage zones'])
            <a href="{{ route('zones.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px;border-radius:12px;background:var(--bg-main);text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--bg-main)'">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--primary),#2980d9);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                    <i class="bi bi-map-fill"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">Zones</span>
            </a>
            @endcanany
            @canany(['view pharmacies', 'manage pharmacies'])
            <a href="{{ route('pharmacies.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px;border-radius:12px;background:var(--bg-main);text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--bg-main)'">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#00b894,#00cec9);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">Pharmacies</span>
            </a>
            @endcanany
            @canany(['view visits', 'manage planning'])
            <a href="{{ route('visits.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px;border-radius:12px;background:var(--bg-main);text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--bg-main)'">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#d97706,#f59e0b);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">Visites</span>
            </a>
            @endcanany
            @canany(['view reports', 'export reports'])
            <a href="{{ route('reports.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px;border-radius:12px;background:var(--bg-main);text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--bg-main)'">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">Rapports</span>
            </a>
            @endcanany
        </div>
    </div>
</div>

{{-- Info des comptes de test --}}
@if(auth()->user()->hasRole('Admin'))
<div class="alert alert-info">
    <i class="bi bi-info-circle-fill" style="font-size:18px;"></i>
    <div>
        <strong>Sprint 1 opérationnel !</strong> Auth | Rôles | Dashboard.
        Comptes de test : <strong>admin@biopharma.mr</strong>, superviseur@biopharma.mr, agent@biopharma.mr, commercial@biopharma.mr — Mot de passe : <strong>password</strong>
    </div>
</div>
@endif

@endsection
