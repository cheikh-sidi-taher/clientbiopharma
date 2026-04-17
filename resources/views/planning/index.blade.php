@extends('layouts.app')

@section('title', 'Planning')
@section('page_title', 'Planning')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:6px;">Planning</h2>
        <p style="font-size:13px;color:var(--text-secondary);">
            Semaine du
            <strong style="color:var(--text-primary)">{{ $weekStart->locale('fr')->isoFormat('d MMMM YYYY') }}</strong>
            au
            <strong style="color:var(--text-primary)">{{ $weekEnd->locale('fr')->isoFormat('d MMMM YYYY') }}</strong>
        </p>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('planning.index', ['date' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-chevron-left"></i> Semaine précédente
        </a>
        <a href="{{ route('planning.index', ['date' => now()->toDateString()]) }}" class="btn btn-outline" style="padding:9px 14px;">
            <i class="bi bi-calendar3"></i> Aujourd'hui
        </a>
        <a href="{{ route('planning.index', ['date' => $weekStart->copy()->addWeek()->toDateString()]) }}" class="btn btn-outline" style="padding:9px 14px;">
            Semaine suivante <i class="bi bi-chevron-right"></i>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
        <i class="bi bi-exclamation-circle-fill"></i>
        <ul style="margin:0 0 0 18px;padding:0;">
            @foreach($errors->all() as $error)
                <li style="font-size:13px;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body" style="padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <div class="stat-card" style="padding:12px 16px;box-shadow:none;background:var(--bg-main);border:1px solid var(--border);">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                            Objectif
                        </div>
                        <div style="font-size:16px;font-weight:900;color:var(--text-primary);">
                            {{ $objectiveMin }} à {{ $objectiveMax }} pharmacies / jour
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
                    <span style="font-size:12px;color:var(--text-secondary);font-weight:700;">Sous objectif</span>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                    <span style="font-size:12px;color:var(--text-secondary);font-weight:700;">Au-dessus</span>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                    <span style="font-size:12px;color:var(--text-secondary);font-weight:700;">Dans l'objectif</span>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(7, 1fr);gap:12px;">
            @foreach($days as $day)
                @php
                    $count = $day['plannedCount'];
                    if ($count < $objectiveMin) {
                        $borderColor = '#dc2626';
                        $chipColor = '#dc2626';
                    } elseif ($count > $objectiveMax) {
                        $borderColor = '#f59e0b';
                        $chipColor = '#f59e0b';
                    } else {
                        $borderColor = '#10b981';
                        $chipColor = '#10b981';
                    }
                    $plannedIds = $plannedIdsMapByDate[$day['date']] ?? [];
                    $dayDateKey = $day['date'];
                @endphp

                <div style="border:2px solid {{ $borderColor }}; border-radius:12px; background:#fff; padding:12px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px;">
                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:900;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $day['label'] }}
                            </div>
                            <div style="font-size:12px;color:var(--text-muted);font-weight:800;margin-top:2px;">
                                {{ $day['display'] }}
                            </div>
                        </div>

                        <span style="padding:6px 10px;border-radius:20px;font-size:12px;font-weight:900;color:{{ $chipColor }};background:{{ $chipColor }}22;">
                            {{ $count }}
                        </span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @if($day['visits']->isEmpty())
                            <div style="font-size:12px;color:var(--text-secondary);font-style:italic;">
                                Aucune pharmacie planifiée
                            </div>
                        @else
                            @foreach($day['visits'] as $visit)
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;padding:10px;background:var(--bg-main);border:1px solid var(--border);border-radius:10px;">
                                    <div style="min-width:0;">
                                        <div style="font-size:13px;font-weight:800;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $visit->pharmacy->name }}
                                        </div>
                                        <div style="margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                            <span style="padding:2px 8px;border-radius:20px;font-size:10px;font-weight:900;background:var(--primary-light);color:var(--primary);">
                                                {{ $visit->pharmacy->zone->name ?? '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('planning.visits.destroy', $visit) }}" onsubmit="return confirm('Supprimer cette visite planifiée ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:18px;line-height:1;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div style="margin-top:12px;border-top:1px solid var(--border);padding-top:12px;">
                        <form method="POST" action="{{ route('planning.visits.store') }}">
                            @csrf
                            <input type="hidden" name="scheduled_date" value="{{ $dayDateKey }}">

                            <label style="display:block;font-size:11px;font-weight:900;color:var(--text-muted);margin-bottom:6px;letter-spacing:.02em;text-transform:uppercase;">
                                Ajouter une pharmacie
                            </label>

                            <div style="display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end;">
                                <div>
                                    <select name="pharmacy_id" required
                                            style="width:100%;padding:9px 10px;border:2px solid #e2e8f0;border-radius:10px;font-size:12px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
                                        <option value="">Choisir...</option>
                                        @foreach($pharmacies as $pharmacy)
                                            <option value="{{ $pharmacy->id }}" {{ isset($plannedIds[$pharmacy->id]) ? 'disabled' : '' }}>
                                                {{ $pharmacy->name }} ({{ $pharmacy->zone->name ?? '—' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary" style="padding:9px 12px;justify-content:center;">
                                    <i class="bi bi-plus-circle-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

