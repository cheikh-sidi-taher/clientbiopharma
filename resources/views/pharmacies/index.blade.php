@extends('layouts.app')

@section('title', 'Pharmacies')
@section('page_title', 'Pharmacies')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text-primary);">Pharmacies</h2>
        <p style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
            {{ $pharmacies->total() }} pharmacie(s) enregistrée(s)
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('pharmacies.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
        </a>
        <a href="{{ route('pharmacies.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
        <a href="{{ route('pharmacies.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>
        <a href="{{ route('pharmacies.create') }}" class="btn btn-primary" id="btn-add-pharmacy">
            <i class="bi bi-plus-circle-fill"></i> Nouvelle Pharmacie
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

{{-- Filtres --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('pharmacies.index') }}" id="filter-form"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

            <div style="flex:2;min-width:200px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Recherche</label>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nom, responsable, téléphone..."
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

            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Statut d'intérêt</label>
                <select name="interest_status" style="width:100%;padding:9px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;outline:none;cursor:pointer;"
                        onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Models\Pharmacy::$interestLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('interest_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="padding:9px 16px;">
                    <i class="bi bi-search"></i>
                </button>
                @if(request()->hasAny(['search','zone_id','interest_status']))
                    <a href="{{ route('pharmacies.index') }}" class="btn btn-outline" style="padding:9px 16px;">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tableau --}}
<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:var(--bg-main);">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">#</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Pharmacie</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Zone</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Contact</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Type</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Statut</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pharmacies as $pharmacy)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-muted);">{{ $pharmacy->id }}</td>
                    <td style="padding:14px 16px;">
                        <div style="font-size:14px;font-weight:600;color:var(--text-primary);">{{ $pharmacy->name }}</div>
                        @if($pharmacy->owner_name)
                            <div style="font-size:12px;color:var(--text-muted);">{{ $pharmacy->owner_name }}</div>
                        @endif
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="font-size:12px;padding:3px 10px;background:var(--primary-light);color:var(--primary);border-radius:20px;font-weight:600;">
                            {{ $pharmacy->zone->name ?? '—' }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-secondary);">
                        {{ $pharmacy->phone ?? '—' }}
                    </td>
                    <td style="padding:14px 16px;font-size:12px;color:var(--text-secondary);text-transform:capitalize;">
                        {{ $pharmacy->type }}
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;
                            background:{{ $pharmacy->interest_color }}22;color:{{ $pharmacy->interest_color }}">
                            {{ $pharmacy->interest_label }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('pharmacies.show', $pharmacy) }}" title="Voir" style="color:var(--primary);font-size:18px;"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('pharmacies.edit', $pharmacy) }}" title="Modifier" style="color:#d97706;font-size:18px;"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('pharmacies.destroy', $pharmacy) }}" onsubmit="return confirm('Supprimer cette pharmacie ?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Supprimer" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:18px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;color:var(--text-muted);">
                        <i class="bi bi-hospital" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        <p style="font-size:15px;font-weight:600;">Aucune pharmacie trouvée</p>
                        <a href="{{ route('pharmacies.create') }}" class="btn btn-primary" style="margin-top:16px;">
                            <i class="bi bi-plus"></i> Ajouter une pharmacie
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pharmacies->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border);">
        {{ $pharmacies->links() }}
    </div>
    @endif
</div>

@endsection
