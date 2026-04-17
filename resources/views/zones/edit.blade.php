@extends('layouts.app')

@section('title', 'Modifier ' . $zone->name)
@section('page_title', 'Zones')

@section('content')

<div style="max-width:640px;">

    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:20px;">
        <a href="{{ route('zones.index') }}" style="color:var(--primary);text-decoration:none;">Zones</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span>Modifier — {{ $zone->name }}</span>
    </div>

    <div class="card">
        <div class="card-header" style="padding:20px 24px 0;">
            <div>
                <h2 class="card-title">Modifier la zone</h2>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:4px;">{{ $zone->name }}</p>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-bottom:20px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul style="margin:0 0 0 8px;padding:0;list-style:none;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('zones.update', $zone) }}" id="zone-edit-form">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Nom de la zone <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $zone->name) }}"
                               style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'"
                               required>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Statut</label>
                        <select name="status" style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#fff;">
                            <option value="active" {{ old('status', $zone->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $zone->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Objectif pharmacies</label>
                        <input type="number" name="target_pharmacies" value="{{ old('target_pharmacies', $zone->target_pharmacies) }}" min="0"
                               style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Agent assigné</label>
                        <select name="agent_id" style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#fff;">
                            <option value="">— Aucun agent —</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id', $zone->agent_id) == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description</label>
                        <textarea name="description" rows="3"
                                  style="width:100%;padding:11px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description', $zone->description) }}</textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle-fill"></i> Mettre à jour
                    </button>
                    <a href="{{ route('zones.index') }}" class="btn btn-outline">
                        <i class="bi bi-x"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
