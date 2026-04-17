@extends('layouts.app')

@section('title', 'Clients')
@section('page_title', 'Clients')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);">Clients</h2>
        <p style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
            {{ $clients->total() }} client(s) converti(s)
        </p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('clients.index') }}" id="filter-form"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

            <div style="flex:2;min-width:200px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Recherche</label>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Pharmacie, zone, commercial..."
                           style="width:100%;padding:9px 12px 9px 36px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;"
                           onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Zone</label>
                <select name="zone_id" style="width:100%;padding:9px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;outline:none;cursor:pointer;"
                        onchange="this.form.submit()">
                    <option value="">Toutes les zones</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="padding:9px 16px;">
                    <i class="bi bi-search"></i>
                </button>
                @if(request()->hasAny(['search','zone_id']))
                    <a href="{{ route('clients.index') }}" class="btn btn-outline" style="padding:9px 16px;">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:var(--bg-main);">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">#</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Pharmacie</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Zone</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Commercial</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Limite crédit</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Conditions</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;font-size:13px;color:var(--text-muted);">{{ $client->id }}</td>
                        <td style="padding:14px 16px;">
                            <div style="font-size:14px;font-weight:700;color:var(--text-primary);">{{ $client->pharmacy->name ?? '—' }}</div>
                            @if($client->pharmacy->owner_name)
                                <div style="font-size:12px;color:var(--text-muted);">{{ $client->pharmacy->owner_name }}</div>
                            @endif
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="padding:3px 10px;background:var(--primary-light);color:var(--primary);border-radius:20px;font-weight:700;font-size:11px;">
                                {{ $client->pharmacy->zone->name ?? '—' }}
                            </span>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                {{ $client->commercial->name ?? '—' }}
                            </div>
                            <div style="font-size:12px;color:var(--text-muted);">
                                {{ $client->commercial->email ?? '' }}
                            </div>
                        </td>
                        <td style="padding:14px 16px;font-size:13px;color:var(--text-primary);font-weight:800;">
                            {{ number_format((float) $client->credit_limit, 2, ',', ' ') }}
                        </td>
                        <td style="padding:14px 16px;font-size:13px;color:var(--text-secondary);max-width:260px;">
                            {{ $client->payment_terms ?? '—' }}
                        </td>
                        <td style="padding:14px 16px;">
                            <a href="{{ route('clients.show', $client) }}" title="Voir" style="color:var(--primary);font-size:18px;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:48px;text-align:center;color:var(--text-muted);">
                            <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            <p style="font-size:15px;font-weight:600;">Aucun client converti</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--border);">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection

