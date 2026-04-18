@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('page_title', 'Utilisateurs')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--text-primary);">Utilisateurs</h2>
        <p style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
            {{ $users->total() }} compte(s) — rôles et accès Spatie
        </p>
    </div>
    @can('manage users')
        <a href="{{ route('users.create') }}" class="btn btn-primary" style="padding:9px 16px;">
            <i class="bi bi-person-plus-fill"></i> Nouvel utilisateur
        </a>
    @endcan
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:220px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nom ou e-mail..."
                       style="width:100%;padding:9px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding:9px 16px;">
                <i class="bi bi-search"></i> Filtrer
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--bg-main);border-bottom:1px solid var(--border);">
                    <th style="text-align:left;padding:12px 20px;font-weight:600;color:var(--text-secondary);">Nom</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:600;color:var(--text-secondary);">E-mail</th>
                    <th style="text-align:left;padding:12px 16px;font-weight:600;color:var(--text-secondary);">Rôle</th>
                    @can('manage users')
                        <th style="text-align:right;padding:12px 20px;font-weight:600;color:var(--text-secondary);">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:14px 20px;font-weight:600;color:var(--text-primary);">{{ $user->name }}</td>
                        <td style="padding:14px 16px;color:var(--text-secondary);">{{ $user->email }}</td>
                        <td style="padding:14px 16px;">
                            <span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#e8f0fa;color:var(--primary);">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        @can('manage users')
                            <td style="padding:14px 20px;text-align:right;white-space:nowrap;">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline" style="padding:6px 12px;font-size:12px;">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                @if(! $user->is(auth()->user()))
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline" style="padding:6px 12px;font-size:12px;color:#dc2626;border-color:#fecaca;margin-left:6px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('manage users') ? 4 : 3 }}" style="padding:32px;text-align:center;color:var(--text-muted);">Aucun utilisateur.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
@endif

@endsection
