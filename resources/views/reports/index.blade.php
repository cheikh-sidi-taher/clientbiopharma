@extends('layouts.app')

@section('title', 'Rapports')
@section('page_title', 'Rapports')

@section('content')
@php
    $query = request()->query();
    $type = $type ?? ($query['type'] ?? 'journalier');
@endphp

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="font-size:20px;font-weight:900;color:var(--text-primary);margin-bottom:6px;">Rapports</h2>
        <p style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
            {{ $label }}
        </p>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        @php
            $q = $query;
        @endphp
        <a href="{{ route('reports.export', array_merge($q, ['format' => 'csv'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
        <a href="{{ route('reports.export', array_merge($q, ['format' => 'excel'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('reports.export', array_merge($q, ['format' => 'pdf'])) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('reports.index') }}" id="report-filters" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
            <div style="min-width:220px;flex:1;">
                <label style="display:block;font-size:12px;font-weight:800;color:var(--text-secondary);margin-bottom:6px;">
                    Période
                </label>
                <select name="type"
                        style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;background:#fff;outline:none;cursor:pointer;"
                        onchange="document.getElementById('report-filters').submit()">
                    <option value="journalier" {{ $type === 'journalier' ? 'selected' : '' }}>Journalier</option>
                    <option value="hebdomadaire" {{ $type === 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                    <option value="mensuel" {{ $type === 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                </select>
            </div>

            @if($type === 'journalier')
                <div style="min-width:200px;">
                    <label style="display:block;font-size:12px;font-weight:800;color:var(--text-secondary);margin-bottom:6px;">
                        Date
                    </label>
                    <input type="date" name="date" value="{{ $start->toDateString() }}"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;background:#fff;outline:none;">
                </div>
            @endif

            @if($type === 'hebdomadaire')
                <div style="min-width:220px;">
                    <label style="display:block;font-size:12px;font-weight:800;color:var(--text-secondary);margin-bottom:6px;">
                        Semaine (choisir un jour)
                    </label>
                    <input type="date" name="week" value="{{ $start->toDateString() }}"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;background:#fff;outline:none;">
                </div>
            @endif

            @if($type === 'mensuel')
                <div style="min-width:220px;">
                    <label style="display:block;font-size:12px;font-weight:800;color:var(--text-secondary);margin-bottom:6px;">
                        Mois
                    </label>
                    <input type="month" name="month" value="{{ $start->format('Y-m') }}"
                           style="width:100%;padding:10px 12px;border:2px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;background:#fff;outline:none;">
                </div>
            @endif

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary" style="padding:9px 16px;">
                    <i class="bi bi-search"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);background:var(--bg-main);">
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Zone</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Planifié</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Réalisé</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Clients convertis</th>
                </tr>
            </thead>
            <tbody>
            @foreach($zones as $row)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-primary);font-weight:700;">
                        {{ $row['zone'] }}
                    </td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-secondary);font-weight:800;">
                        {{ $row['planned_visits'] }}
                    </td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--accent);font-weight:800;">
                        {{ $row['realized_visits'] }}
                    </td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--primary);font-weight:900;">
                        {{ $row['clients_created'] }}
                    </td>
                </tr>
            @endforeach

                <tr style="background:var(--bg-main);">
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-muted);font-weight:900;text-transform:uppercase;">Total</td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--text-secondary);font-weight:900;">{{ $totals['planned_visits'] }}</td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--accent);font-weight:900;">{{ $totals['realized_visits'] }}</td>
                    <td style="padding:14px 16px;font-size:13px;color:var(--primary);font-weight:900;">{{ $totals['clients_created'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

